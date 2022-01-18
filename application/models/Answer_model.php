<?php

class Answer_model extends MY_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tables/replies_tbl');
        $this->load->model('tables/answers_tbl');
        $this->load->model('tables/question_selections_tbl');
    }

    public function create_reply($questionnaire_stylist, $answer, $created_at=NULL)
    {
        log_debug("Answer_model.create_reply() run.");
        log_debug($questionnaire_stylist);
        log_debug($answer);
        log_debug($this->_user_agent);
        log_debug($this->_browser);

        $this->answers_tbl->initialize(
            $this->replies_tbl->initialize('master')
        );
        $this->replies_tbl->trans_start();

        $reply = [
            'stylist_id'        => $questionnaire_stylist['stylist_id'],
            'questionnaire_id'  => $questionnaire_stylist['questionnaire_id'],
            'browser'           => $this->_browser,
            'user_agent'        => $this->_user_agent,
        ];

        if ($created_at !== NULL)
        {
            $reply['updated_at'] = $created_at;
            $reply['created_at'] = $created_at;
        }

        if ($reply_id = $this->replies_tbl->insert($reply))
        {
            $answers = [];

            if (isset($answer['level']))
            {
                foreach ($answer['level'] as $question_id => $level)
                {
                    $_answer = [
                        'reply_id'      => $reply_id,
                        'question_id'   => $question_id,
                        'level'         => $level,
                        'answer'        => NULL,
                        'sub_answer'    => NULL,
                    ];

                    if ($created_at !== NULL)
                    {
                        $_answer['updated_at'] = $created_at;
                        $_answer['created_at'] = $created_at;
                    }

                    if (isset($answer['sub_answer'][$question_id]))
                    {
                        $_answer['sub_answer'] = $answer['sub_answer'][$question_id];
                    }

                    $answers[] = $_answer;
                }
            }

            if (isset($answer['select_one']))
            {
                foreach ($answer['select_one'] as $question_id => $select_one)
                {
                    $_answer = [
                        'reply_id'      => $reply_id,
                        'question_id'   => $question_id,
                        'level'         => NULL,
                        'answer'        => $select_one,
                        'sub_answer'    => NULL,
                    ];

                    if ($created_at !== NULL)
                    {
                        $_answer['updated_at'] = $created_at;
                        $_answer['created_at'] = $created_at;
                    }

                    if (isset($answer['sub_answer'][$question_id]))
                    {
                        $_answer['sub_answer'] = $answer['sub_answer'][$question_id];
                    }

                    $answers[] = $_answer;
                }
            }

            // TODO 未完成
            if (isset($answer['select_multi']))
            {
                foreach ($answer['select_multi'] as $question_id => $select_multi)
                {
                    $_answer = [
                        'reply_id'      => $reply_id,
                        'question_id'   => $question_id,
                        'level'         => NULL,
                        'answer'        => NULL,
                        'sub_answer'    => NULL,
                    ];

                    if ($created_at !== NULL)
                    {
                        $_answer['updated_at'] = $created_at;
                        $_answer['created_at'] = $created_at;
                    }

                    if (isset($answer['sub_answer'][$question_id]))
                    {
                        $_answer['sub_answer'] = $answer['sub_answer'][$question_id];
                    }

                    $answers[] = $_answer;
                }
            }

            if (isset($answer['text']))
            {
                foreach ($answer['text'] as $question_id => $text)
                {
                    $_answer = [
                        'reply_id'      => $reply_id,
                        'question_id'   => $question_id,
                        'level'         => NULL,
                        'answer'        => $text,
                        'sub_answer'    => NULL,
                    ];

                    if ($created_at !== NULL)
                    {
                        $_answer['updated_at'] = $created_at;
                        $_answer['created_at'] = $created_at;
                    }

                    if (isset($answer['sub_answer'][$question_id]))
                    {
                        $_answer['sub_answer'] = $answer['sub_answer'][$question_id];
                    }

                    $answers[] = $_answer;
                }
            }

            if ($answers)
            {
                $this->answers_tbl->insert_batch($answers);
                return $this->replies_tbl->trans_complete();
            }
        }

        $this->replies_tbl->trans_rollback();
        return FALSE;

    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function get_replies($stylist_id, $offset)
    {
        //$stylist_id = 5;
        log_debug("Answer_model.get_replies({$stylist_id}, {$offset}) run.");

        $limit = 20;
        $this->sanitize_offset($offset);
        $this->sanitize_limit($limit);

        $this->replies_tbl->initialize('master');
        $this->replies_tbl->init_pagination($offset, $limit, "/ajax/reply/get_replies/{$stylist_id}/", 5);

        $this->replies_tbl->where('stylist_id', $stylist_id);
        $order_by = 'created_at DESC';

        if ($this->replies_tbl->find(NULL, NULL, $order_by))
        {
            $this->pagination($this->replies_tbl->load_pagination());
            return $this->replies_tbl->get_records();
        }

        return NULL;
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function get_answer($reply_id)
    {
        log_debug("Answer_model.get_answer({$reply_id}) run.");

        $this->answers_tbl->initialize($this->question_selections_tbl->initialize('master'));

        if ($answer = $this->answers_tbl->get_answer($reply_id))
        foreach ($answer as &$ans)
        {
            $ans['selections'] = $this->question_selections_tbl->get_selections($ans['question_id']);
        }

        return $answer;
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function report_count_answers($questions, $salon_id, $stylist_id, $questionnaire_id, $since, $until=NULL)
    {
        log_debug("Answer_model.report_count_answers(questions, {$salon_id}, {$stylist_id}, {$questionnaire_id}, {$since}, {$until}) run.");
        $count_levels = $count_select_ones = $color_select_ones = [];

        if ($questions)
        {
            foreach ($questions as $question)
            {
                //log_debug("question:::::::::::::::::::::::::::::::::");
                //log_debug($question);
                if ($question['type'] == 'level')
                {
                    for ($i=$question['min_level']; $i<=$question['max_level']; $i++)
                    {
                        $count_levels[$question['number']][$i] = 0;
                    }
                }
                elseif ($question['type'] == 'select_one')
                {
                    foreach ($question['selections'] as $i => $selection)
                    {
                        $count_select_ones[$question['number']][$selection['selection']] = 0;
                        $color_select_ones[$question['number']][$i] = $selection['color'];
                    }
                }
            }

            $this->answers_tbl->initialize('master');

            if ($levels = $this->answers_tbl->report_count_levels($salon_id, $stylist_id, $questionnaire_id, $since, $until))
            {
                foreach ($levels as $level)
                {
                    $count_levels[$level['number']][$level['level']] = (int)$level['count_level'];
                }

            }

            if ($select_ones = $this->answers_tbl->report_count_select_ones($salon_id, $stylist_id, $questionnaire_id, $since, $until))
            {
                foreach ($select_ones as $select_one)
                {
                    $count_select_ones[$select_one['number']][$select_one['answer']] = (int)$select_one['count_answer'];
                }
            }
            //log_debug("====================================");
            //log_debug($count_levels);
            //log_debug("====================================");
            //log_debug($count_select_ones);
            //log_debug("====================================");
        }

        return [
            'count_levels'      => $count_levels,
            'count_select_ones' => $count_select_ones,
            'color_select_ones' => $color_select_ones,
        ];
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function report_nps_levels($questions, $salon_id, $stylist_id, $questionnaire_id, $since, $until=NULL)
    {
        log_debug("Answer_model.report_nps_levels(questions, {$salon_id}, {$stylist_id}, {$questionnaire_id}, {$since}, {$until}) run.");
        $this->answers_tbl->initialize('master');

        $nps_levels = [];

        if ($levels = $this->answers_tbl->report_nps_levels($salon_id, $stylist_id, $questionnaire_id, $since, $until))
        {
            foreach ($levels as $level)
            {
                //log_debug($level);
                $nps_levels[$level['number']]['average']     = $level['average'];
                $nps_levels[$level['number']]['correlation'] = $level['correlation'];
                $nps_levels[$level['number']]['nps_flag']    = $level['nps_flag'];
                $nps_levels[$level['number']]['item']        = $level['item'];
            }
        }

        return $nps_levels;
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function report_average_levels($questions, $salon_id, $stylist_id, $questionnaire_id, $since, $until=NULL, $is_daily=TRUE)
    {
        log_debug("Answer_model.report_average_levels(questions, {$salon_id}, {$stylist_id}, {$questionnaire_id}, {$since}, {$until}, {$is_daily}) run.");
        if ($until === NULL)
        {
            $until = date('Y-m-d 23:59:59');
        }

        $average_levels = $nps_levels = [];

        $today = date('Y-m-d');
        $this_month = date('Y-m');
        $max_nps = NULL;
        $total_average = $total_correlation = 0;
        $count_nps = 0;
        $nps_coefficient = [];

        if ($questions)
        {
            foreach ($questions as $question)
            {
                $question_number = $question['number'];

                if ($question['type'] == 'level')
                {
                    if ($is_daily)
                    {
                        $since_date = date('Y-m-d', strtotime($since));

                        while ($today >= $since_date)
                        {
                            $since_day  = date('n/j', strtotime($since_date));
                            $average_levels[$question_number][$since_day] = 0;
                            $since_date = date('Y-m-d', strtotime("+1 day {$since_date}"));
                        }
                    }
                    else
                    {
                        $since_month = date('Y-m', strtotime($since));

                        while ($this_month >= $since_month)
                        {
                            $m = date('Y-n', strtotime($since_month));
                            $average_levels[$question_number][$m] = 0;
                            $since_month = date('Y-m', strtotime("+1 month {$since_month}-01"));
                        }
                    }
                }
            }

            $this->answers_tbl->initialize('master');

            if ($levels = $this->answers_tbl->report_average_levels($salon_id, $stylist_id, $questionnaire_id, $since, $until, $is_daily))
            {
                foreach ($levels as $level)
                {
                    if ($is_daily)
                    {
                        $day = date('n/j', strtotime($level['daily']));

                        if (isset($average_levels[$level['number']][$day]))
                            $average_levels[$level['number']][$day] = (float)$level['average_level'];
                    }
                    elseif ( ! $is_daily && isset($average_levels[$level['number']]["{$level['yyyy']}-{$level['mm']}"]))
                    {
                        $average_levels[$level['number']]["{$level['yyyy']}-{$level['mm']}"] = (float)$level['average_level'];
                    }
                }
            }

            $nps_questions_count = 0;

            if ($levels = $this->answers_tbl->report_nps_levels($salon_id, $stylist_id, $questionnaire_id, $since, $until))
            {
                $nps_coefficient = [];

                foreach ($levels as $level)
                {
                    //log_debug('% % % % % % % % %');
                    //log_debug($level);
                    $nps_levels[$level['number']]['average']     = $level['average'];
                    $nps_levels[$level['number']]['correlation'] = $level['correlation'];
                    $nps_levels[$level['number']]['nps_flag']    = $level['nps_flag'];
                    $nps_levels[$level['number']]['item']        = $level['item'];

                    if (empty($level['nps_flag']))
                    {
                        if ($level['correlation'] >= 0.2 && $level['count'] >= 50)
                        {
                            $total_correlation += $level['correlation'];
                            $total_average     += $level['average'];
                            $nps_coefficient[$level['number']]['average'] = $level['average'];;
                            $nps_coefficient[$level['number']]['correlation'] = $level['correlation'];
                            $nps_coefficient[$level['number']]['item'] = $level['item'];
                        }
                        $nps_questions_count++;
                    }
                }

                $nps_count = $this->answers_tbl->get_nps_count($salon_id, $stylist_id, $questionnaire_id, $since, $until);
                /*
                log_debug("%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%");
                log_debug($since);
                log_debug($until);
                log_debug($nps_count);
                log_debug($levels);
                log_debug($nps_coefficient);
                log_debug($nps_questions_count / 2);
                log_debug("%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%");
                */

                if (array_sum($nps_count) && count($nps_coefficient) > ($nps_questions_count / 2))
                foreach ($nps_coefficient as $number => &$value)
                {
                    log_debug("total_average: {$total_average}");
                    log_debug("total_correlation: {$total_correlation}");
                    $value['coefficient'] = ($value['average'] / $total_average) * ($value['correlation'] / $total_correlation);

                    if ($max_nps === NULL)
                    {
                        $max_nps = [
                            'item' => $value['item'],
                            'coefficient' => $value['coefficient'],
                        ];
                    }
                    elseif ($value['coefficient'] > $max_nps['coefficient'])
                    {
                        $max_nps = [
                            'item' => $value['item'],
                            'coefficient' => $value['coefficient'],
                        ];
                    }
                    elseif ($value['coefficient'] == $max_nps['coefficient'])
                    {
                        $max_nps['item'] .= ", {$value['item']}";
                    }
                }
            }
            //log_debug($average_levels);
        }

        return ['average_levels' => $average_levels, 'nps_levels' => $nps_levels, 'max_nps' => $max_nps, 'nps_count' => array_sum($nps_count)];
    }

    public function nps_transitions($salon_id, $stylist_id, $questionnaire_id)
    {
        log_debug("Answer_model.nps_transitions({$salon_id}, {$stylist_id}, {$questionnaire_id}, {$term}) run.");
        $this->answers_tbl->initialize('master');
        $since = date('Y-m-01 00:00:00');

        for ($i=1; $i<=6; $i++)
        {
            $since = date('Y-m-01 00:00:00', strtotime("{$since} -1 month"));
            $until = date('Y-m-t 23:59:59', strtotime($since));
            $m     = date('n月', strtotime($since));

            $nps_count = $this->answers_tbl->get_nps_count($salon_id, $stylist_id, $questionnaire_id, $since, $until);
            $max_question = NULL;
            $max_nps = NULL;
            $total_average = $total_correlation = 0;
            $nps_coefficient = [];

            if ($average_levels = $this->answers_tbl->report_average_levels($salon_id, $stylist_id, $questionnaire_id, $since, $until, FALSE))
            foreach ($average_levels as $average_level)
            {
                if (empty($average_level['nps_flag']))
                {
                    if ($max_question === NULL)
                    {
                        $max_question = [
                            'item'          => $average_level['item'],
                            'average_level' => $average_level['average_level'],
                        ];
                    }
                    elseif ($average_level['average_level'] > $max_question['average_level'])
                    {
                        $max_question = [
                            'item'          => $average_level['item'],
                            'average_level' => $average_level['average_level'],
                        ];
                    }
                    elseif ($average_level['average_level'] == $max_question['average_level'])
                    {
                        $max_question['item'] .= "、{$average_level['item']}";
                    }
                }
            }

            if (array_sum($nps_count) > 50)
            {
                if ($nps_levels = $this->answers_tbl->report_nps_levels($salon_id, $stylist_id, $questionnaire_id, $since, $until))
                {
                    foreach ($nps_levels as $nps_level)
                    {
                        if (empty($nps_level['nps_flag']) && $nps_level['correlation'] >= 0.2)
                        {
                            $total_correlation += $nps_level['correlation'];
                            $total_average     += $nps_level['average'];
                            $nps_coefficient[$nps_level['number']]['average'] = $nps_level['average'];;
                            $nps_coefficient[$nps_level['number']]['correlation'] = $nps_level['correlation'];
                            $nps_coefficient[$nps_level['number']]['item'] = $nps_level['item'];
                        }
                    }
                }

                foreach ($nps_coefficient as $number => &$value)
                {
                    $value['coefficient'] = ($value['average'] / $total_average) * ($value['correlation'] / $total_correlation);

                    if ($max_nps === NULL)
                    {
                        $max_nps = [
                            'item' => $value['item'],
                            'coefficient' => $value['coefficient'],
                        ];
                    }
                    elseif ($value['coefficient'] > $max_nps['coefficient'])
                    {
                        $max_nps = [
                            'item' => $value['item'],
                            'coefficient' => $value['coefficient'],
                        ];
                    }
                    elseif ($value['coefficient'] == $max_nps['coefficient'])
                    {
                        $max_nps['item'] .= ", {$value['item']}";
                    }
                }
                /*
                log_debug("****************************************");
                log_debug($nps_coefficient);
                log_debug("****************************************");
                */
            }

            $nps[$i] = [
                'promoter'  => 0,
                'passive'   => 0,
                'detractor' => 0,
                'nps'       => 0,
                'since'     => $since,
                'until'     => $until,
                'month'     => $m,
                'max_question' => $max_question,
                'max_nps'   => $max_nps,
                'bar_chart' => -100,
            ];

            if ($sum = array_sum($nps_count))
            {
                foreach ($nps_count as $k => $v)
                {
                    $nps[$i][$k] = round(($v / $sum) * 100, 1);
                }

                $nps[$i]['nps'] = round($nps[$i]['promoter'] - $nps[$i]['detractor'], 1);
            }
        }

        return $nps;
    }

    public function get_sub_answers($questionnaire_id, $stylist_id, $question_id, $since, $until)
    {
        log_debug("Answer_model.get_sub_answers({$questionnaire_id}, {$stylist_id}, {$question_id}, {$since}, {$until}) run.");
        $this->answers_tbl->initialize('master');

        $until = isset($until) ? $until : date('Y-m-d 23:59:59');

        $sub_answers = $major_sub_answers = $minor_sub_answers = [];

        if ($_sub_answers = $this->answers_tbl->get_sub_answers($questionnaire_id, $stylist_id, $question_id, $since, $until))
        {
            shuffle($_sub_answers);

            foreach ($_sub_answers as $sub_answer)
            {
                //log_debug($sub_answer);
                if ($sub_answer['level'] > 8)
                {
                    $major_sub_answers[] = $sub_answer;
                }
                else
                {
                    $minor_sub_answers[] = $sub_answer;
                }
            }
        }

        if ($major_sub_answers)
            return $major_sub_answers;
        else
            return $minor_sub_answers;
    }

}

