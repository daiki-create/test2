<?php

require_once APPPATH.'core/MY_Include.php';

/**
 * MY_Controller Class
 *
 * @package     Hairlogy
 * @author      yuki.hatano@gamil.com
 * @category    Core Controller
 * @link        http://
 */

class MY_Controller extends CI_Controller {

    protected $_uri;
    public $_validation_errors = array();
    public $_module;
    public $_class;
    public $_action;
    public $_data_dir;
    public $_tmp_dir;
    public $_user_agent;
    public $_browser;

    public function __construct()
    {
        parent::__construct();
        $this->_uri     = $this->uri->uri_string();
        $this->_module  = CI_MODULE;
        $this->_class   = $this->router->fetch_class();
        $this->_action  = $this->router->fetch_method();

        $this->_data_dir = config_item('data_dir');
        $this->_tmp_dir  = config_item('tmp_dir');

        $this->load->library('MY_validation');
        $this->load->library('validation/common_validation');

        log_debug("MY_Controller Initialized");
    }

    // =======================================================================================================

    /**
     * バリデーション
     */
    protected function validate($validation_name='common', $requires=[], $init_data=NULL)
    {
        log_debug("validate({$validation_name}) run.");
        $Class = ucfirst($validation_name);

        if (file_exists(APPPATH . "libraries/validation/{$Class}_validation.php"))
        {
            if ($validation_name == 'common')
            {
                if ($init_data === NULL)
                    $this->common_validation->initialize($this->_post);
                else
                    $this->common_validation->initialize($init_data);
            }
            else
                $this->load->library("validation/{$Class}_validation", $this->_post);

            $validation = "{$validation_name}_validation";

            if ($init_data !== NULL)
                $this->$validation->initialize($init_data);

            if ($this->$validation->run($requires))
            {
                log_debug("Validation OK.");
                return TRUE;
            }
            else
            {
                log_error('Validation NG.');
                $this->_validation_errors = $this->$validation->errors();
            }
        }
        else
        {
            log_error("Validation Class not found. [{$Class}_validation}]");
        }

        return FALSE;
    }

    /**
     * メール送信
     */
    protected function sendmail($to, $subject='', $assign_data=NULL, $option=NULL)
    {
        log_debug("sendmail() run.");
        $this->load->library('MY_mailer', $option, 'mailer');

        if (ENVIRONMENT == 'development')
        {
            $to = 'ishida.ke.0331@gmail.com';
            $from = 'sysadmin@stg.hairlogy.jp';
        }
        elseif (ENVIRONMENT == 'staging')
        {
            if (defined('DEV_MAIL_TO'))
                $to = DEV_MAIL_TO;

            $from = 'hairlogy@montecampo.co.jp';
        }
        elseif (ENVIRONMENT != 'production')
        {
            if (defined('DEV_MAIL_TO'))
                $to = DEV_MAIL_TO;
            else
                $to = 'yuki.hatano@gmail.com';

            $from = 'sysadmin@stg.hairlogy.jp';
        }
        else
        {
            $from = 'hairlogy@montecampo.co.jp';
        }

        $this->mailer->set_subject($subject);
        $this->mailer->set_to($to);
        $this->mailer->set_from($from);
        $this->mailer->set_body_by_smarty("{$this->_module}/{$this->_class}/{$this->_action}.tpl", $assign_data, 'text');
        $this->mailer->send();
    }

    /**
     * メール送信（config主体バージョン）
     * @param $conf
     * @param null $assign_data
     * @param null $option
     * @return false
     */
    protected function sendmail_by_conf( $conf, $assign_data=NULL, $option=NULL){

        log_debug( "sendmail_by_conf() run." );
        log_debug( $conf );
        $this->load->library('MY_mailer', $option, 'mailer');

        if( empty( $conf['to'] ) || empty( $conf['from'] ) ){
            log_error( 'mail send address not found' );
            return FALSE;
        }
        $template = $conf['template'] ?? "{$this->_module}/{$this->_class}/{$this->_action}.tpl";

        $this->mailer->set_subject( $conf['subject'] ?? 'unknown' );
        $this->mailer->set_to( $conf['to'] );
        $this->mailer->set_from( $conf['from'] );
        $this->mailer->set_body_by_smarty( $template, $assign_data, 'text' );
        $this->mailer->send();

    }


}

