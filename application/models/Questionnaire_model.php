<?php

class Questionnaire_model extends MY_Model {

    private $_db = 'master';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tables/questionnaires_tbl');
        $this->load->model('tables/questions_tbl');
        $this->load->model('tables/question_selections_tbl');
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_questionnaires($salon_id, $offset=NULL, $search=NULL)
    {
        log_debug("Questionnaire_model.get_questionnaires({$salon_id}) run.");
        $this->questionnaires_tbl->initialize('master');

        if ($offset !== NULL)
        {
            $limit = '10';
            $this->sanitize_offset($offset);
            $this->sanitize_limit($limit);
            $this->questionnaires_tbl->init_pagination($offset, $limit);
        }

        $this->questionnaires_tbl->where('salon_id', $salon_id);
        $this->questionnaires_tbl->where('deleted_flag', '0');

        if (isset($search['status']))
        {
            if (empty($search['status']))
                $this->questionnaires_tbl->where('status', '0');
            else
                $this->questionnaires_tbl->where('status', '1');
        }

        return $this->questionnaires_tbl->find();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_questionnaire($salon_id, $questionnaire_id, $with_questions=FALSE)
    {
        log_debug("Questionnaire_model.get_questionnaire({$salon_id}, {$questionnaire_id}, {$with_questions}) run.");
        $this->questionnaires_tbl->initialize('master');
        $this->questionnaires_tbl->where('id', $questionnaire_id);
        $this->questionnaires_tbl->where('salon_id', $salon_id);
        $this->questionnaires_tbl->where('deleted_flag', '0');

        $questionnaire = [];

        if ($this->questionnaires_tbl->find())
        {
            $questionnaire = $this->questionnaires_tbl->get_row();

            if ($with_questions === TRUE && $questions = $this->get_questions($questionnaire_id))
            {
                $questionnaire['questions'] = $questions;
            }
        }
        
        return $questionnaire;
    }

    // -----------------------------------------------------------------------------------------------------

    public function create_questionnaire($salon_id, $questionnaire)
    {
        log_debug("Questionnaire_model.create_questionnaire({$salon_id}, ) run.");
        $this->question_selections_tbl->initialize(
            $this->questions_tbl->initialize(
                $this->questionnaires_tbl->initialize('master')
            )
        );
        $this->questionnaires_tbl->trans_start();

        $insert_data = [
            'title' => $questionnaire['title'],
            'type'  => $questionnaire['type'],
            'note'  => isset($questionnaire['note']) ? $questionnaire['note'] : '',
        ];

        if (isset($questionnaire['remove'])) foreach ($questionnaire['remove'] as $question_id => $trash)
        {
            log_debug("REMOVE QUESTION=================");
            log_debug($questionnaire['remove']);
            unset($questionnaire['questions'][$question_id]);
        }

        if ($questionnaire_id = $this->questionnaires_tbl->insert($insert_data))
        {
            $number = 0;

            foreach ($questionnaire['questions'] as $question_id => $question)
            {
                if ( ! $this->_set_question($questionnaire_id, $questionnaire, $question_id, $question, $number))
                {
                    $this->questionnaires_tbl->trans_rollback();
                    return FALSE;
                }

                $number++;
            }

            if ($this->questionnaires_tbl->trans_complete())
            {
                return $questionnaire_id;
            }
        }

        $this->questionnaires_tbl->trans_rollback();
        return FALSE;
    }

    // -----------------------------------------------------------------------------------------------------

    public function update_questionnaire($salon_id, $questionnaire_id, $questionnaire)
    {
        log_debug("Questionnaire_model.update_questionnaire({$salon_id}, {$questionnaire_id}) run.");
        $this->question_selections_tbl->initialize(
            $this->questions_tbl->initialize(
                $this->questionnaires_tbl->initialize('master')
            )
        );
        $this->questionnaires_tbl->trans_start();

        $update_data = [
            'title' => $questionnaire['title'],
            'type'  => $questionnaire['type'],
            'note'  => isset($questionnaire['note']) ? $questionnaire['note'] : '',
        ];

        $cond = [
            'id'        => $questionnaire_id,
            'salon_id'  => $salon_id,
        ];

        if ($this->questionnaires_tbl->update($update_data, $cond) !== FALSE)
        {
            if (isset($questionnaire['remove'])) foreach ($questionnaire['remove'] as $question_id => $trash)
            {
                log_debug("REMOVE QUESTION=================");
                log_debug($questionnaire['remove']);
                unset($questionnaire['questions'][$question_id]);
                $this->questions_tbl->delete([
                    'id'               => $question_id,
                    'questionnaire_id' => $questionnaire_id,
                ]);
                $this->question_selections_tbl->delete(['question_id' => $question_id]);
            }

            $this->questions_tbl->update(['nps_flag' => NULL], ['questionnaire_id' => $questionnaire_id]);

            $number = 0;

            foreach ($questionnaire['questions'] as $question_id => $question)
            {
                if ( ! $this->_set_question($questionnaire_id, $questionnaire, $question_id, $question, $number))
                {
                    $this->questionnaires_tbl->trans_rollback();
                    return FALSE;
                }

                $number++;
            }

            return $this->questionnaires_tbl->trans_complete();
        }

        $this->questionnaires_tbl->trans_rollback();
        return FALSE;
    }

    // -----------------------------------------------------------------------------------------------------

    public function update_questionnaire_status($salon_id, $questionnaire_id, $status)
    {
        log_debug("Questionnaire_model.update_questionnaire_status({$salon_id}, {$questionnaire_id}, {$status}) run.");
        $this->questionnaires_tbl->initialize('master');

        if (empty($status)) 
            $new_status = '0';
        else
            $new_status = '1';
        
        $update_data = ['status' => $new_status];
        $cond = ['id' => $questionnaire_id, 'salon_id' => $salon_id, 'deleted_flag' => '0'];

        return ($this->questionnaires_tbl->update($update_data, $cond) !== FALSE);
    }

    // -----------------------------------------------------------------------------------------------------

    public function delete_questionnaire($salon_id, $questionnaire_id)
    {
        log_debug("Questionnaire_model.delete_questionnaire({$salon_id}, {$questionnaire_id}) run.");
        $this->load->model('tables/salon_questionnaires_tbl');
        $this->load->model('tables/questionnaire_stylists_tbl');
        $this->questionnaire_stylists_tbl->initialize(
            $this->salon_questionnaires_tbl->initialize(
                $this->questionnaires_tbl->initialize('master')
            )
        );

        $this->questionnaires_tbl->trans_start();

        $cond = [
            'id'        => $questionnaire_id,
            'salon_id'  => $salon_id,
            'status'    => '0',
        ];

        if ($this->questionnaires_tbl->update(['deleted_flag' => '1'], $cond))
        {
            $this->questionnaire_stylists_tbl->delete(['questionnaire_id' => $questionnaire_id]);
            $this->salon_questionnaires_tbl->delete(['questionnaire_id' => $questionnaire_id]);
            return $this->questionnaires_tbl->trans_complete();
        }

        $this->questionnaires_tbl->trans_rollback();
        return FALSE;
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_questions($questionnaire_id)
    {
        log_debug("Questionnaire_model.get_questions({$questionnaire_id}) run.");
        $this->question_selections_tbl->initialize($this->questions_tbl->initialize($this->_db));
        $this->questions_tbl->where('questionnaire_id', $questionnaire_id);
        $this->questions_tbl->order_by('number', 'ASC');

        if ($questions = $this->questions_tbl->find())
        foreach ($questions as &$question)
        {
            $question['selections'] = $this->question_selections_tbl->get_selections($question['id']);
            //$question['choices'] = json_encode($question['selections']);
            $question['has_sub_question'] = (empty($question['sub_question']) OR $question['type'] == 'message') ? '0' : '1';
            $question['message'] = $question['type'] == 'message' ? $question['sub_question'] : '';
        }

        return $questions;
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_question($salon_id, $questionnaire_id, $question_id)
    {
        log_debug("Questionnaire_model.get_question({$salon_id}, {$questionnaire_id}, {$question_id}) run.");
        $this->questions_tbl->initialize(
            $this->questionnaires_tbl->initialize('master')
        );
    }

    // -----------------------------------------------------------------------------------------------------

    public function update_question_status($salon_id, $questionnaire_id, $question_id, $status)
    {
        log_debug("Questionnaire_model.update_question_status({$salon_id}, {$questionnaire_id}, {$question_id}, {$status}) run.");
        $this->questions_tbl->initialize(
            $this->questionnaires_tbl->initialize('master')
        );
    }

    // -----------------------------------------------------------------------------------------------------

    public function delete_question($salon_id, $questionnaire_id, $question_id)
    {
        log_debug("Questionnaire_model.delete_question({$salon_id}, {$questionnaire_id}, {$question_id}) run.");
        $this->questions_tbl->initialize(
            $this->questionnaires_tbl->initialize('master')
        );
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_my_questionnaires($stylist_id, $salon_id, $with_total=FALSE)
    {
        log_debug("Questionnaire_model.get_my_questionnaires({$stylist_id}, {$salon_id}, {$with_total}) run.");
        $this->questionnaires_tbl->initialize('master');

        if ($questionnaires = $this->questionnaires_tbl->get_my_questionnaires($stylist_id, $salon_id, $with_total))
        {
            log_debug($questionnaires);
            foreach ($questionnaires as &$questionnaire)
            {
                if ($questionnaire['status']) 
                    $questionnaire['url'] = site_url() . "q/{$questionnaire['code']}";
                else
                    $questionnaire['url'] = NULL;
            }
        }
        
        return $questionnaires;
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_questionnaire_of_stylist($code, $with_questions=TRUE)
    {
        log_debug("Questionnaire_model.get_questionnaire_of_stylist({$code}) run.");
        $this->load->model('tables/questionnaire_stylists_tbl');
        $this->_db = $this->questions_tbl->initialize(
            $this->questionnaire_stylists_tbl->initialize('master')
        );

        if ($questionnaire = $this->questionnaire_stylists_tbl->get_questionnaire_of_stylist($code))
        {
            if ($with_questions === TRUE)
            {
                $questions = [];

                if ($questionnaire['status'])
                {
                    $questions = $this->get_questions($questionnaire['id']);
                }

                return [
                    'questionnaire_id'      => $questionnaire['id'],
                    'salon_id'              => $questionnaire['salon_id'],
                    'stylist_id'            => $questionnaire['stylist_id'],
                    'stylist_salon_id'      => $questionnaire['stylist_salon_id'],
                    'questions'             => $questions,
                ];
            }
            else
            {
                return [
                    'questionnaire_id'      => $questionnaire['id'],
                    'salon_id'              => $questionnaire['salon_id'],
                    'stylist_id'            => $questionnaire['stylist_id'],
                    'stylist_salon_id'      => $questionnaire['stylist_salon_id'],
                ];
            }
        }

        return NULL;
    }

    // =======================================================================================================================

    private function _validate_question($question, $number)
    {
        if (mb_strlen($question['question']) === 0 OR  mb_strlen($question['question']) > 100)
        {
            $this->error_messages("[Q{$number}] 質問項目が不正です。");
            return FALSE;
        }

        return TRUE;
    }

    private function _set_question($questionnaire_id, $questionnaire, $question_id, $question, $number)
    {
        log_debug("=== _set_question =========================");
        log_debug($question_id);
        log_debug("============================================");

        if ( ! $this->_validate_question($question, $number))
        {
            return FALSE;
        }

        $type = $question['type'];

        $question_data = [
            'questionnaire_id' => $questionnaire_id,
            'number'    => $number,
            'question'  => $question['question'],
            'type'      => $type,
            'item'      => '',
            'nps_flag'  => NULL,
        ];

        if ($question_id == 'intro')
        {
            $question_data['number'] = NULL;

            if ( ! $question_id = $this->questions_tbl->insert($question_data))
            {
                return FALSE;
            }
        }
        elseif (preg_match('|^new-(\d+)$|', $question_id, $match))
        {
            $nps_flag = FALSE;

            if (isset($questionnaire['nps_flag']) && $questionnaire['nps_flag'] == $question_id)
            {
                $nps_flag = TRUE;
            }

            $question_data['questionnaire_id'] = $questionnaire_id;

            if ( ! $question_id = $this->questions_tbl->insert($question_data))
            {
                return FALSE;
            }

            if ($nps_flag)
            {
                $questionnaire['nps_flag'] = $question_id;
                unset($question['nps_correlation_flag']);
            }
        }

        if ($type == 'level')
        {
            if ( ! empty($question['nps_correlation_flag']))
            {
                $question['min_level'] = 1;
                $question['max_level'] = 5;
                $question_data['nps_correlation_flag'] = 1;
                $question_data['item'] = $question['item'];
            }

            if ($question['min_level'] >= $question['max_level'])
            {
                $this->error_messages('評価アンケートの最小値・最大値が不正です。');
                return FALSE;
            }
            $question_data['min_level'] = $question['min_level'];
            $question_data['max_level'] = $question['max_level'];
            $question_data['min_label'] = $question['min_label'];
            $question_data['max_label'] = $question['max_label'];
            $question_data['nps_flag']  = $questionnaire['nps_flag'] == $question_id ? '1' : NULL;
        }
        elseif ($type == 'select_one')
        {
            $this->question_selections_tbl->delete(['question_id' => $question_id]);

            foreach ($question['selections'] as $i => $selection)
            {
                $question_selection = [
                    'question_id'   => $question_id,
                    'priority'      => $i,
                    'label'         => $selection['label'],
                    'selection'     => $selection['selection'],
                    'color'         => $selection['color'],
                    'default_flag'  => (isset($questionnaire['default_flag'][$question_id]) && $questionnaire['default_flag'][$question_id] == $i) ? '1' : '0',
                ];

                $this->question_selections_tbl->insert($question_selection);
            }
        }
        elseif ($type == 'select_multi')
        {
            $this->question_selections_tbl->delete(['question_id' => $question_id]);

            foreach ($question['selections'] as $i => $selection)
            {
                $question_selection = [
                    'question_id'   => $question_id,
                    'priority'      => $i,
                    'label'         => $selection['label'],
                    'selection'     => $selection['selection'],
                    'color'         => $selection['color'],
                    'default_flag'  => (isset($questionnaire['default_flag'][$question_id]) && $questionnaire['default_flag'][$question_id] == $i) ? '1' : '0',
                ];

                $this->question_selections_tbl->insert($question_selection);
            }
        }
        elseif ($type == 'message')
        {
            $question_data['sub_question'] = $question['message'];
        }
        elseif (empty($question['has_sub_question']))
        {
            $question_data['sub_question'] = '';
        }
        else
        {
            $question_data['sub_question'] = $question['sub_question'];
        }

        return ($this->questions_tbl->update($question_data, ['id' => $question_id]) !== FALSE);
    }

}

