<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package     CI-GUIDE.INFO
 * @author      ci-guide.info Dev Team
 * @copyright   Copyright (c) 2012, www.ci-guide.info
 * @license     http://codeigniter.com/user_guide/license.html
 * @link        http://www.ci-guide.info
 * @since       Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter-Guide Cig_http_client Class
 *
 * HTTP-Client Library
 *
 * @package     CI-GUIDE.INFO
 * @subpackage  Libraries
 * @category    Libraries
 * @author      ci-guide.info Dev Team
 * @link        http://www.ci-guide.info/download/library/http-client/
 */
class MY_http_client {

    private $multi_request_mode;

    private $multi_key;

    private $client_data;

    private $default_config = array(
        'verbose'         => FALSE,
        'url'             => NULL,
        'method'          => 'get',
        'query'           => '',
        'headers'         => array('Expect' => ''),
        'cookies'         => array(),
        'cookie_file'     => NULL,
        'user_agent'      => NULL,
        'connect_timeout' => 5,
        'timeout'         => 10,
        'username'        => NULL,
        'password'        => NULL,
        'proxy_host'      => NULL,
        'proxy_port'      => NULL,
        'proxy_id'        => NULL,
        'proxy_pw'        => NULL,
        'auto_redirect'   => FALSE,
        'auto_referer'    => FALSE,
        'max_redirects'   => NULL,
        'upload_file'     => NULL,
        'curl_options'    => array(),
    );

    private $_config = array();

    // -------------------------------------------------------------------------------------

    /**
     * A constructor.
     * Any default configurations can be overridden with a given parameter.
     */
    public function __construct($config=array())
    {
        $this->initialize($config);
        log_message('debug', "MY_HTTP_Client Class Initialized");
    }

    // -------------------------------------------------------------------------------------

    /**
     * Initialize this object.
     */
    public function initialize($config=array(), $multi_request_mode=FALSE)
    {
        $this->_config = $this->default_config;

        if ( ! empty($config))
        {
            foreach ($config as $key => $value)
            {
                $this->_config[$key] = $value;
            }
        }

        $this->multi_request_mode = $multi_request_mode;
        $this->multi_key = NULL;
        $this->client_data = array();
        return $this;
    }

    /**
     * Set a multi-request key to be used from now on until another key is supplied.
     * When in a multi-request mode, $multi_key must be set to a non-NULL value by calling this function,
     * before any operations except initialize().
     */
    public function multi_key($multi_key)
    {
        if ( ! $this->multi_request_mode || $multi_key === NULL)
        {
            return FALSE;
        }
        $this->multi_key = $multi_key;
        return $this;
    }

    // -------------------------------------------------------------------------------------

    /**
     * Get a value that holds client data.
     */
    private function &get_client_data($multi_key=NULL)
    {
        if ($this->multi_request_mode xor $multi_key !== NULL)
        {
            // $multi_key must be a non-NULL value if and only if in a multi-request mode.
            return NULL;
        }

        if ( ! isset($this->client_data[$multi_key]))
        {
            $this->client_data[$multi_key] = new stdClass();
            $this->client_data[$multi_key]->config = $this->_config;
            $this->client_data[$multi_key]->response = array();
        }
        return $this->client_data[$multi_key];
    }

    /**
     * Get all multi-request keys set after the initialization.
     */
    private function get_multi_keys()
    {
        if ( ! $this->multi_request_mode)
        {
            return FALSE;
        }
        return array_keys($this->client_data);
    }

    // -------------------------------------------------------------------------------------

    /**
     * Set a configuration value for a given key, or multiple key-value pairs.
     * If the first argument is an array, the second argument is ignored.
     */
    public function set_config($key_or_array, $value=NULL)
    {
        if ( ! is_array($key_or_array))
        {
            $key_or_array = array($key_or_array => $value);
        }

        foreach ($key_or_array as $key => $value)
        {
            switch ($key)
            {
                case 'url':
                    $this->set_url($value);
                    break;
                case 'method':
                    $this->set_method($value);
                    break;
                case 'query':
                    $this->set_query($value);
                    break;
                case 'headers':
                    $this->set_http_header($value);
                    break;
                case 'cookies':
                    $this->set_request_cookie($value);
                    break;
                case 'curl_options':
                    $this->set_curl_option($value);
                    break;
                default:
                    $this->_set_config($key, $value);
                    break;
            }
        }
        return $this;
    }

    /**
     * Get a configuration value for a given key.
     */
    public function get_config($key)
    {
        return $this->get_client_data($this->multi_key)->config[$key];
    }

    private function _set_config($key, $value)
    {
        $this->get_client_data($this->multi_key)->config[$key] = $value;
    }

    // -------------------------------------------------------------------------------------

    /**
     * Set a request url.
     */
    public function set_url($url)
    {
        $this->_set_config('url', $url);
        return $this;
    }

    // -------------------------------------------------------------------------------------

    /**
     * Set a request method. (POST or GET)
     */
    public function set_method($method)
    {
        $this->_set_config('method', $method);
        return $this;
    }

    // -------------------------------------------------------------------------------------

    /**
     * Set request queries.
     * If $query is an array and $url_encoded is TRUE, then names and values are urlencoded.
     */
    public function set_query($query, $url_encoded=FALSE)
    {
        if (is_array($query))
        {
            $query = array_reduce(array_keys($query), function( $string, $name) use ( $query, $url_encoded)
            {
                $value = $query[$name];
                if ( ! $url_encoded)
                {
                    $name = urlencode($name);
                    $value = urlencode($value);
                }
                return $string . (strlen($string) > 0 ? '&' : '') . "{$name}={$value}";
            });
        }

        $_query = $this->get_client_data($this->multi_key)->config['query'];

        if (strlen($_query) > 0)
            $query = "{$_query}&{$query}";

        $this->_set_config('query', $query);
        return $this;
    }

    // -------------------------------------------------------------------------------------

    /**
     * Add or replace request headers.
     */
    public function set_http_header($name_or_array, $value=NULL)
    {
        if ( ! is_array($name_or_array))
        {
            $name_or_array = array("{$name_or_array}: $value");
        }

        $headers = $this->get_config('headers');

        foreach ($name_or_array as $header)
        {
            list($name, $value) = $this->split_header_field($header);
            $name = strtolower($name);
            $headers[$name] = $value;
        }

        $this->_set_config('headers', $headers);
        return $this;
    }

    // -------------------------------------------------------------------------------------

    /**
     * Add or replace request cookies.
     */
    public function set_request_cookie($name_or_array, $value=NULL)
    {
        if ( ! is_array($name_or_array))
        {
            $name_or_array = array($name_or_array => $value);
        }

        $cookies = $this->get_config('cookies');

        foreach ($name_or_array as $name => $value)
        {
            $cookies[$name] = $value;
        }

        $this->_set_config('cookies', $cookies);
        return $this;
    }

    // -------------------------------------------------------------------------------------

    /**
     * Set a curl option.
     */
    public function set_curl_option($option_or_array, $value=NULL)
    {
        if ( ! is_array($option_or_array))
        {
            $option_or_array = array($option_or_array => $value);
        }

        $curl_options = $this->get_config('curl_options');

        foreach ($option_or_array as $option => $value)
        {
            $curl_options[$option] = $value;
        }

        $this->_set_config('curl_options', $curl_options);
        return $this;
    }

    // -------------------------------------------------------------------------------------

    /**
     * Enable auto-redirection.
     */
    public function set_auto_redirect($max_redirects=NULL)
    {
        if (isset($max_redirects))
        {
            $this->set_config('max_redirects', $max_redirects);
        }
        return $this->set_config(['auto_redirect' => TRUE, 'auto_referer' => TRUE]);
    }

    /**
     * Disable auto-redirection.
     */
    public function unset_auto_redirect()
    {
        return $this->set_config(['auto_redirect' => FALSE, 'auto_referer' => FALSE]);
    }

    // -------------------------------------------------------------------------------------

    public function download($filename)
    {
        log_message('info', "download()");
        $ch = curl_init();
        try
        {
            if ($fp = @fopen($filename, 'w'))
            {
                $this->setup_curl($ch, $stderr, NULL, $fp);
                if ($result = curl_exec($ch))
                {
                    log_message('info', "save file as `{$filename}`");
                    return $this->process_result($ch, $result, $stderr);
                }
            }
            else
            {
                log_error("Failed to Open File !!! [{$filename}]");
            }
        }
        finally
        {
            curl_close($ch);
            if ($fp)
            {
                fclose($fp);
                if ($this->get_response_code() != '200')
                {
                    @unlink($filename);
                }
            }
            if ($stderr)
            {
                fclose($stderr);
            }
            log_message('info', "Closed resources.");
        }

        return FALSE;
    }

    // -------------------------------------------------------------------------------------

    /**
     * Perform a request.
     */
    public function request()
    {
        if ( ! $this->multi_request_mode)
        {
            $ch = curl_init();
            try
            {
                $this->setup_curl($ch, $stderr);
                $result = curl_exec($ch);
                return $this->process_result($ch, $result, $stderr);
            }
            finally
            {
                curl_close($ch);
                if ($stderr)
                {
                    fclose($stderr);
                }
            }
        }
        else
        {
            $mh = curl_multi_init();
            $handlers = array();
            $stderrs = array();
            try
            {
                foreach ($this->get_multi_keys() as $multi_key)
                {
                    $ch = curl_init();
                    $this->setup_curl($ch, $stderr, $multi_key);
                    $handlers[$multi_key] = $ch;
                    $stderrs[$multi_key]  = $stderr;
                    curl_multi_add_handle($mh, $ch);
                }

                $active = null;
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);

                while ($active && $mrc == CURLM_OK)
                {
                    if (curl_multi_select($mh) != -1)
                    {
                        do {
                            $mrc = curl_multi_exec($mh, $active);
                        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                    }
                }

                if ($mrc != CURLM_OK)
                {
                    return FALSE;
                }

                foreach ($this->get_multi_keys() as $multi_key)
                {
                    $ch     = $handlers[$multi_key];
                    $stderr = $stderrs[$multi_key];
                    $result = curl_multi_getcontent($ch);
                    $this->process_result($ch, $result, $stderr, $multi_key);
                }

                return TRUE;
            }
            finally
            {
                foreach ($this->get_multi_keys() as $multi_key)
                {
                    if (isset($handlers[$multi_key]))
                    {
                        $ch = $handlers[$multi_key];
                        curl_multi_remove_handle($mh, $ch);
                    }

                    if (isset($stderrs[$multi_key]))
                    {
                        $stderr = $stderrs[$multi_key];
                        fclose($stderr);
                    }
                }

                curl_multi_close($mh);
            }
        }

        return FALSE;
    }

    private function append_query_string($url, $query)
    {
        preg_match('/([^\?]*)(\?|$)(.*)/', $url, $m);
        return $m[1] . '?' . $m[3] . (strlen($m[3]) > 0 ? '&' : '') . $query;
    }

    private function dump_query_string($query)
    {
        $ret = '';
        foreach (explode('&', $query) as $key_value)
        {
            preg_match('/([^=]*)(=|$)(.*)/', $key_value, $m);
            list($key, $value) = [urldecode($m[1]), urldecode( $m[3])];
            if ($key == 'PWD') { $value = '********'; }
            $ret .= "[$key] => [$value]\n";
        }

        return $ret;
    }

    private function split_header_field($header)
    {
        if (preg_match('/^([^:]*):\s*(.*)/', $header, $m))
        {
            return array($m[1], $m[2]);
        }
        else
        {
            return FALSE;
        }
    }

    private function setup_curl($ch, &$stderr, $multi_key=NULL, $fp=NULL)
    {
        $config = &$this->get_client_data($multi_key)->config;

        // verbose
        if ($config['verbose'])
        {
            $stderr = tmpfile();
            curl_setopt_array($ch, array(
                CURLOPT_VERBOSE => TRUE,
                CURLOPT_STDERR  => $stderr
           ));
            log_message('debug', "Verbose: ON, tmp_dir: " . sys_get_temp_dir());
        }

        if (is_null($fp))
        {
            curl_setopt_array($ch, array(
                CURLOPT_HEADER         => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_SSLVERSION     => CURL_SSLVERSION_TLSv1_2,
            ));
        }
        else
        {
            curl_setopt_array($ch, array(
                CURLOPT_HEADER         => FALSE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_SSLVERSION     => CURL_SSLVERSION_TLSv1_2,
            ));
            curl_setopt($ch, CURLOPT_FILE, $fp);
        }

        // url, method, queries
        if (strtolower($config['method']) == 'post')
        {
            curl_setopt_array($ch, array(
                CURLOPT_POST    => TRUE,
                CURLOPT_HTTPGET => FALSE
            ));

            if (isset($config['query']))
            {
                if (isset($config['upload_file']))
                {
                    parse_str($config['query'], $post_data);
                    $post_data[$config['upload_file']['name']] = new CURLfile(
                        $config['upload_file']['file_path'],
                        $config['upload_file']['mime_type'],
                        $config['upload_file']['filename']
                    );
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                }
                else
                {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $config['query']);
                }
            }

            $url = $config['url'];
        }
        elseif (strtolower($config['method']) == 'get')
        {
            curl_setopt_array($ch, array(
                CURLOPT_POST    => FALSE,
                CURLOPT_HTTPGET => TRUE
            ));

            if (isset($config['query']))
            {
                $url = $this->append_query_string($config['url'], $config['query']);
            }
            else
            {
                $url = $config['url'];
            }
        }
        elseif (strtolower($config['method']) == 'delete')
        {
            curl_setopt_array($ch, array(
                CURLOPT_CUSTOMREQUEST => 'DELETE',
            ));

            if (isset($config['query']))
            {
                $url = $this->append_query_string($config['url'], $config['query']);
            }
            else
            {
                $url = $config['url'];
            }
        }

        log_message('debug', "Request url: {$url}");
        curl_setopt($ch, CURLOPT_URL, $url);

        if ( ! empty($config['verbose']) && isset($config['query']))
        {
            log_message('debug', "Request query:\n" . $this->dump_query_string($config['query']));
        }

        // headers
        if ( ! empty($config['headers']))
        {
            $headers = array();

            foreach ($config['headers'] as $name => $value)
            {
                $headers[] = "{$name}: {$value}";
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // cookies
        if ( ! empty($config['cookies']))
        {
            $cookies = array();

            foreach ($config['cookies'] as $name => $value)
            {
                $cookies[] = "{$name}={$value}";
            }

            curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $cookies));
        }

        // cookie_file
        if (isset($config['cookie_file']) && is_writable($config['cookie_file']))
        {
            curl_setopt_array($ch, array(
                CURLOPT_COOKIEFILE => $config['cookie_file'],
                CURLOPT_COOKIEJAR  => $config['cookie_file']
           ));
        }

        // user_agent
        if (isset($config['user_agent']))
        {
            curl_setopt($ch, CURLOPT_USERAGENT, $config['user_agent']);
        }

        // connect_timeout, timeout
        curl_setopt_array($ch, array(
            CURLOPT_CONNECTTIMEOUT => $config['connect_timeout'],
            CURLOPT_TIMEOUT        => $config['timeout']
       ));

        // username, password
        if (isset($config['username']) && isset( $config['password']))
        {
            curl_setopt_array($ch, array(
                CURLOPT_HTTPAUTH => CURLAUTH_ANY,
                CURLOPT_USERPWD  => "{$config['username']}:{$config['password']}"
           ));
        }

        // proxy_host, proxy_port, proxy_id, proxy_pw
        if (isset($config['proxy_host']) && isset( $config['proxy_port']))
        {
            curl_setopt_array($ch, array(
                CURLOPT_PROXY     => $config['proxy_host'],
                CURLOPT_PROXYPORT => $config['proxy_port']
           ));
            if (isset($proxy_id))
            {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, "{$config['proxy_id']}:{$config['proxy_pw']}");
            }
        }

        // auto_redirect
        if (isset($config['auto_redirect']))
        {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $config['auto_redirect']);
        }

        // auto_referer
        if (isset($config['auto_referer']))
        {
            curl_setopt($ch, CURLOPT_AUTOREFERER, $config['auto_referer']);
        }

        // max_redirects
        if (isset($config['max_redirects']))
        {
            curl_setopt($ch, CURLOPT_MAXREDIRS, $config['max_redirects']);
        }

        // curl_options
        if ( !empty($config['curl_options']))
        {
            curl_setopt_array($ch, $config['curl_options']);
        }

        return $ch;
    }

    private function process_result($ch, $response, $stderr, $multi_key=NULL)
    {
        $client_data = &$this->get_client_data($multi_key);
        $config = $client_data->config;

        if ($config['verbose'] && $stderr){
            fseek($stderr, 0);
            $fstat = array_slice(fstat($stderr), 13);
            log_message('debug', "== Verbose Info ========================");
            log_message('debug', fread($stderr, $fstat['size']));
            log_message('debug', "== /Verbose Info =======================");
        }

        if ($response === FALSE)
        {
            $client_data->response['errno'] = curl_errno($ch);
            $client_data->response['error'] = curl_error($ch);
            log_message('error', "errno: {$client_data->response['errno']}");
            log_message('error', "error: {$client_data->response['error']}");
            return FALSE;
        }

        $client_data->response['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $client_data->response['cookies'] = array();
        $client_data->response['headers'] = array();

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header_part = str_replace("\r\n", "\n", substr($response, 0, $header_size));
        $body_part = '';

        if (is_string($response))
        {
            $body_part = substr($response, $header_size);
        }
        $client_data->response['data'] = $body_part;

        $content_type = '';

        foreach (explode("\n" , $header_part) as $header)
        {
            list ($name, $value) = $this->split_header_field($header);
            if ($name === NULL){
                continue;
            }
            $name = strtolower($name);
            if ($name == 'set-cookie'){
                $client_data->response['cookies'][] = $value;
            } else {
                $client_data->response['headers'][$name] = $value;
                if ($name == 'content-type')
                    $content_type = $value;
            }
        }

        if ( ! $config['verbose'])
        {
            log_message('debug', "== Response Info ========================");
            log_message('debug', "HTTP Status: {$client_data->response['http_code']}");
            log_message('debug', $header_part);
            log_message('debug', "== /Response Info =======================");
        }
        elseif (preg_match('|^text|', $content_type))
        {
            log_message('debug', "== Response Data ========================");
            log_message('debug', $body_part);
            log_message('debug', "== /Response Data =======================");
        }

        return TRUE;
    }

    // -------------------------------------------------------------------------------------

    /**
     * Returns a response code.
     */
    public function get_response_code()
    {
        $client_data = &$this->get_client_data($this->multi_key);

        if ( ! isset($client_data->response['http_code']))
        {
            return FALSE;
        }

        return $client_data->response['http_code'];
    }

    /**
     * Return the last error number and message.
     */
    public function get_curl_error()
    {
        $client_data = &$this->get_client_data($this->multi_key);
        if ( ! isset($client_data->response['errno']))
        {
            return FALSE;
        }

        return array(
            'errno' => $client_data->response['errno'],
            'error' => $client_data->response['error'],
       );
    }

    /**
     * Returns response data.
     */
    public function get_response_data()
    {
        $client_data = &$this->get_client_data($this->multi_key);

        if ( ! isset($client_data->response['data']))
        {
            return FALSE;
        }

        return $client_data->response['data'];
    }

    /**
     * write file
     */
    public function save_as_file($save_path, $overwrite=TRUE)
    {
        $save_dir = dirname($save_path);
        log_debug("save_as_file() run.");
        log_debug($save_path);

        $data = $this->get_response_data();

        if (file_exists($save_dir) && ! empty($data))
        {
            if ($overwrite !== TRUE && file_exists($save_path))
            {
                log_error('File already exists. [' . $save_path . ']');
                return FALSE;
            }

            $mode = $overwrite === TRUE ? 'wb' : 'x';

            if ( ! function_exists('write_file'))
            {
                $CI = get_instance();
                $CI->load->helper('file');
            }

            if (write_file($save_path, $data, $mode))
            {
                return TRUE;
            }
            else
            {
                log_error("Failed to write file on `{$save_path}`");
            }
        }
        else
        {
            log_error('Directory not found, or data is empty.');
        }

        return FALSE;
    }

    /**
     * Returns a response header for the given name.
     */
    public function get_response_header($name)
    {
        $client_data = &$this->get_client_data($this->multi_key);
        $name = strtolower($name);

        if ( ! isset($client_data->response['headers'][$name]))
        {
            return FALSE;
        }

        return $client_data->response['headers'][$name];
    }

    /**
     * Returns all response headers.
     */
    public function get_response_headers()
    {
        $client_data = &$this->get_client_data($this->multi_key);

        if ( ! isset($client_data->response['headers']))
        {
            return FALSE;
        }

        return $client_data->response['headers'];
    }

    /**
     * Returns response cookies.
     */
    public function get_response_cookies()
    {
        $client_data = &$this->get_client_data($this->multi_key);

        if ( ! isset($client_data->response['cookies']))
        {
            return FALSE;
        }

        return $client_data->response['cookies'];
    }

    /**
     * Returns response data.
     */
    public function get_response()
    {
        $client_data = &$this->get_client_data($this->multi_key);

        if ( ! isset($client_data->response))
        {
            return FALSE;
        }

        return $client_data->response;
    }

    // -------------------------------------------------------------------------------------

    /**
     * Decode and return JSON-serialized response data.
     */
    public function json_decode()
    {
        $client_data = &$this->get_client_data($this->multi_key);

        if ( ! isset($client_data->response['data']))
        {
            return FALSE;
        }

        return json_decode($client_data->response['data'], TRUE);
    }

    /**
     * Decode and return php-serialized response data.
     */
    public function php_unserialize($multi_key=NULL)
    {
        $client_data = &$this->get_client_data($this->multi_key);

        if ( ! isset($client_data->response['data']))
        {
            return FALSE;
        }

        return unserialize($client_data->response['data']);
    }
}

