<?php

require_once APPPATH.'includes/vendor/php-graph-sdk-5.x/src/Facebook/autoload.php';

class MY_sns
{
    private $_fb = NULL;
    private $_line = NULL;

    private $_app_id = '';
    private $_app_secret = '';
    private $_auth_provider = '';
    private $_scope = [];
    private $_callback = NULL;
    private $_reauth = FALSE;

    private $_access_token = NULL;
    private $_error_message;

    // --------------------------------------------------------------------------------------------

    public function __construct($config = [])
    {
        $this->initialize($config);
    }

    // --------------------------------------------------------------------------------------------

    public function initialize($config = [])
    {
        log_debug("MY_sns/initialize() run.");

        $this->_fb = NULL;
        $this->_line = NULL;

        if (isset($config['app_id']) && isset($config['app_secret']) && isset($config['auth_provider']) && isset($config['callback']))
        {
            $this->_app_id = $config['app_id'];
            $this->_app_secret = $config['app_secret'];
            $this->_auth_provider = $config['auth_provider'];
            $this->_callback = base_url($config['callback'], 'https');

            if ($this->_auth_provider == 'facebook')
            {
                $this->_fb = new Facebook\Facebook([
                    'app_id'                => $config['app_id'],
                    'app_secret'            => $config['app_secret'],
                    'default_graph_version' => isset($config['default_graph_version']) ?: 'v5.0',
                ]);

                $this->_scope = isset($config['scope']) ?: ['public_profile' => '', 'email' => '',];
            }
            else if ($this->_auth_provider == 'line')
            {
                $this->_line = [
                    'login_url'          => $config['login_url'],
                    'request_token_url'  => $config['request_token_url'],
                    'request_verify_url' => $config['request_verify_url'],
                ];
                $this->_scope = isset($config['scope']) ?: 'profile openid email';
            }
        }
        else
        {
            log_error('Incorrect Parameter.');
        }
    }

    // --------------------------------------------------------------------------------------------

    public function get_login_url($state=NULL, $nonce=NULL)
    {
        if ($this->_fb)
        {
            return $this->_fb->getRedirectLoginHelper()->getLoginUrl($this->_callback, ['email']);
        }
        elseif($this->_line && ! is_null($state) && ! is_null($nonce))
        {
            $url = $this->_line['login_url'];
            $url .= "?response_type=code";
            $url .= "&client_id={$this->_app_id}";
            $url .= "&redirect_uri={$this->_callback}";
            $url .= "&state={$state}";
            $url .= "&scope={$this->_scope}";
            $url .= "&nonce={$nonce}";
            log_info($url);
            return $url;
        }

        return NULL;
    }

    // --------------------------------------------------------------------------------------------

    public function get_response($code=NULL, $nonce=NULL)
    {
        if ($this->_fb)
        {
            try
            {
                $this->_access_token = $this->_fb->getRedirectLoginHelper()->getAccessToken();
                $permissions = $this->_fb->get('/me?fields=permissions', $this->_access_token);
                $permissions = $permissions->getDecodedBody();
                log_debug($permissions);

                foreach ($permissions['permissions']['data'] as $permission)
                if ($permission['permission'] == 'email')
                { // メールアドレスへのアクセス認可確認
                    if ($permission['status'] == 'granted')
                        break;

                    log_error("Cannot read email.");
                    // メールアドレスの認可がない場合は再認可処理へ
                    $this->_reauth = TRUE;
                    return FALSE;
                }

                $field = '';

                if (isset($this->_scope['public_profile']))
                    $field .= 'id,name';

                if (isset($this->_scope['email']))
                {
                    if (strlen($field) > 0)
                        $field .= ',';

                    $field .= 'email';
                }

                //log_debug($field);
                $response = $this->_fb->get('/me?fields='.$field, $this->_access_token);
                return $response->getDecodedBody();
            }
            catch (Facebook\Exceptions\FacebookResponseException $e)
            {
                log_error('Graph returned an error: ' . $e->getMessage());
            }
            catch (Facebook\Exceptions\FacebookSDKException $e)
            {
                log_error('Facebook SDK returned an error: ' . $e->getMessage());
            }
        }
        elseif ($this->_line && ! is_null($code) && ! is_null($nonce))
        {
            try
            {
                // アクセストークン取得
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                curl_setopt($ch, CURLOPT_URL, $this->_line['request_token_url']);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                    'grant_type'    => 'authorization_code',
                    'code'          => $code,
                    'redirect_uri'  => $this->_callback,
                    'client_id'     => $this->_app_id,
                    'client_secret' => $this->_app_secret,
                ]));

                $json_response = curl_exec($ch);
                curl_close($ch);
                $response_token = json_decode($json_response, TRUE);

                if (isset($response_token['error']) OR empty($response_token['id_token']))
                {
                    log_error("Failed to get LINE token.");
                    if (isset($response_token['error_description']))
                        log_error($response_token['error_description']);

                    return FALSE;
                }

                // ID トークン検証 プロファイル情報取得
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                curl_setopt($ch, CURLOPT_URL, $this->_line['request_verify_url']);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                    'id_token'  => $response_token['id_token'],
                    'client_id' => $this->_app_id,
                ]));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $json_response = curl_exec($ch);
                curl_close($ch);
                $response_verify = json_decode($json_response, TRUE);
                //log_info($response_verify);

                if (isset($response_verify['error']))
                {
                    log_error("Failed to get LINE profiles. ".$response_token['error_description']);
                    return FALSE;
                }

                if ( ! isset($response_verify['nonce']) OR $response_verify['nonce'] != $nonce)
                {
                    log_error("Invalid LINE nonce.");
                    return FALSE;
                }

                if ( ! isset($response_verify['email']))
                { // メールアドレスが取得できない場合は再認可
                    log_error("Cannot read email.");
                    $this->_reauth = TRUE;
                    return FALSE;
                }

                return $response_verify;
            }
            catch (Exception $e)
            {
                log_error('Failed to get LINE response. ' . $e->getMessage());
            }
        }


        log_error('Failed to get response.');
        return NULL;
    }

    // --------------------------------------------------------------------------------------------

    public function set_callback($callback)
    {
        $this->_callback = $callback;
    }

    // --------------------------------------------------------------------------------------------

    public function set_scope($scope)
    {
        $this->_scope = $scope;
    }

    // --------------------------------------------------------------------------------------------

    public function get_error_message()
    {
        return $this->_error_message;
    }

    // --------------------------------------------------------------------------------------------

    /**
     * アクセス再認可URL
     */
    public function get_reauth_url($state=NULL, $nonce=NULL)
    {
        if ($this->_reauth)
        {
            $this->_reauth = FALSE;

            if ($this->_fb)
            {
                $url = $this->_fb->getRedirectLoginHelper()->getReRequestUrl($this->_callback, ['email']);
                log_debug($url);
                return $url;
            }
            elseif ($this->_line)
            {
                $url = $this->_line['login_url'];
                $url .= "?response_type=code";
                $url .= "&client_id={$this->_app_id}";
                $url .= "&redirect_uri={$this->_callback}";
                $url .= "&state={$state}";
                $url .= "&scope={$this->_scope}";
                $url .= "&nonce={$nonce}";
                $url .= "&prompt=consent";
                log_debug($url);
                return $url;
            }
        }

        return NULL;
    }


}