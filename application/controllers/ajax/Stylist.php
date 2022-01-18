<?php

class Stylist extends MYAJAX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('stylist_model');
    }

    /*
    public function get_stylists($salon_id=NULL)
    {
        $status = 'NG';
        $data = NULL;

        if ($stylists = $this->stylist_model->get_stylists($salon_id))
        {
            $status = 'OK';
            $data['stylists'] = $stylists;
        }

        $this->response($status, $data);
    }
    */

    // ----------------------------------------------------------------------------------------------------------------------------

    public function get_stylist($salon_id=NULL, $stylist_id=NULL)
    {
        $status = 'NG';
        $data = NULL;

        if ($stylist = $this->stylist_model->get_stylist($salon_id, $stylist_id))
        {
            log_debug($stylist);
            $this->load->model('questionnaire_model');
            $status = 'OK';
            $data['stylist'] = $stylist;
            $data['questionnaires'] = $this->questionnaire_model->get_my_questionnaires($stylist_id, $salon_id);
        }

        $this->response($status, $data);
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function create_stylist()
    {
        $status = 'NG';
        $data = NULL;

        $required = [
            'salon_id'  => TRUE,
            'kana'      => FALSE,
            'name'      => TRUE,
            'loginid'   => TRUE,
            'phone'     => FALSE,
            'trial_limited_on' => FALSE,
            'note'      => FALSE,
        ];

        if ( ! empty($this->_post['phone1']))
        {
            $this->_post['phone'] = "{$this->_post['phone1']}-{$this->_post['phone2']}-{$this->_post['phone3']}";
        }

        if ($this->validate('stylist', $required))
        {
            if ($stylist = $this->stylist_model->create_stylist($this->_post['salon_id'], $this->_post))
            {
                $assign_data = [
                    'name'      => $stylist['name'],
                    'loginid'   => $stylist['loginid'],
                    'loginpw'   => $stylist['loginpw'],
                    'login_url' => site_url().'salon/login/form/'.urlencode($stylist['loginid']),
                ];

                $this->sendmail($stylist['loginid'], 'hairlogyへようこそ', $assign_data);

                $status = 'OK';
                $data['stylist'] = $stylist;
            }
            else
            {
                $this->error_response('DB', $this->stylist_model->error_messages());
            }
        }

        $this->response($status, $data);
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function update_stylist()
    {
        $status = 'NG';
        $data = NULL;

        $required = [
            'salon_id'  => TRUE,
            'stylist_id'=> TRUE,
            'kana'      => FALSE,
            'name'      => TRUE,
            'loginid'   => TRUE,
            'phone'     => FALSE,
            'trial_limited_on' => FALSE,
            'note'      => FALSE,
        ];

        if ( ! empty($this->_post['phone1']))
        {
            $this->_post['phone'] = "{$this->_post['phone1']}-{$this->_post['phone2']}-{$this->_post['phone3']}";
        }

        if (empty($this->_post['manager_flag']))
        {
            $this->_post['manager_flag'] = '0';
        }

        if ($this->validate('stylist', $required))
        {
            if ($stylist = $this->stylist_model->update_stylist($this->_post['salon_id'], $this->_post['stylist_id'], $this->_post))
            {
                $status = 'OK';
                $data['stylist'] = $stylist;
            }
            else
            {
                $this->error_response('DB', $this->stylist_model->error_messages());
            }
        }

        $this->response($status, $data);
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    /**
     * パスワード初期化
     */
    public function init_stylist($salon_id=NULL)
    {
        $status = 'NG';
        $data = NULL;

        $required = [
            'salon_id'  => TRUE,
            'stylist_id'=> TRUE,
        ];

        $this->_post['salon_id'] = $salon_id;

        if ($this->validate('stylist', $required) && $stylist = $this->stylist_model->get_stylist($salon_id, $this->_post['stylist_id']))
        {
            if ($loginpw = $this->stylist_model->update_stylist_status($this->_post['salon_id'], $this->_post['stylist_id'], '0', TRUE))
            {
                $assign_data = [
                    'name'      => $stylist['name'],
                    'loginid'   => $stylist['loginid'],
                    'loginpw'   => $loginpw,
                    'login_url' => site_url().'salon/login/form/'.urlencode($stylist['loginid']),
                ];

                $this->sendmail($stylist['loginid'], 'hairlogyへようこそ', $assign_data);
                log_debug($loginpw);
                $status = 'OK';
            }
            else
            {
                $this->error_response('DB', $this->stylist_model->error_messages());
            }
        }

        $this->response($status, $data);
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function update_status($salon_id=NULL)
    {
        $status = 'NG';
        $data = NULL;

        $required = [
            'salon_id'  => TRUE,
            'stylist_id'=> TRUE,
            'status'    => TRUE,
        ];

        $this->_post['salon_id'] = $salon_id;

        if ($this->validate('stylist', $required) && $stylist = $this->stylist_model->get_stylist($salon_id, $this->_post['stylist_id']))
        {
            if ($this->stylist_model->update_stylist_status($salon_id, $this->_post['stylist_id'], $this->_post['status']))
            {
                $status = 'OK';
            }
            else
            {
                $this->error_response('DB', $this->stylist_model->error_messages());
            }
        }

        $this->response($status, $data);
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function delete_stylist($salon_id=NULL)
    {
        $status = 'NG';
        $data = NULL;

        $required = [
            'salon_id'  => TRUE,
            'stylist_id'=> TRUE,
        ];

        $this->_post['salon_id'] = $salon_id;

        if ($this->validate('stylist', $required) && $stylist = $this->stylist_model->get_stylist($salon_id, $this->_post['stylist_id']))
        {
            if ($this->stylist_model->delete_stylist($this->_post['salon_id'], $this->_post['stylist_id']))
            {
                $status = 'OK';
            }
            else
            {
                $this->error_response('DB', $this->stylist_model->error_messages());
            }
        }

        $this->response($status, $data);
    }

}

