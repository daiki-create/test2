<?php

class Mypage extends MYSYSADM_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin_model');
    }

    // -----------------------------------------------------------------------------------------------------------

    public function index()
    {
    }

    // -----------------------------------------------------------------------------------------------------------

    public function welcome()
    {
        $this->view->assign('sysadmin', $this->admin_model->get_admin($this->_login['admin_id']));
    }

    // -----------------------------------------------------------------------------------------------------------

    public function update_admin()
    {
        $required = [
            'admin_id'  => TRUE,
            'loginpw'   => TRUE,
            'confirm_loginpw'   => TRUE,
        ];

        if ($this->validate('common', $required))
        {
            $admin_id   = $this->post('admin_id');
            $loginpw    = $this->post('loginpw');
            $confirm_loginpw = $this->post('confirm_loginpw');

            if ($loginpw === $confirm_loginpw && $this->_login['admin_id'] == $admin_id)
            {
                if ($this->admin_model->update_loginpw($admin_id, $loginpw, '1'))
                {
                    $this->_login['status'] = '1';
                    $this->session->set_userdata("{$this->_module}_login", $this->_login);
                    $this->redirect("/{$this->_module}/");
                }
                else
                {
                    $this->_error_messages = 'パスワードの変更に失敗しました。';
                }
            }
            else
            {
                $this->_error_messages = 'パスワードが一致しません。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/welcome/");
    }

}

