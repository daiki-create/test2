<?php

class Administrator extends MYSYSADM_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin_model');
    }

    // -----------------------------------------------------------------------------------------------------------

    public function index()
    {
        $this->load->helper('string');
        $administrators = $this->admin_model->get_admins();
        $csrf = md5(uniqid(random_string(), TRUE));
        log_debug($administrators);
        $this->view->assign('administrators', $administrators);
        $this->view->assign('csrf', $csrf);
        $this->session->set_userdata('csrf', $csrf);
    }

    // -----------------------------------------------------------------------------------------------------------

    public function form($admin_id=NULL)
    {
        $this->view->assign('admin_id', $admin_id);
    }

    // -----------------------------------------------------------------------------------------------------------

    public function create()
    {
        $required = [
            'name'      => TRUE,
            'loginid'   => TRUE,
        ];

        if ($this->validate('common', $required))
        {
            $loginid = $this->post('loginid');

            if ($administrator = $this->admin_model->create_admin($loginid, $this->post('name')))
            {
                log_debug($administrator);
                $assign_data = [
                    'name'    => $administrator['name'],
                    'loginid' => $loginid,
                    'loginpw' => $administrator['loginpw'],
                    'login_url' => site_url() . "{$this->_module}/login/",
                ];
                $this->sendmail($loginid, 'アカウント登録', $assign_data);
                $this->_messages = "システム管理者「{$administrator['name']} ({$loginid})」を登録しました。";
            }
            else
            {
                $this->_error_messages = 'システム管理者登録に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/form/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function update()
    {
        $required = [
            'admin_id'  => TRUE,
            'name'      => TRUE,
            'loginid'   => TRUE,
            //'loginpw'   => FALSE,
            'status'    => FALSE,
        ];

        if ($this->validate('common', $required))
        {
            if ($this->admin_model->update_admin($this->post('admin_id'), $this->post()))
            {
                $this->_messages = 'システム管理者情報を更新しました。';
            }
            else
            {
                $this->_error_messages = 'システム管理者情報更新に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/index/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function reset_pw()
    {
        $required = ['admin_id' => TRUE, 'csrf' => TRUE];

        if ($this->validate('common', $required) && $this->session->userdata('csrf') == $this->post('csrf'))
        {
            $admin_id = $this->post('admin_id');

            if ($admin = $this->admin_model->reset_loginpw($admin_id))
            {
                $assign_data = [
                    'loginpw'   => $admin['loginpw'],
                    'login_url' => site_url() . "{$this->_module}/login/",
                ];
                $this->sendmail($admin['loginid'], 'パスワードリセット', $assign_data);
                $this->_messages = 'ログインパスワードをリセットしました。';
            }
            else
            {
                $this->_error_messages = 'ログインパスワードリセットに失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function delete()
    {
        $required = ['admin_id' => TRUE, 'csrf' => TRUE];

        if ($this->validate('common', $required) && $this->session->userdata('csrf') == $this->post('csrf'))
        {
            if ($this->admin_model->delete_admin($this->post('admin_id')))
            {
                $this->_messages = 'システム管理者名を削除しました。';
            }
            else
            {
                $this->_error_messages = 'システム管理者削除に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function mypage()
    {
        $admin_id = $this->_login['admin_id'];
        $administrator = $this->admin_model->get_admin($admin_id);
        $this->view->assign('administrator', $administrator);
    }

    // -----------------------------------------------------------------------------------------------------------

    public function update_pw()
    {
        $required = [
            'loginpw'   => TRUE,
            'confirm_loginpw'   => TRUE,
        ];

        if ($this->validate('common', $required))
        {
            $loginpw    = $this->post('loginpw');
            $confirm_loginpw = $this->post('confirm_loginpw');

            if ($loginpw === $confirm_loginpw)
            {
                if ($this->admin_model->update_loginpw($this->_login['admin_id'], $loginpw))
                {
                    $this->_messages = 'パスワードを変更しました。';
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
        $this->redirect("/{$this->_module}/{$this->_class}/mypage/");
    }

}

