<?php

require_once APPPATH.'core/MY_Table.php';

/**
 * MY_Model Class
 *
 * @package     Hairlogy
 * @category    Core Model
 * @author      yuki.hatano@gmail.com
 */

class MY_Model extends CI_Model {

    public $duplicate = FALSE;

    protected $_login;
    protected $_data_dir;
    protected $_master = 'master';
    protected $_slave  = 'slave1';
    protected $_user_agent = '';
    protected $_browser = '';

    private $_error_messages = [];

    private $_pagination = '';

    // --------------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        $CI =& get_instance();
        $this->_login       = isset($CI->_login) ? $CI->_login : NULL;
        $this->_data_dir    = $CI->_data_dir;
        $this->_client_ip   = isset($CI->_client_ip)  ? $CI->_client_ip: '';
        $this->_user_agent  = isset($CI->_user_agent) ? $CI->_user_agent: '';
        $this->_browser     = isset($CI->_browser)    ? $CI->_browser: '';

        $name = get_called_class();
        log_debug("{$name} Class Initialized... ");
    }

    // --------------------------------------------------------------------------------

    public function pagination($pagination_link=NULL)
    {
        if ( ! is_null($pagination_link))
        {
            $this->_pagination = $pagination_link;
        }
        return $this->_pagination;
    }

    // --------------------------------------------------------------------------------

    /**
     * Sanitize offset
     *
     * @param   int $val        offset値
     * @param   int $default    default値
     */
    protected function sanitize_offset(&$val, $default='0')
    {
        if (filter_var($val, FILTER_VALIDATE_INT) === FALSE)
        {
            if (filter_var($default, FILTER_VALIDATE_INT) === FALSE)
            {
                log_debug("sanitized offset: 0");
                $val = '0';
            }
            else
            {
                log_debug("sanitized offset: {$default}");
                $val = $default;
            }
        }
    }

    // --------------------------------------------------------------------------------

    /**
     * Sanitize limit
     *
     * @param   int $val        limit値
     * @param   int $default    default値
     */
    protected function sanitize_limit(&$val, $default='20')
    {
        if (filter_var($val, FILTER_VALIDATE_INT) === FALSE)
        {

            if (filter_var($default, FILTER_VALIDATE_INT) === FALSE)
            {
                log_debug("sanitized limit: 10");
                $val = '10';
            }
            else
            {
                log_debug("sanitized limit: {$default}");
                $val = $default;
            }
        }
    }

    // --------------------------------------------------------------------------------

    /**
     * return error messages
     */
    public function error_messages($error_msgs=NULL)
    {
        if (is_array($error_msgs))
        {
            $this->_error_messages = $error_msgs;
        }
        elseif (is_string($error_msgs))
        {
            $this->_error_messages[] = $error_msgs;
        }

        return $this->_error_messages;
    }

    // --------------------------------------------------------------------------------

    /**
     * CSVファイルからデータロード
     */
    protected function load_csv($csv_file)
    {
        if (file_exists($csv_file))
        {
            $csv = new SplFileObject($csv_file);
            $csv->setFlags(SplFileObject::READ_CSV);
            return $csv;
        }

        return FALSE;
    }

    // --------------------------------------------------------------------------------

    /**
     * データをCSVファイルへ出力
     */
    protected function output_csv($csv_file, $data)
    {
        $file = new SplFileObject($csv_file, 'w');

        foreach ($data as $line)
        {
            $file->fputcsv($line);
        }
    }

}

