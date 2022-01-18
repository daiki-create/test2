<?php

class Report extends MYAJAX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('questionnaire_model');
        $this->load->model('answer_model');
    }

    /**
     * 質問の回答数
     */
    public function count_answers($questionnaire_id=NULL, $term='30days', $stylist_id=NULL)
    {
        $status = 'NG';
        $data = [];

        $since = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $until = NULL;

        switch($term)
        {
            case 'this_month':
                $since = date('Y-m-01 00:00:00');
                break;
            case 'last_month':
                $since = date('Y-m-01 00:00:00', strtotime('last month'));
                $until = date('Y-m-t 23:59:59', strtotime('last month'));
                break;
            case 'half_year':
                $since = date('Y-m-01 00:00:00', strtotime('-6 months'));
                break;
            case 'one_year':
                $since = date('Y-m-01 00:00:00', strtotime('-12 months'));
                break;
        }

        $questionnaire = $this->questionnaire_model->get_questionnaire($this->_login['salon_id'], $questionnaire_id, TRUE) OR
        $questionnaire = $this->questionnaire_model->get_questionnaire('0', $questionnaire_id, TRUE);
        //log_debug($questionnaire);

        $stylist_id =  empty($stylist_id) ? $this->_login['stylist_id'] : $stylist_id;

        if ($count_answers = $this->answer_model->report_count_answers($questionnaire['questions'], $this->_login['salon_id'], $stylist_id, $questionnaire_id, $since, $until))
        {
            //log_debug($count_levels);
            $status = 'OK';
            $data['count_levels'] = $count_answers['count_levels'];
            $data['count_select_ones'] = $count_answers['count_select_ones'];
            $data['color_select_ones'] = $count_answers['color_select_ones'];
        }

        $this->response($status, $data);
    }

    /**
     * 平均推移 + NPS相関
     */
    public function average_level($questionnaire_id=NULL, $term='30days', $stylist_id='')
    {
        $status = 'OK';
        $data = [];

        $since = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $until = NULL;
        $is_daily = TRUE;

        switch($term)
        {
            case 'this_month':
                $since = date('Y-m-01 00:00:00');
                break;
            case 'last_month':
                $since = date('Y-m-01 00:00:00', strtotime('last month'));
                $until = date('Y-m-t 23:59:59', strtotime('last month'));
                break;
            case 'half_year':
                $since = date('Y-m-01 00:00:00', strtotime('-6 months'));
                $is_daily = FALSE;
                break;
            case 'one_year':
                $since = date('Y-m-01 00:00:00', strtotime('-12 months'));
                $is_daily = FALSE;
                break;
        }

        $questionnaire = $this->questionnaire_model->get_questionnaire($this->_login['salon_id'], $questionnaire_id, TRUE) OR
        $questionnaire = $this->questionnaire_model->get_questionnaire('0', $questionnaire_id, TRUE);
        //log_debug($questionnaire);

        $stylist_id =  empty($stylist_id) ? $this->_login['stylist_id'] : $stylist_id;

        if ($levels = $this->answer_model->report_average_levels($questionnaire['questions'], $this->_login['salon_id'], $stylist_id, $questionnaire_id, $since, $until, $is_daily))
        {
            $status = 'OK';
            $data = $levels;
        }

        $this->response($status, $data);
    }

    /**
     * NPS相関
     */
    public function nps_level($questionnaire_id=NULL, $stylist_id='')
    {
        $status = 'OK';
        $data = [];

        $since = date('Y-m-01 00:00:00', strtotime('-6 months'));
        $questionnaire = $this->questionnaire_model->get_questionnaire($this->_login['salon_id'], $questionnaire_id, TRUE) OR
        $questionnaire = $this->questionnaire_model->get_questionnaire('0', $questionnaire_id, TRUE);

        $stylist_id =  empty($stylist_id) ? $this->_login['stylist_id'] : $stylist_id;

        if ($nps_levels = $this->answer_model->report_nps_levels($questions, $salon_id, $stylist_id, $questionnaire_id, $since))
        {
            $status = 'OK';
            $data['nps_levels'] = $nps_levels;
        }

        $this->response($status, $data);
    }

    /**
     * 追加質問の回答
     */
    public function sub_question_answer($questionnaire_id=NULL, $question_id=NULL, $term='30days', $stylist_id='')
    {
        $status = 'NG';
        $data = [];

        $since = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $until = NULL;

        $this->response($status, $data);

        switch($term)
        {
            case 'this_month':
                $since = date('Y-m-01 00:00:00');
                break;
            case 'last_month':
                $since = date('Y-m-01 00:00:00', strtotime('last month'));
                $until = date('Y-m-t 23:59:59', strtotime('last month'));
                break;
            case 'half_year':
                $since = date('Y-m-01 00:00:00', strtotime('-6 months'));
                break;
            case 'one_year':
                $since = date('Y-m-01 00:00:00', strtotime('-12 months'));
                break;
        }

        $stylist_id =  empty($stylist_id) ? $this->_login['stylist_id'] : $stylist_id;

        if ($sub_answers = $this->answer_model->get_sub_answers($questionnaire_id, $stylist_id, $question_id, $since, $until))
        {
            $status = 'OK';
            $data = $sub_answers;
        }

        $this->response($status, $data);
    }

}


