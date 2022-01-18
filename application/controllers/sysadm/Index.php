<?php

class Index extends MYSYSADM_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Youtube_model');
    }

    // -------------------------------------------------------------------------------------------------

    /**
     * TOP画面
     */
    public function index()
    {
    }

    // -------------------------------------------------------------------------------------------------

    /**
     * YouTube動画更新
     */
    public function create_post()
     {
        if ($inserted_count = $this->Youtube_model->create_yt_post())
        {   
            $this->_messages[] = "{$inserted_count}件、追加登録されました。";
        }
        else
        {
            $this->_error_messages[] = '追加する新しい動画はありませんでした。';
        }

        $this->redirect("/{$this->_module}/index/");
     }

}
