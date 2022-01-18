<?php

class Youtube extends MYCLI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Youtube_model');
    }


    public function create_post($index = 0)
    {
        if ($this->Youtube_model->create_yt_post($index) === TRUE) 
        {
            echo 'youtube動画データの挿入ができました。';
        }
        else 
        {
            echo 'youtube動画データの挿入に失敗しました。';
        }
    }
}
