<?php

class MYDL_Controller extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('session');
        $this->load->driver('excache', array('adapter' => 'apcu',), 'apcu');

        $this->_client_ip   = $this->input->ip_address();
        $this->_user_agent  = $this->input->user_agent();
        $this->_referer     = $this->input->get_request_header('REFERER');

        define('APC_TTL', ENVIRONMENT == 'production' ? 60 * 60 * 24 : 180);

        log_debug('-----------------------------------------------------');
        log_debug('MYDL_Controller -------------------------------------');
        log_debug('-----------------------------------------------------');
        log_debug(" ClientIP: {$this->_client_ip}");
        log_debug("     HOST: ". base_url());
        log_debug("      URI: /{$this->_uri}");
        log_debug("   Module: {$this->_module}");
        log_debug("    Class: {$this->_class}");
        log_debug("   Action: {$this->_action}");
        log_debug("  Referer: {$this->_referer}");
        log_debug("UserAgent: {$this->_user_agent}");
        log_debug('-----------------------------------------------------');
    }

    protected function output($file, $mime_type, $force=FALSE)
    {
        if (file_exists($file))
        {
            if ($force === TRUE)
            {
                $this->load->helper('download');
                force_download($file, NULL);
            }
            else
            {
                log_debug("file path: {$file}");
                log_debug("mime type: {$mime_type}");
                log_debug("file size: ".filesize($file));
                log_debug('-----------------------------------------------------');
                $this->output->set_header("Content-Type: {$mime_type}");
                $this->output->set_header("Content-Length: ". filesize($file));
                $this->output->set_output( read_file($file) );
            }
        }
        elseif ($mime_type == 'image/png')
        {
            log_error("File Not Found !!! [{$file}]");
            $file = APPPATH . '../data/not_found_image.png';
            $this->output->set_header("Content-Type: {$mime_type}");
            $this->output->set_header("Content-Length: ". filesize($file));
            $this->output->set_output( read_file($file) );
        }
    }

}

