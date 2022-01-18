<?php

class Questionnaire extends MYSYSADM_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('questionnaire_model');
    }

    // -----------------------------------------------------------------------------------------------------------

    public function index($salon_id='0', $offset='0')
    {
        $questionnaires = $this->questionnaire_model->get_questionnaires('0');
        //log_debug($questionnaires);
        $this->view->assign('questionnaires', $questionnaires);
    }

    // -----------------------------------------------------------------------------------------------------------

    public function detail($questionnaire_id=NULL)
    {
        $questions = [];

        if ($questionnaire = $this->questionnaire_model->get_questionnaire('0', $questionnaire_id))
        {
            //log_debug($questionnaire);
            if ($questions = $this->questionnaire_model->get_questions($questionnaire_id))
                $questionnaire['questions'] = array_column($questions, NULL, 'id');

            //log_debug($questions);
        }
        else
        {
            $this->redirect("/{$this->_module}/{$this->_class}/");
        }

        $this->view->assign('questionnaire_id', $questionnaire_id);
        $this->view->assign('questionnaire',    $questionnaire);
        $this->view->assign('questions',        $questions);
        return $questionnaire;
    }

    // -----------------------------------------------------------------------------------------------------------

    public function form($questionnaire_id=NULL)
    {
        $questionnaire = empty($questionnaire_id) ? [] : $this->detail($questionnaire_id);

        if ($post = $this->session->flashdata('post'))
        {
            log_debug($post);
            $questionnaire = array_merge($questionnaire, $post);
            foreach ($questionnaire['questions'] as $question_id => &$question)
            {
                log_debug($question);
                if ($question_id == $post['nps_flag'])
                {
                    $question['nps_flag'] = '1';
                }
                if (isset($post['default_flag'][$question_id]) && ($question['type'] == 'select_one' OR $question['type'] == 'select_multi'))
                {
                    $question['selections'][$post['default_flag'][$question_id]]['default_flag'] = '1'; 
                }
            }
        }
        log_debug($questionnaire);

        $default_colors = config_item('default_colors');
        $this->view->assign('default_colors',   $default_colors);
        $this->view->assign('questionnaire',    $questionnaire);
        $this->js_data(['default_colors' => $default_colors]);
    }

    // -----------------------------------------------------------------------------------------------------------

    public function create()
    {
        $required = [
            'type'  => TRUE,
            'title' => TRUE,
            'note'  => FALSE,
        ];

        if ($this->validate('questionnaire', $required) && $this->_question_validate())
        {
            if ($questionnaire_id = $this->questionnaire_model->create_questionnaire($questionnaire_id, $this->_post))
            {
                $this->_messages[] = 'アンケートを新規登録しました。';
                $this->redirect("/{$this->_module}/{$this->_class}/detail/{$questionnaire_id}/");
            }
            else
            {
                $this->_error_messages[] = 'アンケートの新規登録に失敗しました。';
            }
        }

        $this->session->set_flashdata('post', $this->_post);
        $this->redirect("/{$this->_module}/{$this->_class}/form/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function update($questionnaire_id=NULL)
    {
        $required = [
            'type'  => TRUE,
            'title' => TRUE,
        ];

        if ($this->validate('questionnaire', $required) && $this->_question_validate())
        {
            if ($this->questionnaire_model->update_questionnaire('0', $questionnaire_id, $this->_post))
            {
                $this->_messages[] = 'アンケートの内容を更新しました。';
                $this->redirect("/{$this->_module}/{$this->_class}/detail/{$questionnaire_id}/");
            }
            elseif ($error_messages = $this->questionnaire_model->error_messages())
            {
                log_error($error_messages);
                $this->_error_messages = $error_messages;
            }
            else
            {
                $this->_error_messages[] = 'アンケートの更新に失敗しました。';
            }
        }

        $this->session->set_flashdata('post', $this->_post);
        $this->redirect("/{$this->_module}/{$this->_class}/form/{$questionnaire_id}/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function enable()
    {
        $status = '1';

        if ($this->questionnaire_model->update_questionnaire_status('0', $this->_post['questionnaire_id'], $status))
        {
            $this->_messages[] = 'アンケートを有効にしました。';
        }
        else
        {
            $this->_error_messages[] = 'アンケートの状態更新に失敗しました。';
        }

        $this->redirect("/{$this->_module}/{$this->_class}/detail/{$this->_post['questionnaire_id']}/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function disable()
    {
        $status = '0';

        if ($this->questionnaire_model->update_questionnaire_status('0', $this->_post['questionnaire_id'], $status))
        {
            $this->_messages[] = 'アンケートを停止しました。';
        }
        else
        {
            $this->_error_messages[] = 'アンケートの状態更新に失敗しました。';
        }

        $this->redirect("/{$this->_module}/{$this->_class}/detail/{$this->_post['questionnaire_id']}/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function delete()
    {
        if ($this->questionnaire_model->delete_questionnaire('0', $this->_post['questionnaire_id']))
        {
            $this->_messages[] = 'アンケートを削除しました。';
        }
        else
        {
            $this->_error_messages[] = 'アンケートの削除に失敗しました。';
            $this->redirect("/{$this->_module}/{$this->_class}/detail/{$this->_post['questionnaire_id']}/");
        }

        $this->redirect("/{$this->_module}/{$this->_class}/index/");
    }

    // ===========================================================================================================

    private function _question_validate()
    {
        if (isset($this->_post['questions']) && is_array($this->_post['questions']) && count($this->_post['questions']) > 0)
        {
            $question_validate = TRUE;
            $validation_errors = $this->_validation_errors;

            foreach ($this->_post['questions'] as $num => $question)
            {
                if (isset($this->_post['remove'][$num]))
                {
                    continue;
                }

                log_debug($question);
                $required = [
                    'question'  => TRUE,
                    'type'      => TRUE,
                    'sub_question' => FALSE,
                ];

                if ($question['type'] == 'level')
                {
                    $required['min_label'] = $required['min_level'] = $required['max_label'] = $required['max_level'] = TRUE;
                }
                elseif ($question['type'] == 'select_one' OR $question['type'] == 'select_multi')
                {
                    $required['selections'] = TRUE;
                }
                elseif ($question['type'] == 'message')
                {
                    $required['message'] = TRUE;
                }

                if ( ! $this->validate('question', $required, $question))
                {
                    log_error("Validation Error: Q{$question['number']}");
                    $validation_errors["Q{$question['number']}"] = $this->_validation_errors;
                    $question_validate = FALSE;
                }
            }

            log_error($validation_errors);
            $this->_validation_errors = $validation_errors;

            return $question_validate;
        }

        return FALSE;
    }

}

