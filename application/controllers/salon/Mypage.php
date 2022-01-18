<?php

class Mypage extends MYSALON_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('stylist_model');
        $this->load->model('salon_mail_model');
    }

    public function index()
    {
        $stylist = $this->stylist_model->get_stylist($this->_login['salon_id'], $this->_login['stylist_id']);
        $this->view->assign('stylist', $stylist);
    }

    public function form($salon_id=NULL, $stylist_id=NULL)
    {
        $this->index();
    }

    public function update($salon_id=NULL, $stylist_id=NULL)
    {
        $required = [
            'kana'      => FALSE,
            'name'      => TRUE,
            //'loginid'   => TRUE,
            'loginpw'   => FALSE,
        ];

        if ($this->validate('stylist', $required))
        {
            if ($this->stylist_model->update_stylist($salon_id, $stylist_id, $this->_post))
            {
                $this->_messages = '更新しました。';
            }
            elseif ($error_messages = $this->stylist_model->error_messages())
            {
                log_error($error_messages);
                $this->_error_messages = $error_messages;
            }
            else
            {
                $this->error_messages = '更新に失敗しました。';
            }
        }
        else
        {
            if (isset($this->_validation_errors['loginpw']))
            {
                $length = strlen($this->_validation_errors['loginpw']);

                if ($length > 20 OR $length < 6)
                    $this->_validation_errors['loginpw'] = 'ログインパスワードは、6文字以上20文字以下で登録してください。';
                else
                    $this->_validation_errors['loginpw'] = 'ログインパスワードは、半角英数字・記号で登録してください。';
            }

        }

        $this->redirect("/{$this->_module}/{$this->_class}/");
    }

    public function leave()
    {
        // 退会処理
        if( $this->stylist_model->left_stylist( $this->_login['salon_id'], $this->_login['stylist_id'] ) ){
            // 管理者へ通知
            $this->salon_mail_model->initialize( NULL, config_item('notice_mail') );
            $this->salon_mail_model->send_withdrawal($this->_login);

            // ユーザーへ通知
            $this->salon_mail_model->initialize( NULL, config_item('notice_mail') );
            $this->salon_mail_model->send_withdrawal_user($this->_login);

            // さようなら
            $this->session->unset_userdata("{$this->_module}_login");
            $this->_messages[] = '退会手続きが完了しました。';
            $this->redirect("/{$this->_module}/{$this->_class}/");
        }

        $this->_error_messages[] = '退会手続きに失敗しました。';
        $this->redirect("/{$this->_module}/{$this->_class}/");
    }

}

