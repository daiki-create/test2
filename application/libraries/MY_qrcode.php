<?php

require_once APPPATH.'includes/phpqrcode/qrlib.php';

class MY_qrcode {

    private $_qr_version = '3';
    private $_ec_level   = QR_ECLEVEL_M;
    private $_qr_margin  = '4';

    public function __construct($config=array())
    {
        $this->initialize($config);
    }

    public function initialize($config=array())
    {
        if (isset($config['qr_version']))
            $this->_qr_version = $config['qr_version'];

        if (isset($config['qr_margin']))
            $this->_qr_margin = $config['qr_margin'];

        if (isset($config['ec_level']))
        {
            if ($config['ec_level'] == 'L' OR $config['ec_level'] == 'l')
                $this->_ec_level = QR_ECLEVEL_L;
            elseif ($config['ec_level'] == 'M' OR $config['ec_level'] == 'm')
                $this->_ec_level = QR_ECLEVEL_M;
            elseif ($config['ec_level'] == 'Q' OR $config['ec_level'] == 'q')
                $this->_ec_level = QR_ECLEVEL_Q;
            elseif ($config['ec_level'] == 'H' OR $config['ec_level'] == 'h')
                $this->_ec_level = QR_ECLEVEL_H;
        }
    }

    public function generate($text, $output_path=NULL)
    {
        if ( ! is_null($output_path) && file_exists($output_path))
            @unlink($output_path);

        QRcode::png($text, $output_path, $this->_ec_level, $this->_qr_version, $this->_qr_margin);

        if ( ! is_null($output_path))
        {
            if (file_exists($output_path))
                return TRUE;
            else
                return FALSE;
        }
    }

}

