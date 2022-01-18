<?php
/**
 * Administrators_tbl Class
 *
 *   システム管理者テーブル
 *
 * @project     Hairlogy
 * @package     Table model
 * @author      yuki.hatano@gmail.com
 * @copyright   montecampo Co., Ltd. All Rights Reserved
 */

class Administrators_tbl extends MY_Table {

    public function __construct()
    {
        parent::__construct();
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_login($loginid)
    {
        $this->select(['id', 'loginid', 'loginpw', 'name', 'status']);

        if ($this->find(['loginid' => $loginid]))
        {
            return $this->get_row();
        }

        return NULL;
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_admin($admin_id)
    {
        $this->select(['id', 'loginid', 'name', 'status']);

        if ($this->find(['id' => $admin_id]))
        {
            return $this->get_row();
        }

        return NULL;
    }

    // -----------------------------------------------------------------------------------------------------

    public function get_admins()
    {
        $this->select(['id', 'loginid', 'name', 'status']);
        return $this->find();
    }

}

