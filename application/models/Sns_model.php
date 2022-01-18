<?php

class Sns_model extends MY_Model
{
    private $_response = NULL;
    private $_auth_provider = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tables/sns_authentications_tbl');
        $this->load->model('tables/stylists_tbl');
        $this->load->model('tables/salons_tbl');
    }

    public function initialize($response, $auth_provider)
    {
        log_debug("Sns_model.initialize(response, {$auth_provider}) run.");
        log_debug($response);
        $this->_auth_provider = $auth_provider;

        if ($auth_provider == 'facebook')
        {
            if (isset($response['email']) && strlen($response['email']) > 0 &&
                isset($response['id'])    && strlen($response['id']) > 0 &&
                isset($response['name'])  && strlen($response['name']) > 0)
            {
                $this->_response = [
                    'snsid'     => $response['id'],
                    'user_name' => $response['name'],
                    'loginid'   => $response['email'],
                ];
            }
        }
        elseif ($auth_provider == 'line')
        {
            if (isset($response['email']) && strlen($response['email']) > 0 &&
                isset($response['sub'])   && strlen($response['sub']) > 0 &&
                isset($response['name'])  && strlen($response['name']) > 0)
            {
                $this->_response = [
                    'snsid'     => $response['sub'],
                    'user_name' => $response['name'],
                    'loginid'   => $response['email'],
                ];
            }
        }
        /*
        elseif ($auth_provider == 'google') { }
        elseif ($auth_provider == 'yahoo') { }
        elseif ($auth_provider == 'twitter') { }
        */

        return $this;
    }

    // --------------------------------------------------------------------------------------------

    /**
     * SNS認証
     * @param $response SNS APIからのレスポンスデータ
     * @param $auth_provider facebook | instagram | google | yahoo
     * @return array|bool
     */
    public function authenticate($online_salon_flag=FALSE)
    {
        log_debug("Sns_model.authenticate() run.");

        if ( ! $this->_response)
        {
            log_error('Failed to get profiles !!!');
            return FALSE;
        }

        $tmp_loginpw = NULL;

        $db = $this->stylists_tbl->initialize(
            $this->salons_tbl->initialize(
                $this->sns_authentications_tbl->initialize('master')
            )
        );
        $this->sns_authentications_tbl->trans_start();

        if ( ! $stylist = $this->stylists_tbl->get_login_by_sns_auth($this->_response['loginid']))
        { // アカウント無し
            $this->load->model('stylist_model');
            $this->stylist_model->_db = $db;

            // スタイリスト登録(仮登録)
            if ( ! $stylist = $this->stylist_model->create_stylist('0', [
                'loginid' => $this->_response['loginid'],
                'kana'    => '',
                'name'    => $this->_response['user_name'],
                'note'    => '',
                'online_salon_flag' => ($online_salon_flag) ? '1' : '0',
            ]))
            {
                $this->sns_authentications_tbl->trans_rollback();
                log_error('Failed to create stylist !!!');
                return FALSE;
            }

            $tmp_loginpw = random_string('alnum', 32);
            //$stylist['online_salon_flag'] = ($online_salon_flag) ? '1' : '0';
            $stylist['online_salon_status'] = 'new';
        }
        elseif ($online_salon_flag && empty($stylist['online_salon_flag']))
        {
            $this->stylists_tbl->update(['online_salon_flag' => '1',], ['id' => $stylist['id'],]);
        }

        // スタイリスト情報と紐づけます
        $this->sns_authentications_tbl->replace([
            'stylist_id'    => $stylist['id'],
            'auth_provider' => $this->_auth_provider,
            'user_name'     => $this->_response['user_name'],
            'snsid'         => $this->_response['snsid'],
            'mail'          => $this->_response['loginid'],
            'tmp_loginpw'   => empty($tmp_loginpw) ? '' : password_hash($tmp_loginpw, PASSWORD_DEFAULT),
        ]);

        if ($this->sns_authentications_tbl->trans_complete())
        {
            $login = $this->_convert_login($stylist);
            $login['tmp_loginpw'] = $tmp_loginpw;
            log_debug("SNS Login OK.");
            log_debug($login);
            $this->stylists_tbl->update(['last_login_at' => date('Y-m-d H:i:s')], ['id' => $stylist['id']]);
            return $login;
        }

        log_error("Failed to insert sns_authentications.");
        return FALSE;
    }

    // ============================================================================================

    private function _convert_login($stylist)
    {
        return [
            'loginid'        => $stylist['loginid'],
            'stylist_id'     => $stylist['id'],
            'salon_id'       => $stylist['salon_id'],
            'sns_id'         => $this->_response['snsid'],
            'name'           => $stylist['name'],
            'status'         => $stylist['status'],
            'agreement_flag' => $stylist['agreement_flag'],
            'manager_flag'   => $stylist['manager_flag'],
            'online_salon_flag'   => $stylist['online_salon_flag'],
            'online_salon_status' => $stylist['online_salon_status'],
        ];
    }

}
