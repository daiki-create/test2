<?php

class Answer extends MYAJAX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('answer_model');
    }

    public function get_answer($reply_id)
    {
        $status = 'NG';
        $data   = [];

        if ($answer = $this->answer_model->get_answer($reply_id))
        {
            log_debug($answer);
            $status = 'OK';
            $data['answer'] = $answer;
        }

        $this->response($status, $data);
    }

}

