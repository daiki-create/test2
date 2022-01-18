<?php

/**
 * Class Left
 *
 *  退会処理
 *
 * @property Online_salon_model $online_salon_model;
 * @property Mail_model $mail_model;
 *
 */
class Leaving extends MYASUBI_Controller {

    public function __construct()
    {
        parent::__construct();

        // 未入会とか、既に退会済とか許さないよ
        if( $this->_login['online_salon_status'] == 'new' || $this->_login['online_salon_status'] == 'checking' ){
            $this->_error_messages[] = 'まだ入会されていません。';
            $this->redirect("/{$this->_module}/");
        }
        if( $this->_login['online_salon_status'] == 'left' ){
            $this->_error_messages[] = '既に退会済です。';
            $this->redirect("/{$this->_module}/info/left/");
        }

        $this->config->load('asubi');
        $this->load->model('mail_model');

    }

    // --------------------------------------------------------------------------------------------------------------------------

    /**
     * 退会確認画面
     */
    public function index()
    {
        $this->redirect("/{$this->_module}/{$this->_class}/confirm/");
    }

    public function confirm()
    {
    }

    // --------------------------------------------------------------------------------------------------------------------------

    /**
     * 退会処理
     */
    public function leave()
    {

        // 退会処理
        if( $this->online_salon_model->left_online_salon_stylist( $this->_login['id'] ) ){

            // 管理者へ通知
            $this->mail_model->initialize( NULL, config_item('asubi_notice_mail') );
            $this->mail_model->send_asubi_withdrawal($this->_login);

            // ユーザーへ通知
            $this->mail_model->initialize( NULL, config_item('asubi_notice_mail') );
            $this->mail_model->send_asubi_withdrawal_user($this->_login);

            // さようなら
            $this->session->unset_userdata("{$this->_module}_login");
            $this->_messages[] = '退会手続きが完了しました。';
            $this->redirect("/{$this->_module}/info/left/");

        }

        $this->_error_messages[] = '退会手続きに失敗しました。';
        $this->redirect("/{$this->_module}/{$this->_class}/confirm/");

    }

    // --------------------------------------------------------------------------------------------------------------------------

}
