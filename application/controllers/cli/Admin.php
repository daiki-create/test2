<?php

class Admin extends MYCLI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function create($loginid=NULL, $name=NULL)
    {
        $this->load->model('admin_model');

        if ($admin = $this->admin_model->create_admin($loginid, $name, TRUE))
        {
            echo "Succeeded to create Administrator.\n";
            var_export($admin);
        }
        else
        {
            echo "Failed to create Administrator !!\n";
        }
    }

}

