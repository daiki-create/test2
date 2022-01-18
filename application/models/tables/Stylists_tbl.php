<?php
/**
 * Stylists_tbl Class
 *
 *   スタイリスト管理テーブル
 *
 * @project     Hairlogy
 * @package     Table model
 * @author      yuki.hatano@gmail.com
 * @copyright   montecampo Co., Ltd. All Rights Reserved
 */

class Stylists_tbl extends MY_Table {

    public function __construct()
    {
        parent::__construct();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_salon_id($stylist_id)
    {
        $this->select('stylists.salon_id');
        $this->where('stylists.id', $stylist_id);
        $this->where('stylists.deleted_flag', '0');

        if ($this->find())
            return $this->get('salon_id');

        return NULL;
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_login($loginid)
    {
        $this->select([
            'stylists.id',
            'stylists.loginid',
            'stylists.loginpw',
            'stylists.name',
            'stylists.salon_id',
            'stylists.status',
            'stylists.stylist_status',
            'stylists.agreement_flag',
            'stylists.manager_flag',
            'stylists.trial_limited_on',
        ]);
        // サロン無所属または、サロン有効
        $this->join('salons', 'salons.id = stylists.salon_id', 'left');
        $this->where('stylists.status', '1');
        $this->where('stylists.agreement_flag', '1');
        $this->where('stylists.deleted_flag', '0');
        $this->group_start();
        $this->where('stylists.salon_id', '0');
        $this->or_group_start();
        $this->where('salons.status', '1');
        $this->where('salons.deleted_flag', '0');
        $this->group_end();
        $this->group_end();

        if ($this->find(['stylists.loginid' => $loginid]))
        {
            return $this->get_row();
        }

        return NULL;
    }

    // -----------------------------------------------------------------------------------------------------

    /**
     * 仮登録スタイリスト
     */
    public function get_pre_login($loginid)
    {
        $this->select(['id', 'loginid', 'loginpw', 'name', 'salon_id', 'status', 'stylist_status', 'agreement_flag', 'manager_flag']);
        $this->where('status', '0');
        $this->where('agreement_flag', '0');
        $this->where('deleted_flag', '0');

        if ($this->find(['loginid' => $loginid]))
        {
            return $this->get_row();
        }

        return NULL;
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_stylists($salon_id, $status=NULL)
    {
        $this->select([
            'stylists.id',
            'stylists.salon_id',
            'stylists.kana',
            'stylists.name',
            'stylists.loginid',
            'stylists.phone',
            'stylists.note',
            'stylists.status',
            'stylists.manager_flag',
            'stylists.agreement_flag',
            'stylists.reset_pw_limited_at',
            'stylists.trial_limited_on',
            'stylists.last_login_at',
            'stylists.created_at',
            'stylists.updated_at',
            'salons.name as salon_name',
            '(SELECT COUNT(`replies`.`id`) FROM `replies` WHERE `replies`.`stylist_id` = `stylists`.`id`) AS reply_count',
            '(SELECT MAX(`replies`.`created_at`) FROM `replies` WHERE `replies`.`stylist_id` = `stylists`.`id`) AS last_replied_at',
        ]);
        $this->join('salons', 'salons.id = stylists.salon_id', 'left');
        $this->where('stylists.salon_id', $salon_id);
        $this->where('stylists.online_salon_flag', '0');
        $this->where('stylists.deleted_flag', '0');

        if ($status !== NULL)
            $this->where('stylists.status', $status);

        return $this->find(NULL, NULL, ['stylists.id DESC']);
    }
    // -----------------------------------------------------------------------------------------------------

    public function get_stylist($salon_id, $stylist_id, $reset_pw_md5=NULL)
    {
        $this->select([
            'stylists.id',
            'stylists.salon_id',
            'stylists.kana',
            'stylists.name',
            'stylists.loginid',
            'stylists.phone',
            'stylists.note',
            'stylists.status',
            'stylists.stylist_status',
            'stylists.manager_flag',
            'stylists.agreement_flag',
            'stylists.reset_pw_limited_at',
            'stylists.trial_limited_on',
            'stylists.online_salon_flag',
            'stylists.online_salon_status',
            'stylists.last_login_at',
            'stylists.created_at',
            'stylists.updated_at',
            'salons.name as salon_name',
            '(SELECT COUNT(`replies`.`id`) FROM `replies` WHERE `replies`.`stylist_id` = `stylists`.`id`) AS reply_count',
            '(SELECT MAX(`replies`.`created_at`) FROM `replies` WHERE `replies`.`stylist_id` = `stylists`.`id`) AS last_replied_at',
        ]);
        $this->join('salons', 'salons.id = stylists.salon_id', 'left');
        $this->where('stylists.id', $stylist_id);
        $this->where('stylists.salon_id', $salon_id);
        $this->where('stylists.online_salon_flag', '0');
        if ( ! is_null($reset_pw_md5))
            $this->where('stylists.reset_pw_md5', $reset_pw_md5);
        $this->where('stylists.deleted_flag', '0');

        if ($this->find())
            return $this->get_row();

        return NULL;
    }

    // -----------------------------------------------------------------------------------------------------

    /**
     * SNS認証時用
     * @param $loginid
     * @return mixed|null
     */
    public function get_login_by_sns_auth($loginid)
    {
        $this->select([
            'stylists.id',
            'stylists.loginid',
            'stylists.name',
            'stylists.salon_id',
            'stylists.status',
            'stylists.agreement_flag',
            'stylists.manager_flag',
            'stylists.online_salon_flag',
            'stylists.online_salon_status',
        ]);
        $this->where('stylists.deleted_flag', '0');

        if ($this->find(['stylists.loginid' => $loginid]))
            return $this->get_row();

        return NULL;
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_stylist_by_reset_pw_md5($reset_pw_md5)
    {
        $this->select([
            'stylists.id',
            'stylists.salon_id',
            'stylists.kana',
            'stylists.name',
            'stylists.loginid',
            'stylists.phone',
            'stylists.note',
            'stylists.status',
            'stylists.manager_flag',
            'stylists.agreement_flag',
            'stylists.reset_pw_limited_at',
            'stylists.created_at',
            'stylists.updated_at',
            'salons.name as salon_name',
        ]);
        $this->join('salons', 'salons.id = stylists.salon_id', 'left');
        $this->where('stylists.reset_pw_md5', $reset_pw_md5);
        $this->where('stylists.deleted_flag', '0');

        if ($this->find())
            return $this->get_row();

        return NULL;
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_any_stylist($loginid)
    {
        $this->select([
            'stylists.id',
            'stylists.salon_id',
            'stylists.kana',
            'stylists.name',
            'stylists.loginid',
            'stylists.phone',
            'stylists.note',
            'stylists.status',
            'stylists.manager_flag',
            'stylists.agreement_flag',
            'stylists.reset_pw_limited_at',
            'stylists.created_at',
            'stylists.updated_at',
            'stylists.deleted_flag',
            'salons.name as salon_name',
            'salons.status as salon_status',
            'salons.deleted_flag as salon_deleted_flag',
        ]);
        $this->join('salons', 'salons.id = stylists.salon_id', 'left');
        $this->where('stylists.loginid', $loginid);

        if ($this->find())
            return $this->get_row();

        return NULL;
    }

    // -----------------------------------------------------------------------------------------------------

    public function auth_login_by_sns_auth($loginid, $auth_provider)
    {
        $this->select([
            'stylists.id',
            'stylists.loginid',
            'stylists.name',
            'stylists.salon_id',
            'stylists.status',
            'stylists.agreement_flag',
            'stylists.manager_flag',
            'sns_authentications.tmp_loginpw',
        ]);
        $this->join('sns_authentications', 'sns_authentications.stylist_id = stylists.id');
        $this->where('sns_authentications.auth_provider', $auth_provider);
        $this->where('stylists.deleted_flag', '0');

        if ($this->find(['stylists.loginid' => $loginid]))
            return $this->get_row();

        return NULL;
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_online_salon_users($search)
    {
        // 当月
        $current_month = $this->escape( date('Y-m-01') );

        $this->select([
            'stylists.id',
            'stylists.loginid',
            'stylists.name',
            'stylists.kana',
            'stylists.status',
            'stylists.online_salon_status',
            'stylists.online_salon_activate_at',
            'stylists.online_salon_charge_ignore_flag',
            'stylists.last_login_at',
            'online_salon_receipts.status AS current_receipt_status',
            'sns_authentications.snsid AS sns_id',
        ]);
        $this->join('sns_authentications', 'sns_authentications.stylist_id = stylists.id AND sns_authentications.auth_provider = "facebook"');
        $this->join('online_salon_receipts', 'online_salon_receipts.stylist_id = stylists.id AND month = '. $current_month, 'left');
        $this->where('stylists.online_salon_flag', '1');
        $this->where('stylists.deleted_flag', '0');

        if( !empty($search['name']) ){
            $this->group_start();
            $this->like('stylists.name', $search['name'] );
            $this->or_like('stylists.kana', $search['name']);
            $this->group_end();
        }

        if( !empty($search['loginid']) ){
            $this->like('stylists.loginid', $search['loginid'] );
        }

        if( !empty($search['status']) ){
            $this->where_in('stylists.online_salon_status', $search['status'] );
        }else{
            $this->where('stylists.online_salon_status IS NULL');
        }

        if( !empty($search['charge_ignore']) ){
            if($search['charge_ignore']=='1')// のみ
                $this->where('stylists.online_salon_charge_ignore_flag','1');
            if($search['charge_ignore']=='2')// 除く
                $this->where('stylists.online_salon_charge_ignore_flag','!=','1');
        }

        $order_by = [
            'stylists.online_salon_activate_at DESC',
            'stylists.created_at DESC',
        ];
        return $this->find( NULL, NULL, $order_by );
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_online_salon_checking_users()
    {
        $this->select([
            'stylists.id',
            'stylists.loginid',
            'stylists.name',
            'stylists.kana',
            'stylists.online_salon_status',
            'stylists.created_at',
            'stylists.online_salon_activate_at',
            'sns_authentications.snsid AS sns_id',
        ]);
        $this->join('sns_authentications', 'sns_authentications.stylist_id = stylists.id AND sns_authentications.auth_provider = "facebook"');
        $this->where('stylists.online_salon_flag', '1');
        $this->where('stylists.online_salon_status', 'checking');
        $this->where('stylists.deleted_flag', '0');

        $order_by = [
            'stylists.created_at DESC',
        ];
        return $this->find( NULL, NULL, $order_by );
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_online_salon_receipt_target_stylists($month){

        log_debug("Stylists_tbl.get_online_salon_receipt_target_stylists({$month}) run.");

        $month = $this->escape($month);
        $this->select([
            'stylists.id',
            'stylists.loginid',
            'stylists.name',
            'sns_authentications.mail',
        ]);
        $this->join('sns_authentications', 'sns_authentications.stylist_id = stylists.id AND sns_authentications.auth_provider = "facebook"');
        $this->join('online_salon_receipts', 'online_salon_receipts.stylist_id = stylists.id AND online_salon_receipts.month = '.$month, 'left');
        $this->where('stylists.online_salon_flag', '1');
        $this->where('stylists.deleted_flag', '0');
        $this->where('stylists.online_salon_status', 'active');
        $this->where('online_salon_receipts.id', NULL );

        return $this->find();

    }

    // -----------------------------------------------------------------------------------------------------

    public function get_online_salon_stylist($stylist_id)
    {
        $this->select([
            'stylists.id',
            'stylists.id AS stylist_id',
            'stylists.loginid',
            'stylists.name',
            'stylists.kana',
            'stylists.status',
            'stylists.online_salon_flag',
            'stylists.online_salon_status',
            'stylists.online_salon_activate_at',
            'stylists.online_salon_charge_ignore_flag',
            'stylists.created_at',
            'sns_authentications.snsid AS sns_id'
        ]);
        $this->join('sns_authentications', 'sns_authentications.stylist_id = stylists.id AND sns_authentications.auth_provider = "facebook"');
        $this->where('stylists.id', $stylist_id);
        $this->where('stylists.online_salon_flag', '1');
        $this->where('stylists.deleted_flag', '0');

        if ($this->find())
            return $this->get_row();

        return NULL;
        
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_unsubscribe_target_stylists( $min_month, $month )
    {

        $this->select([
            'stylists.id',
            'stylists.salon_id',
        ]);
        $this->join('salon_receipts', 'salon_receipts.stylist_id=stylists.id');
        $this->where('salon_receipts.status', 'failed');
        $this->where('salon_receipts.month BETWEEN "'.$min_month.'" and "'.$month.'"');
        $this->where('stylists.stylist_status', 'active');
        $this->where('stylists.deleted_flag', '0');
        $this->group_by('salon_receipts.stylist_id');
        $this->having('count("salon_receipts.stylist_id") >= 3');

        return $this->find();

    }

    // -----------------------------------------------------------------------------------------------------

    
}

