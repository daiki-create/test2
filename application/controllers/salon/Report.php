<?php

class Report extends MYSALON_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('questionnaire_model');
        $this->load->model('answer_model');
    }

    public function index()
    {
        $questionnaires = $this->questionnaire_model->get_my_questionnaires($this->_login['stylist_id'], $this->_login['salon_id'], TRUE);
        log_debug($questionnaires);
        $this->view->assign('questionnaires', $questionnaires);
    }

    public function detail($questionnaire_id=NULL)
    {
        $questionnaire = $this->questionnaire_model->get_questionnaire($this->_login['salon_id'], $questionnaire_id, TRUE) OR
        $questionnaire = $this->questionnaire_model->get_questionnaire('0', $questionnaire_id, TRUE);

        if ($this->_login['manager_flag'])
        {
            $this->load->model('stylist_model');
            $stylists = $this->stylist_model->get_stylists($this->_login['salon_id'], NULL, '1');
            //log_debug($stylists);
            $this->view->assign('stylists', $stylists);
        }

        //log_debug($questionnaire);
        $this->view->assign('questionnaire', $questionnaire);
        $this->js_data(['default_colors' => config_item('default_colors')]);
    }

}
