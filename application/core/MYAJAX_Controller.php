<?php
/**
 * MYAJAX_Controller Class
 *
 * @package     Hairlogy
 * @category    Core Controller
 * @author      yuki.hatano@gmail.com
 */

class MYAJAX_Controller extends MY_Controller {

    public $_login;

    protected $_responded  = FALSE;

    protected $_response;
    protected $_response_errors  = array();
    protected $_error_messages   = array();
    protected $_request_data_md5 = '';
    protected $_client_ip        = '';

    // -----------------------------------------------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('cookie');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->driver('excache', array('adapter' => 'apcu',), 'apcu');
        //$this->load->driver('cache', array('adapter' => 'redis', 'backup' => 'file'), 'redis');

        $module = '';

        if ($_referer = explode('/',$this->input->get_request_header('REFERER')))
        {
            if (isset($_referer[3]))
                $module = $_referer[3];
        }

        // ログイン情報
        if ( ! $this->_login = $this->session->userdata("{$module}_login"))
        {
            if (($this->_class != 'login' OR $this->_action != 'auth'))
            {
                session_write_close();
                $this->error_response('AUTH', ['Unauthorized']);
                $this->response('NG');
                $this->output->_display();
                log_debug('loged out !');
                exit;
            }
        }
        elseif ( ! $this->session->get_flash_keys())
        { // release `session lock`
            log_debug("release `session lock`");
            session_write_close();
        }

        // リクエスト情報
        $this->_post        = $this->input->post(NULL, FALSE);
        $this->_client_ip   = $this->input->ip_address();
        $this->common_validation->initialize();

        log_debug('-----------------------------------------------------');
        log_debug('- MYAJAX Controller run');
        log_debug('-----------------------------------------------------');
        log_debug("ClientIP: {$this->_client_ip}");
        log_debug("    HOST: ". base_url());
        log_debug("     URI: /{$this->_uri}");
        log_debug("  Module: {$this->_module}");
        log_debug("   Class: {$this->_class}");
        log_debug("  Action: {$this->_action}");
        log_debug('--- POST: -------------------------------------------');
        foreach ($this->_post as $key => $val)
        {
            $key = trim($key);

            if (is_array($val))
            {
                $this->_post[$key] = $val;
                log_debug("  {$key}: array()");
            }
            elseif (is_string($val))
            {
                $val = trim($val);

                /*
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
                */

                $this->_post[$key] = $val;
                if ($key == 'loginpw')
                    log_debug("  {$key}: ******");
                else
                    log_debug("  {$key}: {$val}");
            }
            elseif (is_int($val) || is_float($val))
            {
                $this->_post[$key] = $val;
                log_debug("  {$key}: {$val}");
            }
        }
        log_debug('-----------------------------------------------------');
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * レスポンスデータ作成
     */
    protected function response($status='NG', $data=array(), $pagination=array())
    {
        if ($this->_responded !== TRUE)
        {
            $this->_response = array(
                'status'     => $status,
                'exec_time'  => $this->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end'),
                'errors'     => $this->_response_errors,
                'pagination' => $pagination,
                'data'       => $data,
            );

            $json = json_encode($this->_response);

            if ( ! empty($this->_do_after_output))
            {
                $this->output->set_header('Content-Length: '.strlen($json));
                $this->output->set_header('Connection: close');
            }

            $this->output->set_content_type('application/json', 'utf-8');
            $this->output->set_output($json);
            log_info("Response: {$json}");
            //log_info($this->_response);

            $this->_response  = array();
            $this->_responded = TRUE;
        }
        else
        {
            log_error('Already output response data.');
        }
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * typeahead用レスポンスデータ作成
     */
    protected function typeahead_response($data, $value_keys=array())
    {
        if ($this->_responded !== TRUE)
        {
            $response = array();

            foreach($data as $datum)
            {
                $_values = array();

                foreach ($value_keys as $key)
                {
                    if (isset($datum[$key]))
                    {
                        $_values[$key] = $datum[$key];
                    }
                }

                $response[] = $_values;
            }

            $this->output->set_content_type('application/json', 'utf-8');
            $json = json_encode($response);
            //log_debug($json);
            $this->output->set_output($json);
            $this->_responded = TRUE;
        }
        else
        {
            log_error('Already output response data.');
        }
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * エラーレスポンスデータ作成
     */
    protected function error_response($error_code, $error_msgs=array())
    {
        $this->_response_errors[] = array(
            'error_code' => $error_code,
            'error_msgs' => $error_msgs,
        );
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * バリデーション
     */
    protected function validate($validation_group='common', $requires=array(), $init_data=NULL)
    {
        log_debug("validate() run.");
        if (parent::validate($validation_group, $requires, $init_data))
        {
            log_debug("Validation OK.");
            return TRUE;
        }
        else
        {
            $this->error_response('validation', $this->_validation_errors);
        }

        return FALSE;
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * 出力
     */
    public function _output($output=NULL)
    {
        if ( ! empty($this->_do_after_output))
        {
            ob_start();
        }

        if (is_array($output))
        {
            echo json_encode($output, TRUE);
        }
        elseif (is_string($output))
        {
            echo $output;
        }
        else
        {
            log_error('Invalid Output !');
            show_error('Invalied Output !');
        }

        if ( ! empty($this->_do_after_output))
        {
            ob_end_flush();
            ob_flush();
            flush();

            log_debug("flash output");
            log_debug("Executing After output: {$this->_do_after_output}().........");
            $this->{$this->_do_after_output}();
        }
    }
    // ----------------------------------------------------------------------------------------------------------------

    /**
     * 画像アップロード
     */
    protected function _img_upload($input_name, $dir, $sub_dir='', $max_size=10240, $delete_files=FALSE, $option=FALSE)
    {
        log_debug("_img_upload({$input_name}, {$dir}, {$sub_dir}) run.");

        $dir = ltrim($dir, '/');
        $tmp_path = "{$this->_tmp_dir}/{$this->_class}/{$dir}";

        if ( ! file_exists($tmp_path) && ! @mkdir($tmp_path, 0775, TRUE))
        {
            $this->_error_messages[] = "tmp directory error.[{$this->_tmp_dir}/{$this->_class}/{$dir}]";
            log_error("tmp directory error.[{$this->_tmp_dir}/{$this->_class}/{$dir}]");
            return FALSE;
        }

        $upload_path = "{$this->_data_dir}/{$this->_class}/{$dir}";

        if ( ! file_exists($upload_path) && ! @mkdir($upload_path, 0775, TRUE))
        {
            $this->_error_messages[] = 'data directory error.';
            log_error("data directory error. [{$upload_path}]");
            return FALSE;
        }

        if (is_string($sub_dir))
        {
            $upload_path .= '/' . ltrim($sub_dir, '/');

            if ( ! file_exists($upload_path) && ! @mkdir($upload_path, 0775, TRUE))
            {
                $this->_error_messages[] = 'data sub directory error.';
                log_error("data sub directory error. [{$upload_path}]");
                return FALSE;
            }
        }

        $config = array(
            'upload_path'       => $tmp_path,
            'allowed_types'     => 'jpeg|jpg|gif|tif|tiff|png',
            'max_size'          => $max_size,
            'file_ext_tolower'  => TRUE,
            'overwrite'         => TRUE,
            'encrypt_name'      => TRUE,
        );

        $this->load->library('upload', $config);
        if ($this->upload->do_upload($input_name))
        {
            $tmp_file   = $this->upload->data('full_path');
            $file_ext   = strtolower($this->upload->data('file_ext'));

            if ($file_ext == '.jpeg')
                $file_ext = '.jpg';
            elseif ($file_ext == '.tiff')
                $file_ext = '.tif';

            $file_md5   = md5_file($tmp_file);
            $file_name  = "{$file_md5}{$file_ext}";
            $file_org   = "{$file_md5}_org{$file_ext}";
            //log_debug($this->upload->data());

            $file_info = array(
                'file_name'  => $file_name,
                'file_ext'   => $this->upload->data('file_ext'),
                'file_type'  => $this->upload->data('file_type'),
                'orig_name'  => $this->upload->data('orig_name'),
                //'file_size'  => $this->upload->data('file_size'),
                //'img_width'  => $this->upload->data('image_width'),
                //'img_height' => $this->upload->data('image_height'),
                'file_md5'   => $file_md5,
            );

            if (isset($option['allowed_types']))
            {
                if ( ! in_array($file_info['file_type'], $option['allowed_types']))
                {
                    @unlink($tmp_file);
                    log_error('File uploaded but not allowed mime type.');
                    log_error($file_info);
                    return FALSE;
                }
            }

            if ($delete_files)
            {
                $this->_delete_files($dir, $sub_dir);
            }

            $img_path = "{$upload_path}/{$file_name}";
            $org_path = "{$upload_path}/{$file_org}";
            log_debug($img_path);
            if (@rename($tmp_file, $img_path) && @copy($img_path, $org_path))
            {
                $this->load->library('MY_imagick');

                if ($file_info['file_type'] == 'image/jpeg')
                {
                    $this->my_imagick->strip($img_path);
                }

                if (empty($option))
                {
                    $size = getimagesize($img_path);
                    $file_info['img_width']  = $size[0];
                    $file_info['img_height'] = $size[1];
                    $file_info['file_size']  = filesize($img_path);

                    if ($file_info['file_type'] == 'image/tiff')
                    {
                        $this->my_imagick->convert_2_cmyk($img_path);
                    }
                }
                else
                {
                    if (isset($option['max_height']) OR isset($option['max_width']))
                    {
                        $this->my_imagick->resize($img_path, $option);
                    }

                    $size = getimagesize($img_path);
                    $file_info['img_width']  = $size[0];
                    $file_info['img_height'] = $size[1];
                    $file_info['file_size']  = filesize($img_path);

                    if (! empty($option['convert2tiff']) && $file_info['file_type'] != 'image/tiff')
                    {
                        $dst_path = "{$upload_path}/{$file_md5}.tif";

                        if ( ! $this->my_imagick->convert2tiff($img_path, $dst_path))
                        {
                                @unlink($img_path);
                                log_error('Failed to convert jpeg image to tiff.');
                                return FALSE;
                        }

                        $size = getimagesize($dst_path);
                        $file_info['img_width']  = $size[0];
                        $file_info['img_height'] = $size[1];
                        $file_info['file_size']  = filesize($dst_path);
                    }

                    if ( ! empty($option['convert2jpeg']) && $file_info['file_type'] != 'image/jpeg')
                    {
                        $dst_path = "{$upload_path}/{$file_md5}.jpg";

                        if ($this->my_imagick->convert2jpeg($img_path, $dst_path))
                        {
                            if ($file_info['file_type'] == 'image/tiff')
                                $this->my_imagick->convert_2_cmyk($img_path);

                            $img_path = $dst_path;
                        }
                        else
                        {
                            @unlink($img_path);
                            log_error('Failed to convert image.');
                            return FALSE;
                        }
                    }
                    if ( ! empty($option['convert2png']) && $file_info['file_type'] != 'image/png')
                    {
                        $dst_path = "{$upload_path}/{$file_md5}.png";

                        if ($this->my_imagick->convert2png($img_path, $dst_path))
                        {
                            if ($file_info['file_type'] == 'image/tiff')
                                $this->my_imagick->convert_2_cmyk($img_path);

                            $img_path = $dst_path;
                        }
                        else
                        {
                            @unlink($img_path);
                            log_error('Failed to convert image.');
                            return FALSE;
                        }
                    }
                }

                if (isset($option['thumb_height']) OR isset($option['thumb_width']))
                {
                    $this->my_imagick->thumbnail($img_path, $option);
                }

                log_debug($file_info);
                return $file_info;
            }

        }
        else
        {
            log_error($this->upload->display_errors('', ''));
            $this->_error_messages = $this->upload->display_errors('', '');
        }

        return FALSE;
    }
    // ----------------------------------------------------------------------------------------------------------------

    /**
     * データアップロード
     */
    protected function _data_upload($input_name, $dir, $sub_dir='', $max_size=10240, $delete_files=FALSE)
    {
        log_debug("_data_upload({$input_name}, {$dir}, {$sub_dir}) run.");
        $dir = ltrim($dir, '/');
        $tmp_path = "{$this->_tmp_dir}/{$this->_class}/{$dir}";

        if ( ! file_exists($tmp_path) && ! @mkdir($tmp_path, 0775, TRUE))
        {
            $this->_error_messages[] = "tmp directory error.[{$this->_tmp_dir}/{$this->_class}/{$dir}]";
            return FALSE;
        }

        $upload_path = "{$this->_data_dir}/{$this->_class}/{$dir}";

        if ( ! file_exists($upload_path) && ! @mkdir($upload_path, 0775, TRUE))
        {
            $this->_error_messages[] = 'data directory error.';
            return FALSE;
        }

        if ($sub_dir && is_string($sub_dir))
        {
            $upload_path .= '/' . ltrim($sub_dir, '/');

            if ( ! file_exists($upload_path) && ! @mkdir($upload_path, 0775))
            {
                $this->_error_messages[] = 'data sub directory error.';
                return FALSE;
            }
        }

        $config = array(
            'upload_path'       => $tmp_path,
            'max_size'          => $max_size,
            'allowed_types'     => '*',
            'file_ext_tolower'  => TRUE,
            'overwrite'         => TRUE,
            'encrypt_name'      => TRUE,
        );

        $this->load->library('upload', $config);

        if ($this->upload->do_upload($input_name))
        {
            $tmp_file = $this->upload->data('full_path');
            $file_ext = $this->upload->data('file_ext');
            $file_md5 = md5_file($tmp_file);

            if (rename($tmp_file, "{$upload_path}/{$file_md5}{$file_ext}"))
            {
                return [
                    'orig_name' => $this->upload->data('orig_name'),
                    'file_name' => $tmp_file,
                    'file_ext'  => $file_ext,
                    'file_md5'  => $file_md5,
                    'file_size' => $this->upload->data('file_size'),
                ];
            }
            else
            {
                log_error("Failed to rename file. [{$tmp_file}]->[{$upload_path}/{$file_md5}{$file_ext}]");
            }
        }
        else
        {
            $this->_error_messages[] = $this->upload->display_errors('<p>', '</p>');
            log_error($this->_error_messages);
        }

        return FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------------

    protected function _delete_files($dir, $sub_dir, $del_dir=FALSE)
    {
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

    // ----------------------------------------------------------------------------------------------------------------

}

