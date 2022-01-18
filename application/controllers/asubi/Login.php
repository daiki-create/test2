<?php

class Login extends MYASUBI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    // --------------------------------------------------------------------------------------------------------------------------

    public function index()
    {
        $state = md5(uniqid(random_string(), TRUE));
        $this->session->set_userdata('state', $state);

        $facebook_config = config_item('facebook');
        $facebook_config['callback'] = 'asubi/login/callback/facebook/';
        $this->load->library('MY_sns', $facebook_config);

        $this->view->assign('facebook_login_url', $this->my_sns->get_login_url());
    }

    // --------------------------------------------------------------------------------------------------------------------------------

    /**
     * ログアウト
     */
    public function logout()
    {
        $this->session->unset_userdata("{$this->_module}_login");
        $this->redirect("/{$this->_module}/{$this->_class}/");
    }

    // --------------------------------------------------------------------------------------------------------------------------------

    /**
     * SNS認証コールバック
     */
    public function callback($auth_provider=NULL)
    {
        $this->_post['auth_provider'] = $auth_provider;
        $this->_post['state'] = $this->input->get('state');
        $required = ['auth_provider' => TRUE, 'state' => TRUE,];

        if ($this->validate('sns', $required))
        {
            $state = $this->session->userdata('state');
            $this->load->library('MY_sns', config_item($auth_provider));

            if ( $response = $this->my_sns->get_response($this->input->get('code')))
            {
                $this->load->model('sns_model');

                if ($login = $this->sns_model->initialize($response, $auth_provider)->authenticate(TRUE))
                {

                    // 認証成功 -> 再ロードしてログイン状態にする
                    $this->load->model('online_salon_model');
                    $this->_login = $this->online_salon_model->get_online_salon_stylist($login['stylist_id']);
                    $this->session->unset_userdata("{$this->_module}_reauth");
                    $this->session->set_userdata("{$this->_module}_login", $this->_login );

                    // カード登録フェーズ（またはMyPage）へ
                    $this->redirect("/{$this->_module}/creditcard/");

                }
            }

            if ($reauth_url = $this->my_sns->get_reauth_url($state))
            { // アクセス再認可画面へ
                if ( ! $this->session->userdata("{$this->_module}_reauth"))
                {
                    log_debug("redirect reauth.");
                    $this->session->set_userdata("{$this->_module}_reauth", "1");
                    $this->redirect($reauth_url);
                }
            }
        }

        $this->session->unset_userdata("{$this->_module}_reauth");
        $this->_error_messages = 'ログインに失敗しました。';
        $this->redirect("/{$this->_module}/login/");
    }

}
