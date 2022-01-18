<?php

class Nps extends MYAJAX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('questionnaire_model');
        $this->load->model('answer_model');
    }

    /**
     * NPS推移
     */
    public function transition($questionnaire_id=NULL, $stylist_id=NULL)
    {
        $status = 'NG';
        $data = [];

        $stylist_id =  empty($stylist_id) ? $this->_login['stylist_id'] : $stylist_id;
        log_debug($stylist_id);

        if ($nps_transition = $this->answer_model->nps_transitions($this->_login['salon_id'], $stylist_id, $questionnaire_id))
        {
            //log_debug($nps_transition);
            $data['nps_transition'] = $nps_transition;
            $status = 'OK';
        }

        $this->response($status, $data);
    }
}

