<?php
/**
 * Answers_tbl Class
 *
 *   回答内容テーブル
 *
 * @project     Hairlogy
 * @package     Table model
 * @author      yuki.hatano@gmail.com
 * @copyright   montecampo Co., Ltd. All Rights Reserved
 */

class Answers_tbl extends MY_Table {

    public function __construct()
    {
        parent::__construct();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_answer($reply_id)
    {
        $this->select([
            'answers.level',
            'answers.answer',
            'answers.sub_answer',
            'answers.question_id',
            'questions.number',
            'questions.question',
            'questions.type',
            'questions.choices',
            'questions.min_level',
            'questions.max_level',
        ]);
        $this->join('questions', 'questions.id = answers.question_id', 'left');
        $this->where('answers.reply_id', $reply_id);
        $this->order_by('questions.number');
        return $this->find();
    }

    // -----------------------------------------------------------------------------------------------------

    public function report_count_select_ones($salon_id, $stylist_id, $questionnaire_id, $since, $until=NULL)
    {
        $this->select([
            'questions.number',
            'answers.answer',
        ]);
        $this->select('count(answers.answer) AS count_answer', FALSE);
        $this->join('replies', 'replies.id = answers.reply_id', 'left');
        $this->join('questions', 'questions.id = answers.question_id', 'left');
        $this->join('questionnaires', 'questionnaires.id = questions.questionnaire_id', 'left');
        $this->where('questions.questionnaire_id', $questionnaire_id);
        $this->where('questions.type', 'select_one');
        $this->where('replies.stylist_id', $stylist_id);
        $this->where('replies.created_at >=', $since);
        $this->where_in('questionnaires.salon_id', ['0', $salon_id]);

        if ($until)
            $this->where('replies.created_at <=', $until);

        $group_by = ['answers.answer', 'answers.question_id'];
        $order_by = 'answers.question_id ASC';

        if ($this->find(NULL, $group_by, $order_by))
        {
            return $this->get_records();
        }

        return [];
    }

    // -----------------------------------------------------------------------------------------------------

    public function report_count_levels($salon_id, $stylist_id, $questionnaire_id, $since, $until=NULL)
    {
        $this->select([
            'questions.number',
            'answers.level',
            'replies.stylist_id',
        ]);
        $this->select('count(answers.level) AS count_level', FALSE);
        $this->join('replies', 'replies.id = answers.reply_id', 'left');
        $this->join('questions', 'questions.id = answers.question_id', 'left');
        $this->join('questionnaires', 'questionnaires.id = questions.questionnaire_id', 'left');
        $this->where('questions.questionnaire_id', $questionnaire_id);
        $this->where('questions.type', 'level');
        $this->where('replies.stylist_id', $stylist_id);
        $this->where('replies.created_at >=', $since);
        $this->where_in('questionnaires.salon_id', ['0', $salon_id]);

        if ($until)
            $this->where('replies.created_at <=', $until);

        $group_by = ['answers.level', 'answers.question_id'];
        $order_by = 'answers.question_id ASC';

        if ($this->find(NULL, $group_by, $order_by))
        {
            return $this->get_records();
        }

        return [];
    }

    // -----------------------------------------------------------------------------------------------------

    public function report_average_levels($salon_id, $stylist_id, $questionnaire_id, $since, $until=NULL, $is_daily=TRUE)
    {
        $this->select([
            'questions.number',
            'questions.item',
            'questions.nps_flag',
            'questions.min_level',
            'questions.max_level',
            'replies.stylist_id',
        ]);
        $this->select('avg(answers.level) AS average_level', FALSE);

        if ($is_daily)
        {
            $this->select('DATE(replies.created_at) AS daily', FALSE);
        }
        else
        {
            $this->select('YEAR(replies.created_at) AS yyyy', FALSE);
            $this->select('MONTH(replies.created_at) AS mm', FALSE);
        }

        $this->join('replies', 'replies.id = answers.reply_id', 'left');
        $this->join('questions', 'questions.id = answers.question_id', 'left');
        $this->join('questionnaires', 'questionnaires.id = questions.questionnaire_id', 'left');
        $this->where('questions.type', 'level');
        //$this->where('questions.nps_correlation_flag', '1');
        $this->where('questions.questionnaire_id', $questionnaire_id);
        $this->where('replies.stylist_id', $stylist_id);
        $this->where('replies.created_at >=', $since);

        if ($until)
            $this->where('replies.created_at <=', $until);

        $this->where_in('questionnaires.salon_id', ['0', $salon_id]);

        $group_by = ['answers.question_id'];

        if ($is_daily)
        {
            $group_by[] = 'daily';
        }
        else
        {
            $group_by[] = 'yyyy';
            $group_by[] = 'mm';
        }

        $order_by = 'answers.question_id ASC';

        if ($this->find(NULL, $group_by, $order_by))
        {
            return $this->get_records();
        }

        return [];
    }

    public function report_nps_levels($salon_id, $stylist_id, $questionnaire_id, $since, $until=NULL)
    {
        $this->select([
            'questions.number',
            'questions.item',
            'questions.nps_flag',
        ]);
        $this->select('avg(answers.level) AS average', FALSE);

        $this->join('replies', 'replies.id = answers.reply_id', 'left');
        $this->join('questions', 'questions.id = answers.question_id', 'left');
        $this->join('questionnaires', 'questionnaires.id = questions.questionnaire_id', 'left');
        $this->where('questions.type', 'level');
        $this->where('questions.questionnaire_id', $questionnaire_id);
        $this->where('replies.stylist_id', $stylist_id);
        $this->where('replies.created_at >=', $since);
        $this->where_in('questionnaires.salon_id', ['0', $salon_id]);

        if ($until)
            $this->where('replies.created_at <=', $until);

        $group_by = ['answers.question_id'];

        if ($averages = $this->find(NULL, $group_by))
        {
            /* 1. それぞれの変数の平均値を求める */
            $averages = array_column($averages, NULL, 'number');
            log_debug($averages);

            $this->select([
                'answers.reply_id',
                'answers.level',
                'questions.number',
                'questions.nps_flag',
            ]);

            $this->join('replies', 'replies.id = answers.reply_id', 'left');
            $this->join('questions', 'questions.id = answers.question_id', 'left');
            $this->where('questions.type', 'level');
            $this->where('questions.questionnaire_id', $questionnaire_id);
            $this->where('replies.stylist_id', $stylist_id);
            $this->where('replies.created_at >=', $since);

            if ($until)
                $this->where('replies.created_at <=', $until);

            if ($records = $this->find())
            {
                $replies = [];
                $deviation = 0;

                /* 2. それぞれの変数の偏差（数値 － 平均値）を求める */
                foreach ($records as &$row)
                {
                    if (isset($averages[$row['number']]))
                    {
                        if ( ! isset($averages[$row['number']]['count']))
                            $averages[$row['number']]['count'] = 1;
                        else
                            $averages[$row['number']]['count']++;

                        if ( ! isset($averages[$row['number']]['dispersion']))
                            $averages[$row['number']]['dispersion'] = 0;

                        if ( ! isset($averages[$row['number']]['covariance']))
                            $averages[$row['number']]['covariance'] = 0;

                        $average = $averages[$row['number']]['average']; // 平均
                        $deviation = $row['level'] - $average; // 偏差 (値 - 平均)
                        $averages[$row['number']]['dispersion'] += $deviation ** 2; // 偏差の2乗を加算

                        if ( ! empty($row['nps_flag']))
                        {
                            $replies[$row['reply_id']]['nps_deviation']['deviation']= $deviation;
                        }
                        else
                        {
                            $replies[$row['reply_id']][$row['number']]['deviation'] = $deviation;
                        }
                    }
                }

                /* 3. それぞれの変数の分散（偏差の二乗平均）を求める */
                /* 4. それぞれの変数の標準偏差（分散の正の平方根）を求める */
                foreach ($averages as $number => &$average)
                {
                    log_debug("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@");
                    if (empty($average['nps_flag']))
                    {
                        // 標準偏差
                        $average['standard_deviation'] = sqrt($average['dispersion'] / $average['count']);
                        log_debug("number: {$number}, standard_deviation: {$average['standard_deviation']}");
                    }
                    else
                    {
                        // NPSの標準偏差
                        $nps_standard_deviation = sqrt($average['dispersion'] / $average['count']);
                        log_debug("nps_standard_deviation: {$nps_standard_deviation}");
                    }
                    $average['product'] = 0;
                }

                /* 5. 共分散（偏差の積の平均）を求める NPS偏差と対象偏差の積 */
                foreach ($replies as $reply_id => $_replies)
                {
                    foreach ($_replies as $number => $reply)
                    {
                        if ($number != 'nps_deviation')
                            $averages[$number]['product'] += $_replies['nps_deviation']['deviation'] * $reply['deviation']; // 偏差の積を加算
                    }
                }

                foreach ($averages as $number => &$average)
                {
                    $average['correlation'] = 0;

                    if (empty($average['nps_flag']))
                    {
                        log_debug("=========================================");
                        log_debug("number: {$number}, count: {$average['count']}, average: {$average['average']}, standard_deviation: {$average['standard_deviation']}, product: {$average['product']}");
                        // 共分散 covariance
                        $covariance = $average['product'] / $average['count'];
                        log_debug("covariance: {$covariance}");
                        log_debug("standard_deviation: {$average['standard_deviation']}");

                        if ($standard_deviation = $nps_standard_deviation * $average['standard_deviation'])
                        {
                            log_debug("standard_deviation: {$standard_deviation}");
                            $correlation = $covariance / $standard_deviation;
                            log_debug("correlation: {$correlation}");
                            $average['correlation'] = $correlation;
                        }
                        unset($average['dispersion'], $average['covariance'], $average['deviation'], $average['standard_deviation']);
                        log_debug("=================================================");
                    }
                }
            }

            return $averages;
        }

        return [];
    }

    function get_nps_count($salon_id, $stylist_id, $questionnaire_id, $since, $until)
    {
        $nps = [
            'promoter'  => 0,
            'passive'   => 0,
            'detractor' => 0,
        ];
        $this->select('count(answers.level) AS promoter', FALSE);

        $this->join('replies', 'replies.id = answers.reply_id', 'left');
        $this->join('questions', 'questions.id = answers.question_id', 'left');
        $this->join('questionnaires', 'questionnaires.id = questions.questionnaire_id', 'left');
        $this->where('answers.level >', '8');
        $this->where('questions.type', 'level');
        $this->where('questions.nps_flag', '1');
        $this->where('questions.questionnaire_id', $questionnaire_id);
        $this->where('replies.stylist_id', $stylist_id);
        $this->where('replies.created_at <=', $until);
        $this->where('replies.created_at >=', $since);
        $this->where_in('questionnaires.salon_id', ['0', $salon_id]);

        if ($this->find())
        {
            $nps['promoter'] = $this->get('promoter');
        }

        $this->select('count(answers.level) AS passive', FALSE);

        $this->join('replies', 'replies.id = answers.reply_id', 'left');
        $this->join('questions', 'questions.id = answers.question_id', 'left');
        $this->join('questionnaires', 'questionnaires.id = questions.questionnaire_id', 'left');
        $this->where('answers.level <', '9');
        $this->where('answers.level >', '6');
        $this->where('questions.type', 'level');
        $this->where('questions.nps_flag', '1');
        $this->where('questions.questionnaire_id', $questionnaire_id);
        $this->where('replies.stylist_id', $stylist_id);
        $this->where('replies.created_at <=', $until);
        $this->where('replies.created_at >=', $since);
        $this->where_in('questionnaires.salon_id', ['0', $salon_id]);

        if ($this->find())
        {
            $nps['passive'] = $this->get('passive');
        }

        $this->select('count(answers.level) AS detractor', FALSE);

        $this->join('replies', 'replies.id = answers.reply_id', 'left');
        $this->join('questions', 'questions.id = answers.question_id', 'left');
        $this->join('questionnaires', 'questionnaires.id = questions.questionnaire_id', 'left');
        $this->where('answers.level <', '7');
        $this->where('questions.type', 'level');
        $this->where('questions.nps_flag', '1');
        $this->where('questions.questionnaire_id', $questionnaire_id);
        $this->where('replies.stylist_id', $stylist_id);
        $this->where('replies.created_at <=', $until);
        $this->where('replies.created_at >=', $since);
        $this->where_in('questionnaires.salon_id', ['0', $salon_id]);

        if ($this->find())
        {
            $nps['detractor'] = $this->get('detractor');
        }

        return $nps;
    }

    public function get_sub_answers($questionnaire_id, $stylist_id, $question_id, $since, $until)
    {
        $this->select([ 'answers.reply_id', 'answers.sub_answer', 'answers.level', 'answers.created_at' ]);
        $this->join('replies', 'replies.id = answers.reply_id');
        $this->join('questions', 'questions.id = answers.question_id');
        $this->where('answers.question_id', $question_id);
        $this->where('replies.questionnaire_id', $questionnaire_id);
        $this->where('replies.stylist_id', $stylist_id);
        $this->where('replies.created_at >=', $since);
        $this->where('replies.created_at <=', $until);
        $this->where('questions.type', 'level');
        /*
        $this->order_by('answers.level DESC');
        $this->order_by('answers.created_at DESC');
        */
        return $this->find();
    }

}

