<?php

class MYSYSADM_Controller extends MYWWW_Controller {

    protected $_login = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->_login = $this->session->userdata("{$this->_module}_login");

        if( ! $this->_login && $this->_class != 'login')
        {
            if ($this->input->method() == 'post')
            {
                $this->session->set_userdata("{$this->_module}_request_uri", $this->_referer);
            }
            else
            {
                $this->session->set_userdata("{$this->_module}_request_uri", $this->_uri);
            }

            log_error("Unauthorized !");
            $this->redirect("/{$this->_module}/login/");
        }
        elseif ($this->_login['status'] == '-1' && $this->_class != 'mypage' && $this->_class != 'login')
        {
            $this->redirect("/{$this->_module}/mypage/welcome/");
        }

        if (isset($this->_login))
        {
            log_debug('-Login-----------------------------------------------');
            log_debug($this->_login);
        }
        log_debug('-----------------------------------------------------');
        log_debug("MYSYSADM_Controller initialized.");

    }

}

