<?php

class Image extends MYDL_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function qr_code($salon_id=NULL, $code=NULL)
    {
        $qr_image = "{$this->_data_dir}/qr_code/{$salon_id}/{$code}.png";
        $this->output($qr_image, 'image/png');
    }

}
