<?php

class Admin_model extends MY_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tables/administrators_tbl');
    }

    // -------------------------------------------------------------------------------------------------

    public function authenticate($loginid, $loginpw)
    {
        $this->administrators_tbl->initialize();

        if ($admin = $this->administrators_tbl->get_login($loginid))
        {
            if ($admin['status'] != '0' && password_verify($loginpw, $admin['loginpw']))
            {
                $login = [
                    'admin_id'  => $admin['id'],
                    'name'      => $admin['name'],
                    'status'    => $admin['status'],
                ];
                log_debug("Login OK.");
                log_debug($login);
                return $login;
            }
        }

        return FALSE;
    }

    // -------------------------------------------------------------------------------------------------

    public function get_login($loginid)
    {
        $this->administrators_tbl->initialize();

        if ($admin = $this->administrators_tbl->get_login($loginid))
        {
            $admin['admin_id'] = $admin['id'];
            unset($admin['loginpw'], $admin['id']);
            return $admin;
        }

        return NULL;
    }

    // -------------------------------------------------------------------------------------------------

    public function get_admin($admin_id)
    {
        $this->administrators_tbl->initialize();

        if ($admin = $this->administrators_tbl->get_admin($admin_id))
        {
            $admin['admin_id'] = $admin['id'];
            unset($admin['loginpw'], $admin['id']);
            return $admin;
        }

        return NULL;
    }

    // -------------------------------------------------------------------------------------------------

    public function get_admins()
    {
        $this->administrators_tbl->initialize();
        return $this->administrators_tbl->get_admins();
    }

    // -------------------------------------------------------------------------------------------------

    /**
     * システム管理者登録
     */
    public function create_admin($loginid, $name, $force=FALSE)
    {
        $this->administrators_tbl->initialize();

        $loginpw = random_string('alpha', 8);

        $administrator = [
            'loginid'   => $loginid,
            'loginpw'   => password_hash($loginpw, PASSWORD_DEFAULT),
            'name'      => $name,
            'status'    => '1',
        ];

        if ( ! $force)
        {
            $administrator['status'] = '-1';
        }

        if ($this->administrators_tbl->insert_update($administrator, ['loginid' => $loginid]))
        {
            $administrator['loginpw'] = $loginpw;;
            return $administrator;
        }

        return NULL;
    }

    // -------------------------------------------------------------------------------------------------

    /**
     * システム管理者更新
     */
    public function update_admin($admin_id, $admin)
    {
        $this->administrators_tbl->initialize();
        $administrator = [
            'loginid' => $admin['loginid'],
            'name'    => $admin['name'],
        ];

        if (empty($admin['status']))
            $administrator['status'] = '0';
        else
            $administrator['status'] = '1';

        if (isset($admin['loginpw']) && is_string($admin['loginpw']) && strlen($admin['loginpw']) > 5)
        {
            $administrator['loginpw'] = password_hash($admin['loginpw'], PASSWORD_DEFAULT);
        }
        
        return ($this->administrators_tbl->update($administrator, ['id' => $admin_id]) !== FALSE);
    }

    // -------------------------------------------------------------------------------------------------

    /**
     *
     */
    public function update_loginpw($admin_id, $loginpw, $status=NULL)
    {
        $this->administrators_tbl->initialize();
        $administrator = ['loginpw' => password_hash($loginpw, PASSWORD_DEFAULT)];

        if ($status !== NULL)
        {
            $administrator['status'] = $status;
        }

        return ($this->administrators_tbl->update($administrator, ['id' => $admin_id]) !== FALSE);
    }

    // -------------------------------------------------------------------------------------------------

    public function delete_admin($admin_id)
    {
        log_debug("Administrator_model.delete_admin({$admin_id}) run.");

        $this->administrators_tbl->initialize();
        return $this->administrators_tbl->delete(['id' => $admin_id]) !== FALSE;
    }

    // -------------------------------------------------------------------------------------------------

    /**
     * ログインパスワード初期化
     *
     * @param   int     $admin_id
     */
    public function reset_loginpw($admin_id)
    {
        log_debug("Administrator_model.reset_loginpw({$admin_id}) run.");

        $this->administrators_tbl->initialize();

        if ($admin = $this->administrators_tbl->get_admin($admin_id))
        {
            $loginpw = random_string('alpha', 8);
            $loginpw_hash = password_hash($loginpw, PASSWORD_DEFAULT);

            if ($this->administrators_tbl->update(['loginpw' => $loginpw_hash, 'status' => '-1'], ['id' => $admin_id]) !== FALSE)
            {
                return [
                    'name'      => $admin['name'],
                    'loginid'   => $admin['loginid'],
                    'loginpw'   => $loginpw,
                ];
            }
        }

        return FALSE;
    }

}

