<?php

class Salon extends MYAJAX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('salon_model');
    }

    // ----------------------------------------------------------------------------------------------------------------------------

    public function get_salons($offset='0')
    {
        $status = 'NG';
        $data = $pagination = [];

        if ($salons = $this->salon_model->get_salons($offset, '1'))
        {
            if ($offset == '0')
            {
                array_unshift($salons, [
                    'id'    => '0',
                    'name'  => '無所属',
                    'phone' => '',
                    'address' => '',
                ]);
                log_debug($salons);
            }

            $status = 'OK';
            $data['salons'] = $salons;
            $pagination = $this->salon_model->pagination();
        }


        $this->response($status, $data, $pagination);
    }

    // ----------------------------------------------------------------------------------------------------------------------------

}

