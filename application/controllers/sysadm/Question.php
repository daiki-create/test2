<?php

class Question extends MYSYSADM_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('questionnaire_model');
    }

    // -----------------------------------------------------------------------------------------------------------

    public function index()
    {
    }

    // -----------------------------------------------------------------------------------------------------------

    public function create()
    {
        $this->redirect("/{$this->_module}/{$this->_class}/index/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function update()
    {
        $this->redirect("/{$this->_module}/{$this->_class}/index/");
    }

    // -----------------------------------------------------------------------------------------------------------

    public function delete()
    {
        $this->redirect("/{$this->_module}/{$this->_class}/index/");
    }

}

