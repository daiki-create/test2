<?php

class Questionnaire extends MYSALON_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('questionnaire_model');
    }

    public function index()
    {
        $questionnaires = $this->questionnaire_model->get_my_questionnaires($this->_login['stylist_id'], $this->_login['salon_id']);
        //log_debug($questionnaires);
        $this->view->assign('questionnaires', $questionnaires);
        $this->view->assign('reply_interval_days', config_item('reply_interval_days'));
    }

    public function preview($questionnaire_id=NULL)
    {
        if ( ! $questionnaire = $this->questionnaire_model->get_questionnaire($this->_login['salon_id'], $questionnaire_id, TRUE))
            $questionnaire = $this->questionnaire_model->get_questionnaire('0', $questionnaire_id, TRUE);
        log_debug($questionnaire);

        $this->view->assign('questionnaire', $questionnaire);
    }

    public function done($questionnaire_id=NULL)
    {
        $this->load->model('landing_page_model');

        $questionnaire = $this->questionnaire_model->get_questionnaire('0', $questionnaire_id, TRUE);
        $lp_url = $this->landing_page_model->current_landing_page($this->_login['salon_id']);

        $this->view->assign('questionnaire', $questionnaire);
        $this->view->assign('lp_url', $lp_url);
    }

}
