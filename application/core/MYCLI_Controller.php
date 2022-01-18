<?php
/**
 * MYCLI_Controller Class
 *
 * @package     Hairlogy
 * @category    Core Controller
 * @author      yuki.hatano@gmail.com
 */

class MYCLI_Controller extends MY_Controller {

    public $_login;

    // ----------------------------------------------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->_login = array(
            'admin_id'  => 0,
            'admin_name'=> 'batch',
        );
        $this->_module = 'cli';

        log_debug('-----------------------------------------------------');
        log_debug('- MYCLI_Controller run');
        log_debug('-----------------------------------------------------');

        if (is_cli())
        {
            log_debug("     URI: {$this->_uri}");
            log_debug("  Module: {$this->_module}");
            log_debug("   Class: {$this->_class}");
            log_debug("  Action: {$this->_action}");
            log_debug('-----------------------------------------------------');
        }
        else
        {
            log_error("The CLI Controller is not allowed from HTTP Request.");
            show_error('Permission Denied.');
        }
    }

    // ----------------------------------------------------------------------------------------------------------------

    public function _output($output=NULL)
    {
        if (is_array($output))
        {
            print_r($output);
        }
        elseif (is_string($output))
        {
            echo $output;
        }
        else
        {
            log_error('Invalid Output !');
            exit(1);
        }
    }

}

