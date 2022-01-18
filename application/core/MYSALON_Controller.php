<?php

class MYSALON_Controller extends MYWWW_Controller {

    protected $_login = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->_login = $this->session->userdata("{$this->_module}_login");

        if( ! $this->_login && $this->_class != 'login' && $this->_class != 'trial')
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

        if (isset($this->_login))
        {
            log_debug('-Login-----------------------------------------------');
            log_debug($this->_login);
        }
        log_debug('-----------------------------------------------------');
        log_debug("MYSALON_Controller initialized.");

    }

    public function _output($output=NULL)
    {
        $this->view->assign('salon_id', $this->_login['salon_id']);
        $this->view->assign('login',    $this->_login);
        parent::_output($output);
    }

}

