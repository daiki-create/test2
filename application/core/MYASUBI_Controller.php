<?php

class MYASUBI_Controller extends MYWWW_Controller {

    protected $_login = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->_login = $this->session->userdata("{$this->_module}_login");

        if( !$this->_login && ( empty( $this->_class ) || !in_array($this->_class,['index','login','inquiry', 'info'] )))
        {
            log_error("Unauthorized !");
            $this->redirect("/{$this->_module}/login");
        }

        if (isset($this->_login))
        {
            log_debug('-Login-----------------------------------------------');
            log_debug($this->_login);

            if( empty( $this->_login['online_salon_flag'] ) ){
                log_error("Not online salon user !");
                $this->session->unset_userdata("{$this->_module}_login");
                $this->redirect("/{$this->_module}/login");
            }

            // 最新のユーザ情報に更新
            $this->load->model('online_salon_model');
            $this->_login = $this->online_salon_model->get_online_salon_stylist($this->_login['stylist_id']);
            if( empty( $this->_login ) ){
                $this->session->unset_userdata("{$this->_module}_login");
                $this->redirect("/{$this->_module}/login");
            }
            $this->session->set_userdata("{$this->_module}_login", $this->_login);


        }
        log_debug('-----------------------------------------------------');
        log_debug("MYASUBI_Controller initialized.");

    }

    public function _output($output=NULL)
    {
        $this->view->assign('login', $this->_login);
        parent::_output($output);
    }

}

