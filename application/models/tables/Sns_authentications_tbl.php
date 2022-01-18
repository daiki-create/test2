<?php

/**
 * Sns_authentications_tbl Class
 *
 *   SNS認証管理テーブル
 *
 * @project     Hairlogy
 * @package     Table model
 * @author      monte.ishida@gmail.com
 * @copyright   montecampo Co., Ltd. All Rights Reserved
 */

class Sns_authentications_tbl extends MY_Table
{
    public function __construct()
    {
        parent::__construct();
    }

    // --------------------------------------------------------------------------------------------

    public function get_sns_authentication($auth_provider, $mail)
    {
        $this->select([
            'sns_authentications.auth_provider',
            'sns_authentications.user_name',
            'sns_authentications.snsid',
            'sns_authentications.mail',
            'sns_authentications.stylist_id',
            'sns_authentications.created_at',
            'stylists.loginid',
        ]);
        $this->join('stylists', 'stylists.id = sns_authentications.stylist_id');

        $where = [
            'auth_provider' => $auth_provider,
            'mail'          => $mail,
        ];

        if ($this->find($where))
            return $this->get_row();

        return NULL;
    }


}
