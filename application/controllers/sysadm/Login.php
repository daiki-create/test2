<?php

class Login extends MYSYSADM_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $remember_me = '0';

        if ($loginid = get_cookie("{$this->_module}_remember_me"))
        {
            $loginid = $this->encrypt->decode($loginid);
            $remember_me = '1';
        }

        $this->view->assign('loginid',      $loginid);
        $this->view->assign('remember_me',  $remember_me);
    }

    public function auth()
    {
        $this->load->model('admin_model');

        if ($login = $this->admin_model->authenticate($this->post('loginid'), $this->post('loginpw')))
        {
            if ( ! empty($this->_post['remember_me']))
            {
                set_cookie("{$this->_module}_remember_me", $this->encrypt->encode($this->_post['loginid']), (30 * 24 * 60 * 60));
            }
            else
            {
                delete_cookie("{$this->_module}_remember_me");
            }

            $this->session->set_userdata("{$this->_module}_login", $login);
            $this->redirect("/{$this->_module}/");
        }

        $this->redirect("/{$this->_module}/{$this->_class}/logout/");
    }

    public function logout()
    {
        $this->session->unset_userdata("{$this->_module}_login");
        $this->redirect("/{$this->_module}/{$this->_class}/");
    }

}

