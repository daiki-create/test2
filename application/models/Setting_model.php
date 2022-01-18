<?php

class Setting_model extends MY_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tables/settings_tbl');
    }

    /*
    public function set_terms_of_service($terms_of_service)
    {
        log_debug("Setting_model.set_terms_of_service() run.");
        $this->settings_tbl->initialize('master');
        $data = ['name' => 'terms_of_service', 'val' => $terms_of_service];
        return $this->settings_tbl->replace($data) !== FALSE;
    }

    public function get_terms_of_service()
    {
        $this->settings_tbl->initialize('master');
        $this->settings_tbl->select(['val']);
        $this->settings_tbl->where('name', 'terms_of_service');

        if ($this->settings_tbl->find())
        {
            return $this->settings_tbl->get('terms_of_service');
        }

        return '';
    }
    */

}

