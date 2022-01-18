<?php

class Login extends MYSALON_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('stylist_model');
    }

    // --------------------------------------------------------------------------------------------------------------------------------

    /**
     * ログイン画面
     */
    public function index()
    {
        $remember_me = '0';
        
        if ($loginid = get_cookie("{$this->_module}_remember_me"))
        {
            $loginid = $this->encrypt->decode($loginid);
            $remember_me = '1';
        }
        else
        {
            $loginid = $this->session->flashdata('loginid');
        }

        $state = md5(uniqid(random_string(), TRUE));
        $this->session->set_userdata('state', $state);

        $nonce = md5(uniqid(random_string(), TRUE));
        $this->session->set_userdata('nonce', $nonce);

        $this->load->library('MY_sns', config_item('facebook'));
        $this->view->assign('facebook_login_url', $this->my_sns->get_login_url());

        $this->my_sns->initialize(config_item('line'));
        $this->view->assign('line_login_url', $this->my_sns->get_login_url($state, $nonce));

        $this->view->assign('loginid',      $loginid);
        $this->view->assign('remember_me',  $remember_me);
    }

    // --------------------------------------------------------------------------------------------------------------------------------

    /**
     * ログイン認証
     */
    public function auth()
    {
        if ($login = $this->stylist_model->authenticate($this->post('loginid'), $this->post('loginpw')))
        {
            log_debug($login);
            if (empty($login['salon_id']))
                $login['manager_flag'] = '0';

            set_cookie("{$this->_module}_remember_me", $this->encrypt->encode($this->_post['loginid']), (30 * 24 * 60 * 60));
            $this->session->set_userdata("{$this->_module}_login", $login);
            $this->session->set_userdata("{$this->_module}_login2", $login);
        }
        else if ($error_messages = $this->stylist_model->error_messages())
        {
            log_error($error_messages);
            $this->_error_messages = $error_messages;
            $this->session->set_flashdata('loginid', $this->_post['loginid']);
            $this->redirect("/{$this->_module}/login/");
        }

        $this->redirect("/{$this->_module}/");
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
     * 利用規約許諾・会員登録FORM
     */
    public function form($loginid=NULL, $auth_provider=NULL)
    {
        log_debug($loginid);
        $this->_post['loginid'] = empty($loginid) ? NULL : urldecode($loginid);
        $required = ['loginid' => FALSE];
        log_debug($this->_post);

        if ($this->stylist_model->has_agreement($this->_post['loginid']))
        {
            $this->session->set_flashdata('loginid', $this->_post['loginid']);
            $this->redirect('/salon/login/');
        }

        if ($this->validate('common', $required))
        {
            if ( ! $this->stylist_model->is_available_salon_by_loginid($this->_post['loginid']))
            {
                if ($error_messages = $this->stylist_model->error_messages())
                {
                    log_error($error_messages);
                    $this->view->assign('error_messages', $error_messages);
                }
            }

            $this->view->assign('tmp_loginpw', $this->session->flashdata('tmp_loginpw'));
            $this->view->assign('auth_provider', $auth_provider);
            $this->view->assign('loginid', $this->_post['loginid']);
        }

        //$this->load->model('setting_model');
        //$this->view->assign('terms_of_service', $this->setting_model->get_terms_of_service());
    }

    // --------------------------------------------------------------------------------------------------------------------------------

    /**
     * 利用規約許諾・会員本登録
     */
    public function join($auth_provider=NULL)
    {
        $required = [
            'loginid' => TRUE,
            'loginpw' => TRUE,
            'tmp_loginpw' => FALSE,
        ];

        if ( ! empty($this->_post['agreement']))
        {
            if ($auth_provider)
            {
                log_debug($this->_post);
                $login = $this->stylist_model->sns_authenticate($this->_post['loginid'], $this->_post['tmp_loginpw'], $auth_provider);
            }
            else
            {
                $login = $this->stylist_model->authenticate($this->post('loginid'), $this->post('loginpw'), TRUE);
            }

            if ($login)
            {
                if ($this->stylist_model->agree($login['stylist_id']))
                {
                    $login['status'] = '1';
                    $login['agreement_flag'] = '1';
                    $this->session->set_userdata("{$this->_module}_login", $login);
                    $this->redirect("/{$this->_module}/mypage/form/");
                }
            }
            elseif ($error_messages = $this->stylist_model->error_messages())
            {
                log_error($error_messages);
                $this->_error_messages = $error_messages;
            }
            else
            {
                log_error('Login Failed.');
            }
        }

        if ($auth_provider){
            $this->redirect("/{$this->_module}/{$this->_class}/");
        }
        else
            $this->redirect("/{$this->_module}/{$this->_class}/form/".urlencode($this->_post['loginid']));
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
            $nonce = $this->session->userdata('nonce');

            if ($auth_provider == 'line' && $state != $this->post('state'))
            { // state確認
                $this->session->unset_userdata("{$this->_module}_reauth");
                $this->_error_messages = 'ログインに失敗しました。';
                $this->redirect("/{$this->_module}/login/");
            }

            $this->load->library('MY_sns', config_item($auth_provider));

            $response = [
                'email'=>'test@gmail.com',
                'id'     => 'test',
                'name'   => 'test',
            ];
            //テスト用：疑似FBログイン（stateに32桁の適当な数字ってバリデーションの関数に書いてあった。）
            //http://192.168.1.202/salon/login/callback/facebook?state=00000000000000000000000000000000

            //疑似ログインに邪魔なのでコメントアウト
            // if ($response = $this->my_sns->get_response($this->input->get('code'), $nonce))
            {
                $this->load->model('sns_model');

                if ($login = $this->sns_model->initialize($response, $auth_provider)->authenticate())
                {
                    if (empty($login['agreement_flag']))
                    {
                        log_debug($login);
                        $this->session->set_flashdata('tmp_loginpw', $login['tmp_loginpw']);
                        $this->redirect("/{$this->_module}/{$this->_class}/form/".urlencode($login['loginid'])."/{$auth_provider}/");
                    }
                    elseif (empty($login['salon_id']))
                    {
                        $login['manager_flag'] = '0';
                    }

                    $this->session->unset_userdata("{$this->_module}_reauth");
                    $this->session->set_userdata("{$this->_module}_login", $login);
                    $this->redirect("/{$this->_module}/");
                }
            }

            if ($reauth_url = $this->my_sns->get_reauth_url($state, $nonce))
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

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function request()
    {
        $loginid = $this->session->flashdata('loginid');
        $this->view->assign('loginid', $loginid);
    }

    public function reregist()
    {
        $required = [
            'loginid' => TRUE,
        ];

        if ($this->validate('common', $required))
        {
            if ($stylist = $this->stylist_model->update_stylist_pw_md5($this->_post['loginid']))
            {
                $assign_data = [
                    'name'         => $stylist['name'],
                    'reregist_url' => site_url() . "{$this->_module}/login/reset/{$stylist['reset_pw_md5']}/",
                ];

                $this->sendmail($this->_post['loginid'], 'hairlogy パスワード再登録', $assign_data);
                $this->_messages[] = 'パスワード再登録メールを送信しました。';
            }
            elseif ($error_messages = $this->stylist_model->error_messages())
            {
                log_error($error_messages);
                $this->_error_messages = $error_messages;
                $this->session->set_flashdata('loginid', $this->_post['loginid']);
            }
            else
            {
                $this->_error_messages[] = '再登録申請に失敗しました。再度お試しください。';
                $this->session->set_flashdata('loginid', $this->_post['loginid']);
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/request/");
    }

    public function reset($reset_pw_md5=NULL)
    {
        $required = [
            'reset_pw_md5' => TRUE,
        ];

        $this->_post['reset_pw_md5'] = $reset_pw_md5;

        if ($this->validate('common', $required))
        {
            if ($stylist = $this->stylist_model->get_stylist_by_reset_pw_md5($reset_pw_md5))
            {
                $this->view->assign('stylist', $stylist);
                $this->view->assign('reset_pw_md5', $reset_pw_md5);
            }
            elseif ($error_messages = $this->stylist_model->error_messages())
            {
                log_error($error_messages);
                $this->_error_messages = $error_messages;
                $this->redirect("/{$this->_module}/{$this->_class}/");
            }
            else
            {
                $this->redirect("/{$this->_module}/{$this->_class}/");
            }
        }
        else
        {
            $this->redirect("/{$this->_module}/{$this->_class}/");
        }
    }

    public function update_pw($salon_id=NULL, $stylist_id=NULL, $reset_pw_md5=NULL)
    {
        $required = [
            'new_password'     => TRUE,
            'confirm_password' => TRUE,
            'salon_id'         => TRUE,
            'stylist_id'       => TRUE,
            'reset_pw_md5'     => TRUE,
        ];

        $this->_post['salon_id'] = $salon_id;
        $this->_post['stylist_id'] = $stylist_id;
        $this->_post['reset_pw_md5'] = $reset_pw_md5;

        if ($this->validate('stylist', $required) &&
            $this->_post['new_password'] === $this->_post['confirm_password'])
        {
            if ($this->stylist_model->update_pw($this->_post))
            {
                $this->_messages[] = 'パスワードを再登録しました。';
                $this->redirect("/{$this->_module}/{$this->_class}/");
            }
            elseif ($error_messages = $this->stylist_model->error_messages())
            {
                log_error($error_messages);
                $this->_error_messages = $error_messages;
            }
            else
            {
                $this->_error_messages[] = '再登録に失敗しました。再度お試しください。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/reset/{$reset_pw_md5}/");
    }

    public function test($test=null)
    {
        $a=$this->_login['stylist_id'];
        $this->view->assign("a",$a);
    }

    // テスト用強制退会の手動ボタン
    public function kick()
    {

        // テスト表示用。処理には関係なし
        $month = date('Y-m-01');
        $min_month=date('Y-m-01',strtotime('-2 month'));

        $command = 'php ' . FCPATH . "index.php cli salon unsubscribe ";
        exec( $command, $output, $retval );
        if( empty( $retval ) ){
            $this->_messages[] = "強制退会をしました___".$min_month.'___'.$month.'___';
        }else{
            $this->_error_messages[] = '強制退会に失敗しました___'.$min_month.'___'.$month;
            $this->_error_messages[] = $command;
            $this->_error_messages[] = print_r( $output, TRUE );
        }

        $this->redirect("/{$this->_module}/{$this->_class}/test/");

    }

   }

