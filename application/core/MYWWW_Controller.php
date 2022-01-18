<?php

/**
 * MYWWW_Controller Class
 *
 * @package     Hairlogy
 * @author      yuki.hatano@gamil.com
 * @category    Core Controller
 * @link        http://
 */

class MYWWW_Controller extends MY_Controller {

    protected $_client_ip;
    protected $_referer;
    protected $_post = [];

    protected $_login           = NULL;
    protected $_title           = '';
    protected $_menu            = NULL;
    protected $_active_menu     = NULL;
    protected $_active_sub_menu = NULL;
    protected $_sub_title       = '';
    protected $_js_data         = NULL;
    protected $_option_head     = '';
    protected $_option_foot     = '';
    protected $_revision = '0000000000';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('user_agent');
        $this->load->driver('excache', array('adapter' => 'apcu',), 'apcu');

        $this->_client_ip   = $this->input->ip_address();
        $this->_user_agent  = $this->input->user_agent();
        $this->_browser     = $this->agent->browser();
        $this->_referer     = $this->input->get_request_header('REFERER');

        $this->_post        = $this->input->post();

        define('APC_TTL', ENVIRONMENT == 'production' ? 60 * 60 * 24 : 180);

        log_debug('-----------------------------------------------------');
        log_debug('MYWWW_Controller ------------------------------------');
        log_debug('-----------------------------------------------------');
        log_debug(" ClientIP: {$this->_client_ip}");
        log_debug("     HOST: ". base_url());
        log_debug("      URI: /{$this->_uri}");
        log_debug("   Module: {$this->_module}");
        log_debug("    Class: {$this->_class}");
        log_debug("   Action: {$this->_action}");
        log_debug("  Referer: {$this->_referer}");
        log_debug("UserAgent: {$this->_user_agent}");
        log_debug("  Browser: {$this->_browser}");
        log_debug('-----------------------------------------------------');

        if ( ! empty($this->_post))
        {
            log_debug('POST Queries: ---------------------------------------');

            if (is_array($this->_post)) foreach ($this->_post as $key => &$val)
            {
                if (is_string($val))
                {
                    $val = trim($val);
                }

                if (preg_match('|url$|', $key))
                {
                    $val = preg_replace_callback(
                        '/[^\x21-\x7e]+/',
                        function( $matches ) {
                            return urlencode( $matches[0] );
                        },
                        $val
                    );
                }

                if (preg_match('|date$|', $key))
                {
                    if (is_array($val)) foreach ($val as &$v)
                    {
                        $v = str_replace('/', '-', $v);
                    }
                    else
                    {
                        $val = str_replace('/', '-', $val);
                    }
                }

                if (is_array($val))
                    log_debug("  {$key}: " . var_export($val, TRUE));
                elseif (preg_match('|loginpw|', $key))
                    log_debug("  {$key}: ********");
                else
                    log_debug("  {$key}: {$val}");
            }
        }
        log_debug('-----------------------------------------------------');

        $this->_revision = config_item('revision');

        // Smarty初期化
        $this->view->template_dir    = APPPATH . 'views/' . $this->_module;
        $this->view->compile_dir     = rtrim(PROJECTPATH.'/tmp/templates_c/'.$this->_module, '/');
        $this->view->addPluginsDir(realpath(APPPATH) . '/third_party/smarty_plugins/');
        $this->view->assign('module',  $this->_module);
        $this->view->assign('class',   $this->_class);
        $this->view->assign('action',  $this->_action);
        $this->view->assign('login',   $this->_login);
        $this->view->assign('messages',          $this->session->flashdata('messages'));
        $this->view->assign('error_messages',    $this->session->flashdata('error_messages'));
        $this->view->assign('validation_errors', $this->session->flashdata('validation_errors'));
        $this->view->assign('login_ttl',         config_item('login_ttl'));
        $this->view->assign('revision',          $this->_revision);
        $this->view->assign('today',             date('Y-m-d'));
        $this->_template = 'layout.tpl';

        log_debug("MYWWW_Controller Initialized");
    }

    // =======================================================================================================

    protected function post($key=NULL, $val=NULL)
    {
        if (is_null($key)) {
            return $this->_post;
        }

        if (isset($this->_post[$key]))
        {
            if ( ! is_null($val))
            {
                $this->_post[$key] = $val;
            }
            return $this->_post[$key];
        }

        return NULL;
    }

    // -------------------------------------------------------------------------------------------------------

    /**
     * バリデーション
     */
    protected function validate($validation_group='common', $requires=[], $init_data=NULL)
    {
        log_debug("MYWWW_Controller.validate({$validation_group}) run.");
        if (parent::validate($validation_group, $requires, $init_data))
        {
            log_debug("Validation OK.");
            return TRUE;
        }
        else
        {
            $this->_validation_errors = $this->_validation_errors;
        }

        return FALSE;
    }

    // -------------------------------------------------------------------------------------------------------

    /**
     * リダイレクト
     */
    protected function redirect($redirect_to)
    {
        if ( ! empty($this->_messages))
        {
            $this->session->set_flashdata('messages', $this->_messages);
        }

        if ( ! empty($this->_error_messages))
        {
            log_error("== Error =======================");
            log_error($this->_error_messages);
            $this->session->set_flashdata('error_messages', $this->_error_messages);
        }

        if ( ! empty($this->_validation_errors))
        {
            $this->session->set_flashdata('validation_errors', $this->_validation_errors);
        }

        redirect($redirect_to, 'location', 303);
    }

    // -------------------------------------------------------------------------------------------------------

    public function _output($output)
    {
        if (is_array($output))
        {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($output);
        }
        elseif (empty($output))
        {
            if (file_exists(APPPATH . "../htdocs/css/{$this->_module}.css"))
                $this->_option_head .= '<link href="/css/'.$this->_module.'.css?' . $this->_revision . '" rel="stylesheet">'."\n";

            if (file_exists(APPPATH . "../htdocs/css/{$this->_module}/{$this->_class}.css"))
                $this->_option_head .= '<link href="/css/'.$this->_module.'/'.$this->_class.'.css?' . $this->_revision . '" rel="stylesheet">'."\n";

            if (file_exists(APPPATH . "../htdocs/css/{$this->_module}/{$this->_class}/{$this->_action}.css"))
                $this->_option_head .= '<link href="/css/'.$this->_module.'/'.$this->_class.'/'.$this->_action . '.css?' . $this->_revision . '" rel="stylesheet">'."\n";

            if (file_exists(APPPATH . "../htdocs/js/{$this->_module}.js"))
                $this->_option_foot .= '<script src="/js/'.$this->_module.'.js?' . $this->_revision . '"></script>'."\n";

            if (file_exists(APPPATH . "../htdocs/js/{$this->_module}/{$this->_class}.js"))
                $this->_option_foot .= '<script src="/js/'.$this->_module.'/'.$this->_class.'.js?' . $this->_revision . '"></script>'."\n";

            if (file_exists(APPPATH . "../htdocs/js/{$this->_module}/{$this->_class}/{$this->_action}.js"))
                $this->_option_foot .= '<script src="/js/'.$this->_module.'/'.$this->_class.'/'.$this->_action.'.js?' . $this->_revision . '"></script>'."\n";

            $this->view->assign('option_head',      $this->_option_head);
            $this->view->assign('option_foot',      $this->_option_foot);
            $this->view->assign('js_data',          $this->_js_data);
            $this->view->assign('title',            $this->_title);
            $this->view->assign('sub_title',        $this->_sub_title);
            $this->view->assign('menu',             $this->_menu);
            $this->view->assign('active_menu',      $this->_active_menu);
            $this->view->assign('active_sub_menu',  $this->_active_sub_menu);

            try
            {
                $this->view->display($this->_template);
            }
            catch (SmartyException $e)
            {
                log_error($e->getMessage());
                show_error($e->getMessage(), 500, 'Smarty Error.');
            }
        }
        elseif (is_string($output))
        {
            echo $output;
        }
    }

    // -------------------------------------------------------------------------------------------------------

    /**
     * JSデータ
     */
    protected function js_data($js_data=NULL)
    {
        if (empty($js_data))
        {
            return $this->_js_data;
        }
        elseif (is_array($js_data))
        {
            foreach ($js_data as $key => $data)
            {
                $this->_js_data .= "MC.{$key} = " . json_encode($data) . ";\n";
            }
        }
        //log_debug($this->_js_data);
    }

    // -------------------------------------------------------------------------------------------------------

    protected function show_404()
    {
        log_error("Page not found. [{$this->_uri}]");
        $this->_class  = 'error';
        $this->_action = 'not_found';
        $this->view->assign('class', 'error');
        $this->view->assign('action', 'not_found');
    }

    // -------------------------------------------------------------------------------------------------------

    /**
     * 画像アップロード
     */
    protected function _img_upload($input_name, $dir, $sub_dir='', $max_size=10240, $delete_files=FALSE)
    {
        log_debug("_img_upload({$input_name}, {$dir}, {$sub_dir}) run.");

        $dir = ltrim($dir, '/');
        $tmp_path = "{$this->_tmp_dir}/{$this->_class}/{$dir}";

        if ( ! file_exists($tmp_path) && ! @mkdir($tmp_path, 0770))
        {
            $this->_error_messages[] = "tmp directory error.[{$this->_tmp_dir}/{$this->_class}/{$dir}]";
            log_error("tmp directory error.[{$this->_tmp_dir}/{$this->_class}/{$dir}]");
            return FALSE;
        }

        $upload_path = "{$this->_data_dir}/{$this->_class}/{$dir}";

        if ( ! file_exists($upload_path) && ! @mkdir($upload_path, 0770))
        {
            $this->_error_messages[] = 'data directory error.';
            log_error("data directory error. [{$upload_path}]");
            return FALSE;
        }

        if (is_string($sub_dir) OR is_numeric($sub_dir))
        {
            $upload_path .= '/' . ltrim($sub_dir, '/');

            if ( ! file_exists($upload_path) && ! @mkdir($upload_path, 0770))
            {
                $this->_error_messages[] = 'data sub directory error.';
                log_error("data sub directory error. [{$upload_path}]");
                return FALSE;
            }
        }

        $config = array(
            'upload_path'       => $tmp_path,
            'allowed_types'     => 'jpeg|jpg|gif|tiff|png',
            'max_size'          => $max_size,
            'file_ext_tolower'  => TRUE,
            'overwrite'         => TRUE,
            'encrypt_name'      => TRUE,
        );
        $this->load->library('upload', $config);

        if ($this->upload->do_upload($input_name))
        {
            $tmp_file   = $this->upload->data('full_path');
            $file_ext   = $this->upload->data('file_ext');
            $file_md5   = md5_file($tmp_file);
            $file_name  = "{$file_md5}{$file_ext}";

            $file_info = array(
                'file_name'  => $file_name,
                'file_ext'   => $this->upload->data('file_ext'),
                'orig_name'  => basename($this->upload->data('orig_name'), $this->upload->data('file_ext')),
                'file_size'  => $this->upload->data('file_size'),
                'img_width'  => $this->upload->data('image_width'),
                'img_height' => $this->upload->data('image_height'),
                'file_md5'   => $file_md5,
            );

            if ($delete_files)
            {
                $this->_delete_files($dir, $sub_dir);
            }
            log_debug("File uploaded. [{$upload_path}]");
            log_debug($file_info);

            if (@rename($tmp_file, "{$upload_path}/{$file_name}"))
            {
                return $file_info;
            }
        }
        else
        {
            log_error($this->upload->display_errors('', ''));
        }

        return FALSE;
    }

    // =======================================================================================================

    private function _delete_files($dir, $sub_dir, $del_dir=FALSE)
    {
        $dir     = $this->security->sanitize_filename($dir);
        $sub_dir = $this->security->sanitize_filename($sub_dir);

        $upload_path = "{$this->_data_dir}/{$this->_class}/{$dir}";

        if ( ! file_exists($upload_path))
        {
            return TRUE;
        }

        if (is_string($sub_dir))
        {
            $upload_path .= '/' . ltrim($sub_dir, '/');

            if ( ! file_exists($upload_path))
            {
                return TRUE;
            }
        }

        $this->load->helper('file');

        return delete_files($upload_path, $del_dir);
    }

}

