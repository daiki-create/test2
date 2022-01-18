<?php

class Stylist_model extends MY_Model {

    public $_db;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tables/salons_tbl');
        $this->load->model('tables/stylists_tbl');
        $this->_db = 'master';
    }

    // ----------------------------------------------------------------------------------------------------------

    public function sns_authenticate($loginid, $tmp_loginpw, $auth_provider)
    {
        log_debug("Stylist_model.sns_authenticate({$loginid}, ******, {$auth_provider}) run.");
        $this->stylists_tbl->initialize();

        if ($this->is_available_salon_by_loginid($loginid, TRUE) &&
            $stylist = $this->stylists_tbl->auth_login_by_sns_auth($loginid, $auth_provider))
        {
            if (password_verify($tmp_loginpw, $stylist['tmp_loginpw']) || $auth_provider)
            {
                $login = [
                    'stylist_id'    => $stylist['id'],
                    'salon_id'      => $stylist['salon_id'],
                    'name'          => $stylist['name'],
                    'status'        => $stylist['status'],
                    'agreement_flag'=> $stylist['agreement_flag'],
                    'manager_flag'  => $stylist['manager_flag'],
                ];
                log_debug("Login OK.");
                log_debug($login);
                $this->stylists_tbl->update(['last_login_at' => date('Y-m-d H:i:s')], ['id' => $stylist['id']]);
                return $login;
            }
            else
            {
                log_error("Tmp Password not match ! [{$tmp_loginpw}]");
            }
        }
        return FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function authenticate($loginid, $loginpw, $is_pre=FALSE)
    {
        log_debug("Stylist_model.authenticate({$loginid}, ******) run.");
        $this->stylists_tbl->initialize();

        $stylist = NULL;

        if ($this->is_available_salon_by_loginid($loginid, TRUE))
        {
            if ($is_pre === TRUE)
                $stylist = $this->stylists_tbl->get_pre_login($loginid);
            else
                $stylist = $this->stylists_tbl->get_login($loginid);

            if ($stylist)
            {
                if (password_verify($loginpw, $stylist['loginpw']))
                {
                    $login = [
                        'stylist_id'    => $stylist['id'],
                        'salon_id'      => $stylist['salon_id'],
                        'name'          => $stylist['name'],
                        'loginid'       => $stylist['loginid'],
                        'status'        => $stylist['status'],
                        'stylist_status'=> $stylist['stylist_status'],
                        'agreement_flag'=> $stylist['agreement_flag'],
                        'manager_flag'  => $stylist['manager_flag'],
                    ];
                    log_debug("Login OK.");
                    log_debug($login);
                    $this->stylists_tbl->update(['last_login_at' => date('Y-m-d H:i:s')], ['id' => $stylist['id']]);
                    return $login;
                }
            }
            else
            {
                log_error('Failed to get stylist.');
            }
        }

        return FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function agree($stylist_id)
    {
        log_debug("Stylist_model.agree({$stylist_id}) run.");
        $this->stylists_tbl->initialize();
        return $this->stylists_tbl->update(['status' => '1', 'agreement_flag' => '1'], ['id' => $stylist_id]) !== FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function get_stylists($salon_id, $offset=NULL, $status=NULL)
    {
        log_debug("Stylist_model.get_stylists({$salon_id}, {$offset}, {$status}) run.");
        $this->stylists_tbl->initialize('master');

        if ($offset !== NULL)
        {
            $limit = 20;
            $this->sanitize_offset($offset);
            $this->sanitize_limit($limit);
            $this->stylists_tbl->init_pagination($offset, $limit);
        }

        $stylists = $this->stylists_tbl->get_stylists($salon_id, $status);

        $this->pagination($this->stylists_tbl->load_pagination());

        return $stylists;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function get_stylist($salon_id, $stylist_id)
    {
        log_debug("Stylist_model.get_stylist({$salon_id}, {$stylist_id}) run.");

        $this->stylists_tbl->initialize('master');
        return $this->stylists_tbl->get_stylist($salon_id, $stylist_id);
    }

    // ----------------------------------------------------------------------------------------------------------

    public function update_stylist($salon_id, $stylist_id, $stylist)
    {
        log_debug("Stylist_model.update_stylist({$salon_id}, {$stylist_id}) run.");

        $this->_db = $this->stylists_tbl->initialize(
            $this->salons_tbl->initialize($this->_master)
        );

        if ( ! $this->is_available_salon_by_salon_id($salon_id))
            return FALSE;

        $cond = [
            'stylists.id'           => $stylist_id,
            'stylists.salon_id'     => $salon_id,
            'stylists.deleted_flag' => '0',
        ];

        $data = [
            'kana'  => mb_convert_kana($stylist['kana'], 'rnsKC'),
            'name'  => $stylist['name'],
        ];

        if (isset($stylist['status']))
        {
            if ($stylist['status'] == '1')
                $data['status'] = '1';
            else
                $data['status'] = '0';
        }

        if ( ! empty($stylist['loginid']))
        {
            $data['loginid'] = $stylist['loginid'];
        }

        if ( ! empty($stylist['loginpw']))
        {
            $data['loginpw'] = password_hash($stylist['loginpw'], PASSWORD_DEFAULT);
        }

        if (isset($stylist['manager_flag']))
            $data['manager_flag'] = empty($stylist['manager_flag']) ? '0' : '1';

        if (isset($stylist['phone']))
            $data['phone'] = $stylist['phone'];

        if ( ! empty($stylist['trial_limited_on']))
            $data['trial_limited_on'] = $stylist['trial_limited_on'];

        if (isset($stylist['note']))
            $data['note'] = $stylist['note'];

        if ($this->stylists_tbl->update($data, $cond) !== FALSE)
            return $this->stylists_tbl->get_stylist($salon_id, $stylist_id);
        else
            return FALSE;
    }
    // ----------------------------------------------------------------------------------

    public function left_stylist( $salon_id, $stylist_id )
    {
        log_debug("Stylist_model.left_stylist({ $salon_id, $stylist_id}) run.");

        // $this->_db = $this->Salon_receipts_tbl->initialize(
        //     $this->stylists_tbl->initialize( $this->_master )
        // );

        // 直前ロード
        $stylist = $this->get_stylist( $salon_id, $stylist_id );
        if( empty( $stylist ) ){
            log_error('stylist not found');
            return FALSE;
        }

        // 退会に更新
        $this->stylists_tbl->where('id',$stylist_id);
        $this->stylists_tbl->where_in('stylist_status',['trial','active','inactive']);
        if( !$this->stylists_tbl->update(['stylist_status' => 'left']) )
        {
            log_error('update error');
            return FALSE;
        }

        // クレジットカード情報削除

        // 退会しました
        return TRUE;
    }

    // ----------------------------------------------------------------------------------

    public function get_unsubscribe_target_stylists( $min_month, $month )
    {
        log_debug("Stulist_model.get_unsubscribe_target_stylists({$month}) run.");

        $this->stylists_tbl->initialize( $this->_master );
        $res = $this->stylists_tbl->get_unsubscribe_target_stylists( $min_month, $month);
    
        return $res;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function update_stylist_status($salon_id, $stylist_id, $status, $reset_pw=FALSE)
    {
        log_debug("Stylist_model.update_stylist_status({{$salon_id}, $stylist_id}, {$status}, {$reset_pw}) run.");

        $this->_master = $this->stylists_tbl->initialize('master');

        $cond = [
            'stylists.id'       => $stylist_id,
            'stylists.salon_id' => $salon_id,
            'stylists.deleted_flag' => '0',
        ];

        $data = ['status' => empty($status) ? '0' : '1'];

        if (empty($status))
        {
            if ($reset_pw === TRUE)
            {
                $loginpw = random_string('alpha', 6);
                $data['loginpw'] = password_hash($loginpw, PASSWORD_DEFAULT);
                $data['agreement_flag'] = '0';

                if ($this->stylists_tbl->update($data, $cond) !== FALSE)
                {
                    return $loginpw;
                }

                return NULL;
            }

            $cond['status'] = '1';
            return ($this->stylists_tbl->update($data, $cond) !== FALSE);
        }
        else
        {
            $cond['status'] = '0';
            return ($this->stylists_tbl->update($data, $cond) !== FALSE);
        }
    }

    // ----------------------------------------------------------------------------------------------------------

    public function create_trial($trial_stylist)
    {
        log_debug("Stylist_model.create_trial(trial_stylist) run.");
        $this->load->model('tables/questionnaires_tbl');
        $this->load->model('tables/salon_questionnaires_tbl');
        $this->_db = $this->questionnaires_tbl->initialize(
            $this->salon_questionnaires_tbl->initialize(
                $this->salons_tbl->initialize('master')
            )
        );
        $this->salons_tbl->trans_start();

        if ($stylist = $this->has_stylist($trial_stylist['loginid']))
        {
            log_debug($stylist);
            if ($stylist['deleted_flag'] == '0')
            {
                if (empty($stylist['agreement_flag']))
                {
                    $this->error_messages("トライアル会員登録確定のお知らせメールをご確認の上、\nトライアル会員登録を「確定」してください。");
                    return TRUE;
                }
                else
                {
                    $this->error_messages("アカウント情報が既に登録されています。\nログイン画面よりログインしてください。");
                }
            }
            else
            {
                $this->error_messages("アカウント情報が既に登録されていますが、利用できない状態です。\nサポートまでお問い合わせください。");
            }
            return FALSE;
        }

        $salon = $trial_stylist['salon'];

        $salon_data = [
            'name'  => $salon['name'],
            'phone' => $salon['phone'],
            'fax'   => $salon['fax'],
            'prefecture'=> $salon['prefecture'],
            'postcode1' => $salon['postcode1'],
            'postcode2' => $salon['postcode2'],
            'address'   => $salon['address'],
        ];

        if ($salon_id = $this->salons_tbl->insert($salon_data))
        {
            /*
            foreach ($salon['questionnaire_id'] as $questionnaire_id)
            {
                $this->salon_questionnaires_tbl->insert(['salon_id' => $salon_id, 'questionnaire_id' => $questionnaire_id]);
            }
            */
            $this->salon_questionnaires_tbl->insert(['salon_id' => $salon_id, 'questionnaire_id' => $salon['questionnaire_id']]);

            $trial_stylist['trial_limited_on'] = date('Y-m-d', strtotime('+90 days'));
            $trial_stylist['manager_flag'] = '1';

            if ($stylist = $this->create_stylist($salon_id, $trial_stylist))
            {
                $stylist['salon_id'] = $salon_id;
                log_debug($stylist);

                if ($this->salons_tbl->trans_complete())
                {
                    return $stylist;
                }
            }
        }

        $this->salons_tbl->trans_rollback();
        return FALSE;


    }

    // ----------------------------------------------------------------------------------------------------------

    public function create_stylist($salon_id, $stylist)
    {
        log_debug("Stylist_model.create_stylists({$salon_id}) run.");

        $this->load->model('tables/questionnaires_tbl');
        $this->load->model('tables/questionnaire_stylists_tbl');

        $this->questionnaires_tbl->initialize(
            $this->questionnaire_stylists_tbl->initialize(
                $this->stylists_tbl->initialize(
                    $this->salons_tbl->initialize($this->_db)
                )
            )
        );

        if ( ! $this->_db instanceof CI_DB)
        {
            $this->stylists_tbl->trans_start();
        }

        if ( ! $this->is_available_salon_by_salon_id($salon_id))
        {
            $this->stylists_tbl->trans_rollback();
            return FALSE;
        }

        $loginpw = random_string('alpha', 6);

        $stylist_data = [
            'salon_id'  => $salon_id,
            'loginid'   => $stylist['loginid'],
            'loginpw'   => password_hash($loginpw, PASSWORD_DEFAULT),
            'kana'      => mb_convert_kana($stylist['kana'], 'rnsKC'),
            'name'      => $stylist['name'],
            'phone'     => empty($stylist['phone']) ? NULL : $stylist['phone'],
            'note'      => $stylist['note'],
            'status'    => '0',
            'manager_flag'   => empty($stylist['manager_flag']) ? '0' : '1',
            'agreement_flag' => '0',
            'online_salon_flag' => empty($stylist['online_salon_flag']) ? '0' : '1',
            'trial_limited_on' => empty($stylist['trial_limited_on']) ? NULL: $stylist['trial_limited_on'],
        ];

        if ($stylist_id = $this->stylists_tbl->insert($stylist_data))
        {
            log_debug("stylist_id: {$stylist_id}");
            $qr_dir = "{$this->_data_dir}/qr_code/{$salon_id}";
            log_debug("qr_dir: {$qr_dir}");

            if ( ! file_exists($qr_dir))
            {
                if ( ! @mkdir($qr_dir))
                {
                    log_error("Failed to make directory. [{$qr_dir}]");
                    $this->error_messages("Failed to make directory. [{$qr_dir}]");
                    return FALSE;
                }
            }

            $this->questionnaires_tbl->select(['id']);

            if ($mc_questionnaires = $this->questionnaires_tbl->get_mc_questionnaires($salon_id))
            foreach ($mc_questionnaires as $mc_questionnaire)
            {
                if ( ! $this->_append_questionnaire($salon_id, $stylist_id, $mc_questionnaire['id']))
                {
                    if ( ! $this->_db instanceof CI_DB)
                    {
                        $this->stylists_tbl->trans_rollback();
                    }
                    return FALSE;
                }
            }

            $this->questionnaires_tbl->select(['id']);

            if ( ! empty($salon_id) && $questionnaires = $this->questionnaires_tbl->get_questionnaires($salon_id))
            foreach ($questionnaires as $questionnaire)
            {
                if ( ! $this->_append_questionnaire($salon_id, $stylist_id, $questionnaire['id']))
                {
                    if ( ! $this->_db instanceof CI_DB)
                    {
                        $this->stylists_tbl->trans_rollback();
                    }
                    return FALSE;
                }
            }

            if ( ! $this->_db instanceof CI_DB)
            {
                if ($this->stylists_tbl->trans_complete())
                {
                    $stylist_data['id']      = $stylist_id;
                    $stylist_data['loginpw'] = $loginpw;
                    return $stylist_data;
                }
            }
            else
            {
                $stylist_data['id']      = $stylist_id;
                $stylist_data['loginpw'] = $loginpw;
                return $stylist_data;
            }
        }
        elseif ( ! $this->_db instanceof CI_DB)
        {
            $this->stylists_tbl->trans_rollback();
        }

        if ($this->stylists_tbl->get_error_code() == '1062')
        {
            $this->error_messages("登録済みです。[{$stylist['loginid']}]");
        }

        return FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function append_questionnaire($salon_id, $questionnaire_id, $stylist_id, $_db=NULL)
    {
        log_debug("Stylist_model.append_questionnaire({$salon_id}, {$questionnaire_id}, {$stylist_id}) run.");
        if ($_db === NULL)
        {
            $_db = $this->questionnaire_stylists_tbl->initialize('master');
        }

        $questionnaire_stylist = ['questionnaire_id' => $questionnaire_id, 'stylist_id' => $stylist_id];

        if ( ! $this->questionnaire_stylists_tbl->find($questionnaire_stylist))
        {
            $qr_dir = "{$this->_data_dir}/qr_code/{$salon_id}";
            log_debug("qr_dir: {$qr_dir}");

            if ( ! file_exists($qr_dir))
            {
                if ( ! @mkdir($qr_dir))
                {
                    log_error("Failed to make directory. [{$qr_dir}]");
                    return FALSE;
                }
            }

            return $this->_append_questionnaire($salon_id, $stylist_id, $questionnaire_id);
        }

        return TRUE;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function delete_stylist($salon_id, $stylist_id)
    {
        log_debug("Stylist_model.delete_stylist({$salon_id}, {$stylist_id}) run.");

        $this->_db = $this->stylists_tbl->initialize(
            $this->salons_tbl->initialize($this->_master)
        );

        if ($this->is_available_salon_by_salon_id($salon_id))
        {
            $data = [
                'deleted_flag'  => '1',
                'loginid'       => NULL
            ];

            return ($this->stylists_tbl->update($data, ['id' => $stylist_id, 'salon_id' => $salon_id]) !== FALSE);
        }

        return FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function belong_to($salon_id, $stylist_id)
    {
        log_debug("Stylist_model.belong_to({$salon_id}, {$stylist_id}) run.");

        $this->load->model('tables/questionnaires_tbl');
        $this->load->model('tables/questionnaire_stylists_tbl');
        $this->_db = $this->questionnaire_stylists_tbl->initialize(
            $this->questionnaires_tbl->initialize(
                $this->stylists_tbl->initialize($this->_master)
            )
        );
        $this->stylists_tbl->trans_start();

        if ($this->is_available_salon_by_salon_id($salon_id))
        {
            $last_salon_id = $this->stylists_tbl->get_salon_id($stylist_id);

            if ($last_salon_id === NULL)
            {
                $this->stylists_tbl->trans_rollback();
                return FALSE;
            }

            $cond = [
                'id'            => $stylist_id,
                'salon_id'      => $last_salon_id,
                'status'        => '1',
                'deleted_flag'  => '0',
            ];

            $update_data = ['salon_id' => $salon_id, 'manager_flag' => '0'];

            if ($this->stylists_tbl->update($update_data, $cond) !== FALSE)
            {
                $rename_qrcodes = [];

                if ($mc_questionnaires = $this->questionnaires_tbl->get_mc_questionnaires($salon_id))
                foreach ($mc_questionnaires as $mc_questionnaire)
                {
                    $cond = ['stylist_id' => $stylist_id, 'questionnaire_id' => $mc_questionnaire['id']];

                    if ($q_stylists = $this->questionnaire_stylists_tbl->find($cond))
                    foreach ($q_stylists as $q_stylist)
                    {
                        if ( ! file_exists("{$this->_data_dir}/qr_code/{$last_salon_id}/{$q_stylist['code']}.png"))
                        {
                            $this->_mk_qrcode($last_salon_id, $q_stylist['code']);
                        }

                        $rename_qrcodes[] = $q_stylist['code'];
                    }
                    else
                    {
                        if ( ! $this->_append_questionnaire($salon_id, $stylist_id, $mc_questionnaire['id']))
                        {
                            $this->stylists_tbl->trans_rollback();
                            return FALSE;
                        }
                    }
                }

                $this->questionnaires_tbl->select(['id']);

                if ( ! empty($salon_id) && $questionnaires = $this->questionnaires_tbl->get_questionnaires($salon_id))
                foreach ($questionnaires as $questionnaire)
                {
                    $cond = ['stylist_id' => $stylist_id, 'questionnaire_id' => $questionnaire['id']];

                    if ($q_stylists = $this->questionnaire_stylists_tbl->find($cond))
                    foreach ($q_stylists as $q_stylist)
                    {
                        if ( ! file_exists("{$this->_data_dir}/qr_code/{$last_salon_id}/{$q_stylist['code']}.png"))
                        {
                            $this->_mk_qrcode($last_salon_id, $q_stylist['code']);
                        }

                        $rename_qrcodes[] = $q_stylist['code'];
                    }
                    else
                    {
                        if ( ! $this->_append_questionnaire($salon_id, $stylist_id, $questionnaire['id']))
                        {
                            $this->stylists_tbl->trans_rollback();
                            return FALSE;
                        }
                    }
                }

                foreach ($rename_qrcodes as $rename_qrcode)
                {
                    $old_path = "{$this->_data_dir}/qr_code/{$last_salon_id}/{$rename_qrcode}.png";
                    $new_path = "{$this->_data_dir}/qr_code/{$salon_id}/{$rename_qrcode}.png";
                    log_debug($old_path);
                    log_debug($new_path);
                    if (file_exists($old_path))
                    {
                        if ( ! @copy($old_path, $new_path))
                        {
                            $this->stylists_tbl->trans_rollback();
                            return FALSE;
                        }

                        @unlink($old_path);
                    }
                }
            }

            return $this->stylists_tbl->trans_complete();
        }

        $this->stylists_tbl->trans_rollback();
        return FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function has_agreement($loginid)
    {
        log_debug("Stylist_model.has_agreement({$loginid}) run.");

        $this->stylists_tbl->initialize('master');

        $where = [
            'status'         => '1',
            'agreement_flag' => '1',
            'deleted_flag'   => '0',
            'loginid'        => $loginid,
        ];

        $this->stylists_tbl->where($where);

        if ($this->stylists_tbl->find()) 
            return TRUE;
        else
            return FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function has_stylist($loginid)
    {
        log_debug("Stylist_model.has_stylist({$loginid}) run.");

        $this->stylists_tbl->initialize('master');

        $where = [
            'loginid'        => $loginid,
        ];

        $this->stylists_tbl->where($where);

        if ($this->stylists_tbl->find()) 
            return $this->stylists_tbl->get_row();
        else
            return FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function update_stylist_pw_md5($loginid)
    {
        log_debug("Stylist_model.reregist({$loginid}) run.");

        $this->stylists_tbl->initialize('master');

        if ($login = $this->stylists_tbl->get_login($loginid))
        {
            $update_data = [
                'reset_pw_md5'        => md5(uniqid(random_string(), TRUE)),
                'reset_pw_limited_at' => date("Y-m-d H:i:s",strtotime("+1 day")),
            ];

            if ($this->stylists_tbl->update($update_data, ['loginid' => $loginid,]) !== FALSE)
            {
                return [
                    'name'                => $login['name'],
                    'reset_pw_md5'        => $update_data['reset_pw_md5'],
                    'reset_pw_limited_at' => $update_data['reset_pw_limited_at'],
                ];
            }
        }
        else
        {
            $this->error_messages("登録されていないログインIDです。");
        }

        return FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function get_stylist_by_reset_pw_md5($reset_pw_md5)
    {
        log_debug("Stylist_model.get_stylist_by_reset_pw_md5({$reset_pw_md5}) run.");

        $this->stylists_tbl->initialize('master');

        if ($stylist = $this->stylists_tbl->get_stylist_by_reset_pw_md5($reset_pw_md5))
        {
            if ( ! empty($stylist['reset_pw_limited_at']) &&
                strtotime($stylist['reset_pw_limited_at']) >= strtotime(date("Y-m-d H:i:s")))
            {
                return $stylist;
            }
            else
            {
                $this->error_messages("URLの有効期限が切れています。再度パスワード再登録の手続きをお願いします。");
            }
        }
        else
        {
            $this->error_messages("不正なURLです。");
        }

        return NULL;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function update_pw($stylist)
    {
        log_debug("Stylist_model.update_pw(stylist) run.");

        $this->stylists_tbl->initialize('master');

        if ($_stylist = $this->get_stylist($stylist['salon_id'], $stylist['stylist_id'], $stylist['reset_pw_md5']))
        {
            if ( ! empty($_stylist['reset_pw_limited_at']) &&
                strtotime($_stylist['reset_pw_limited_at']) >= strtotime(date("Y-m-d H:i:s")))
            {
                $update_data = [
                    'loginpw'             => password_hash($stylist['new_password'], PASSWORD_DEFAULT),
                    'reset_pw_md5'        => NULL,
                    'reset_pw_limited_at' => NULL,
                ];

                $cond = [
                    'id'       => $stylist['stylist_id'],
                    'salon_id' => $stylist['salon_id'],
                ];

                return ($this->stylists_tbl->update($update_data, $cond) !== FALSE);
            }
            else
            {
                $this->error_messages("URLの有効期限が切れています。再度パスワード再登録の手続きをお願いします。");
            }
        }

        return FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function is_available_salon_by_loginid($loginid, $initialize=FALSE)
    {
        log_debug("Stylist_model.is_available_salon_by_loginid({$loginid}) run.");

        if ( ! $initialize)
            $this->stylists_tbl->initialize();

        if ($stylist = $this->stylists_tbl->get_any_stylist($loginid))
        {
            if ($stylist['salon_status'] == '0')
            {
                $this->error_messages('サロンのアカウントが停止されています。');
            }
            elseif ($stylist['salon_deleted_flag'] == '1')
            {
                $this->error_messages('サロンが削除されました。');
            }
            /*
            elseif ($stylist['deleted_flag'] == '1')
            {
                $this->error_messages('ユーザーアカウントが削除されました。');
            }
            */
            else
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------

    public function is_available_salon_by_salon_id($salon_id)
    {
        log_debug("Stylist_model.is_available_salon_by_salon_id({$salon_id}) run.");

        if ($salon_id === '0')
        {
            return TRUE;
        }

        $this->salons_tbl->initialize($this->_db);

        if ($salon = $this->salons_tbl->get_any_salon($salon_id))
        {
            if ($salon['status'] == '0')
            {
                $this->error_messages('サロンのアカウントが停止されている為、更新できません。');
            }
            elseif ($salon['deleted_flag'] == '1')
            {
                $this->error_messages('サロンが削除されている為、更新できません。');
            }
            else
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    // ==========================================================================================================

    private function _qr_code($url, $png_path)
    {
        log_debug("Stylist_model._qr_code({$url}, {$png_path}) run.");

        $this->load->library('MY_qrcode', NULL, 'qrcode');
        $this->qrcode->initialize(['qr_margin' => '0', 'qr_version' => '4', 'ec_level' => 'L']);
        return $this->qrcode->generate($url, $png_path);
    }

    // ----------------------------------------------------------------------------------------------------------

    public function get_online_salon_users($search, $offset)
    {
        log_debug("Stylist_model.get_online_salon_users({$offset}) run.");

        $this->stylists_tbl->initialize('master');

        if ($offset !== NULL)
        {
            $limit = 20;
            $this->sanitize_offset($offset);
            $this->sanitize_limit($limit);
            $this->stylists_tbl->init_pagination($offset, $limit);
        }

        $online_salon_users = $this->stylists_tbl->get_online_salon_users($search);
        $this->pagination($this->stylists_tbl->load_pagination());
        return $online_salon_users;

    }

    // ----------------------------------------------------------------------------------------------------------

    public function get_online_salon_checking_users()
    {
        log_debug("Stylist_model.get_online_salon_checking_users run.");

        $this->stylists_tbl->initialize('master');
        return $this->stylists_tbl->get_online_salon_checking_users();
    }


    // ==========================================================================================================

    private function _append_questionnaire($salon_id, $stylist_id, $questionnaire_id)
    {
        log_debug("Stylist_model._append_questionnaire({$salon_id}, {$stylist_id}, {$questionnaire_id}) run.");
        $_brk = 0;
        do
        {
            $code = strtolower(random_string('alnum', 5));
            $questionnaire_stylist = [
                'questionnaire_id'  => $questionnaire_id,
                'stylist_id'        => $stylist_id,
                'code'              => $code,
            ];
            $_brk++;
        }
        while($this->questionnaire_stylists_tbl->insert($questionnaire_stylist) === FALSE && $_brk < 100);

        if ($_brk >= 100)
        {
            $this->error_messages("Failed to generate questionnaire code as MC.");
            return FALSE;
        }

        return $this->_mk_qrcode($salon_id, $code);
    }

    private function _mk_qrcode($salon_id, $code)
    {
        $qr_dir = "{$this->_data_dir}/qr_code/{$salon_id}";
        $url = site_url() . "q/{$code}";
        $png_path = "{$qr_dir}/{$code}.png";
        log_debug("qr_code image: {$png_path}");

        if ( ! $this->_qr_code($url, $png_path))
        {
            $this->error_messages("Failed to create QR code image. [{$png_path}]");
            return FALSE;
        }

        return TRUE;
    }

}