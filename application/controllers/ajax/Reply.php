<?php

class Reply extends MYAJAX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('answer_model');
    }

    public function get_replies($stylist_id=NULL, $offset='0')
    {
        $status = 'NG';
        $data = $pagination = [];

        if ($replies =  $this->answer_model->get_replies($stylist_id, $offset))
        {
            $status = 'OK';
            $data['replies'] = $replies;
            $pagination = $this->answer_model->pagination();
            log_debug($pagination);
        }

        $this->response($status, $data, $pagination);
    }

}

