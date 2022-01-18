<?php

class Trial extends MYSALON_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('stylist_model');
        $this->load->model('questionnaire_model');
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    /**
     * トライアル会員登録FORM
     */
    public function form()
    {
        $this->load->config('prefecture');

        $trial = [];

        if ($post = $this->session->flashdata('trial'))
        {
            $trial = array_merge($trial, $post);
        }

        $questionnaires = $this->questionnaire_model->get_questionnaires('0', NULL, ['status' => '1']);
        log_debug($questionnaires);

        $this->view->assign('trial',            $trial);
        $this->view->assign('questionnaires',   $questionnaires);
        $this->view->assign('prefecture', config_item('prefecture'));
    }

    // --------------------------------------------------------------------------------------------------------------------------------

    /**
     * トライアル会員登録申請
     */
    public function create()
    {
        $required = [
            'kana'      => FALSE,
            'name'      => TRUE,
            'loginid'   => TRUE,
            'phone'     => TRUE,
        ];

        $this->_post['phone'] = "{$this->_post['phone1']}-{$this->_post['phone2']}-{$this->_post['phone3']}";
        $this->_post['salon']['phone'] = "{$this->_post['salon']['phone1']}-{$this->_post['salon']['phone2']}-{$this->_post['salon']['phone3']}";

        $this->_post['salon']['fax'] = '';

        if ( ! empty($this->_post['salon']['fax1']) && ! empty($this->_post['salon']['fax2']) && ! empty($this->_post['salon']['fax3']))
        {
            $this->_post['salon']['fax'] = "{$this->_post['salon']['fax1']}-{$this->_post['salon']['fax2']}-{$this->_post['salon']['fax3']}";
        }

        $salon = $this->_post['salon'];

        if ($this->validate('stylist', $required))
        {
            $required = [
                'name'      => TRUE,
                'phone'     => TRUE,
                'fax'       => FALSE,
                'postcode1' => FALSE,
                'postcode1' => FALSE,
                'address'   => FALSE,
                'questionnaire_id' => TRUE,
            ];

            if ($this->validate('salon', $required, $salon))
            {
                if ($stylist = $this->stylist_model->create_trial($this->_post))
                {
                    if ($stylist !== TRUE)
                    {
                        $subject = '[hairlogy] トライアル会員登録申請';
                        $assign_data = [
                            'stylist_name'  => $stylist['name'],
                            'password'      => $stylist['loginpw'],
                            'url' => site_url('/salon/login/form/'.urlencode($stylist['loginid']), 'https'),
                        ];
                        log_debug($assign_data);
                        $this->sendmail($stylist['loginid'], $subject, $assign_data);
                    }

                    $this->redirect("/{$this->_module}/{$this->_class}/thanks/");
                }
                elseif ($error_messages = $this->stylist_model->error_messages())
                {
                    $this->_error_messages = $error_messages;
                }
                else
                {
                    log_error("Failed to create trial stylist.");
                    $this->_error_messages = ['トライアル会員登録に失敗しました。'];
                }
            }
        }

        $this->session->set_flashdata('trial', $this->_post);
        $this->redirect("/{$this->_module}/{$this->_class}/form/");
    }

    // --------------------------------------------------------------------------------------------------------------------------------

    /**
     * トライアル下院登録申請完了画面
     */
    public function thanks()
    {
    }

}

