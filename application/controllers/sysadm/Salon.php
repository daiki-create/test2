<?php

class Salon extends MYSYSADM_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->config('prefecture');
        $this->load->model('salon_model');
        $this->load->model('stylist_model');
    }

    // -----------------------------------------------------------------------------------------------------------

    public function index($offset='0')
    {
        $salons = $this->salon_model->get_salons($offset, NULL);
        $pagination = $this->salon_model->pagination();
        log_debug($salons);
        $this->view->assign('salons', $salons);
        $this->view->assign('pagination', $pagination);
        $this->view->assign('prefecture', config_item('prefecture'));

    }

    // -----------------------------------------------------------------------------------------------------------

    public function detail($salon_id=NULL)
    {
        $this->load->model('questionnaire_model');
        $salon = $this->salon_model->get_salon($salon_id);
        $this->_convert_salon($salon);
        $stylists = $this->stylist_model->get_stylists($salon_id);
        $questionnaires = $this->questionnaire_model->get_questionnaires('0');
        log_debug($salon);
        //log_debug($stylists);

        $this->view->assign('salon',    $salon);
        $this->view->assign('stylists', $stylists);
        $this->view->assign('questionnaires', $questionnaires);
        $this->view->assign('salon_id', $salon_id);
        $this->view->assign('prefectures',    config_item('prefecture'));
        $this->js_data(['salon_id' => $salon_id]);
    }

    // -----------------------------------------------------------------------------------------------------------

    public function form($salon_id=NULL)
    {
        $this->detail($salon_id);

        if (empty($salon_id))
            $this->view->assign('salon', $this->session->flashdata('salon'));
    }

    // -----------------------------------------------------------------------------------------------------------

    public function update($salon_id=NULL)
    {
        if (empty($salon_id))
            $this->redirect("/{$this->_module}/{$this->_class}/");

        $required = [
            'name'      => TRUE,
            'postcode1' => FALSE,
            'postcode2' => FALSE,
            'address1'  => FALSE,
            'address2'  => FALSE,
            'phone'     => FALSE,
            'fax'       => FALSE,
            'mail'      => FALSE,
            'representative'   => FALSE,
            'questionnaire_id' => TRUE,
        ];

        $this->_convert_salon($this->_post, TRUE);

        if ($this->validate('salon', $required))
        {
            if ($this->salon_model->update_salon($salon_id, $this->post()))
            {
                $this->_messages = '更新しました。';
                $this->redirect("/{$this->_module}/{$this->_class}/detail/{$salon_id}");
            }
            else
            {
                $this->_error_messages = '更新に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/form/{$salon_id}");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function create()
    {
        $required = [
            'name'      => TRUE,
            'postcode1' => FALSE,
            'postcode2' => FALSE,
            'address1'  => FALSE,
            'address2'  => FALSE,
            'phone'     => FALSE,
            'fax'       => FALSE,
            'mail'      => FALSE,
            'representative'   => FALSE,
            'questionnaire_id' => TRUE,
        ];

        $this->_convert_salon($this->_post, TRUE);
        $salon = $this->post();

        if ($this->validate('salon', $required))
        {
            if ($salon_id = $this->salon_model->create_salon($salon))
            {
                $this->_messages = '新規登録しました。';
                $this->redirect("/{$this->_module}/{$this->_class}/detail/{$salon_id}");
            }
            else
            {
                $this->_error_messages = '新規登録に失敗しました。';
            }
        }

        $this->session->set_flashdata('salon', $salon);
        $this->redirect("/{$this->_module}/{$this->_class}/form/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function update_status($salon_id=NULL)
    {
        if ($this->salon_model->update_status($salon_id, $this->_post['status']))
        {
            $this->_messages = 'ステータスを変更しました。';
        }
        else
        {
            $this->_error_messages = 'ステータスを変更に失敗しました。';
        }

        $this->redirect("/{$this->_module}/{$this->_class}/detail/{$salon_id}");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function change_salon($salon_id=NULL)
    {
        $required = [
            'stylist_id'   => TRUE,
            'new_salon_id' => TRUE,
        ];

        if ($this->validate('stylist', $required))
        {
            if ($this->stylist_model->belong_to($this->_post['new_salon_id'], $this->_post['stylist_id']))
            {
                $this->_messages = '所属サロンを変更しました。';
            }
            else
            {
                $this->_error_messages = '所属サロンの変更に失敗しました。';
            }
        }

        $this->redirect("/{$this->_module}/{$this->_class}/detail/{$salon_id}");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function delete()
    {
        if ($this->salon_model->delete_salon($this->_post['salon_id']))
        {
            $this->_messages = '削除しました。';
            $this->redirect("/{$this->_module}/{$this->_class}/index/");
        }
        else
        {
            $this->_error_messages = '削除に失敗しました。';
        }

        $this->redirect("/{$this->_module}/{$this->_class}/detail/{$this->_post['salon_id']}");
    }

    // -----------------------------------------------------------------------------------------------------------

    private function _convert_salon(&$salon, $input=FALSE)
    {
        if ($input)
        {
            if (strlen($salon['phone'][0]) > 0 OR strlen($salon['phone'][1]) > 0 OR strlen($salon['phone'][2]) > 0)
                $salon['phone'] = implode('-', $salon['phone']);
            else
                $salon['phone'] = NULL;

            if (strlen($salon['fax'][0]) > 0 OR strlen($salon['fax'][1]) > 0 OR strlen($salon['fax'][2]) > 0)
                $salon['fax'] = implode('-', $salon['fax']);
            else
                $salon['fax'] = NULL;
        }
        else
        { // output
            if ( ! empty($salon['phone']))
                $salon['phone'] = explode('-', $salon['phone']);

            if ( ! empty($salon['fax']))
                $salon['fax'] = explode('-', $salon['fax']);
        }
    }

}

