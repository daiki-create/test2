<?php

class Inquiry extends MYASUBI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->config->load('asubi');
    }

    // --------------------------------------------------------------------------------------------------------------------------

    /**
     * お問い合わせフォーム
     *
     * @uri /asubi/inquiry/form/
     */
    public function form()
    {
        $this->view->assign('inquiry', $this->session->flashdata('post'));
    }

    // --------------------------------------------------------------------------------------------------------------------------

    /**
     * お問い合わせ確認画面
     *
     * @uri /asubi/inquiry/confirm/
     */
    public function confirm()
    {
        $required = [
            'inquiry' => TRUE,
        ];

        if (empty($this->_login))
        {
            $required['name'] = TRUE;
            $required['mail'] = TRUE;
        }

        if ($this->validate('inquiry', $required))
        {
            $this->view->assign('inquiry', $this->_post);
        }
        else
        {
            $this->session->set_flashdata('post', $this->_post);
            $this->redirect("/{$this->_module}/{$this->_class}/form/");
        }
    }

    // --------------------------------------------------------------------------------------------------------------------------

    /**
     * お問い合わせ送信処理
     *
     * @uri /asubi/inquiry/send/
     */
    public function send()
    {
        $required = [
            'inquiry' => TRUE,
        ];

        if (empty($this->_login))
        {
            $required['name'] = TRUE;
            $required['mail'] = TRUE;
        }

        if ($this->validate('inquiry', $required))
        {
            $this->load->model('mail_model');
            // 管理者へ通知
            $this->mail_model->initialize(NULL, config_item('asubi_notice_mail'));
            $this->mail_model->send_inquiry($this->_post, $this->_login);

            // ユーザーへ通知
            $this->mail_model->initialize(NULL, config_item('asubi_notice_mail'));
            $this->mail_model->send_inquiry_reception($this->_post, $this->_login);
            $this->_messages[] = 'お問い合わせいただきありがとうございました。';
        }
        else
        {
            $this->session->set_flashdata('post', $this->_post);
        }

        $this->redirect("/{$this->_module}/{$this->_class}/form/");
    }

}
