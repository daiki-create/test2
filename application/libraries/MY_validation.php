<?php
/**
 * MY_validation Class
 *
 * @category    Library
 * @author      yuki.hatano@gmail.com
 */

class MY_validation extends MY_Library {

    protected $_data;
    protected $_key_maps = [];
    protected $_CI;

    public function __construct($data=array())
    {
        $this->_data = $data;
        $this->_CI = get_instance();
        log_message('DEBUG', 'MY_validation Class loaded');
    }

    // --------------------------------------------------------------------------------------------

    public function initialize($data=array())
    {
        $this->_data = $data;
        $this->_errors = [];
        log_message('DEBUG', 'MY_validation Class Initialized');
        return $this;
    }

    // --------------------------------------------------------------------------------------------

    public function get_data($key=NULL)
    {
        if (is_string($key) && strlen($key) && isset($this->_data[$key]))
        {
            return $this->_data[$key];
        }
        return $this->_data;
    }

    // --------------------------------------------------------------------------------------------

    public function reset_data($key, $val)
    {
        $this->_data[$key] = $val;
    }

    // --------------------------------------------------------------------------------------------

    public function run($requires=array())
    {
        log_message('INFO', 'my_validation run().');
        //log_message('DEBUG', var_export($requires, TRUE));
        //log_message('DEBUG', var_export($this->_data, TRUE));

        if (is_string($requires))
        {
            $key = $requires;
            $requires = array($key => TRUE);
        }
        elseif ( ! is_array($requires))
        {
            show_error('Internal Server Error.', 500);
        }

        // 必須クエリチェック
        foreach ($requires as $key => $require)
        {
            if ($require && ( ! isset($this->_data[$key]) OR $this->_empty($this->_data[$key])))
            {
                if (isset($this->_key_maps[$key]))
                    $this->_errors[$key] = "「{$this->_key_maps[$key]}」は、必須項目です。";
                else
                    $this->_errors[$key] = "[{$key}] is required.";

                continue;
            }
            elseif ( ! $require && (! isset($this->_data[$key]) OR $this->_empty($this->_data[$key])))
            {
                //unset($this->_data[$key]);
                $this->_data[$key] = NULL;
                continue;
            }
            $val = $this->_data[$key];

            $_key = preg_replace('|^\w+_id$|',         'id',       $key);
            $_key = preg_replace('|^\w+_status$|',     'status',   $_key);
            $_key = preg_replace('|^\w+_flag$|',       'flag',     $_key);
            $_key = preg_replace('|^\w+_url$|',        'url',      $_key);
            $_key = preg_replace('|^\w+_name|',        'name',     $_key);
            $_key = preg_replace('|^\w+_kana|',        'kana',     $_key);
            $_key = preg_replace('|^\w+_mail|',        'mail',     $_key);
            $_key = preg_replace('|^\w+_note$|',       'note',     $_key);
            $_key = preg_replace('|^\w+_time|',        'time',     $_key);
            $_key = preg_replace('|^\w+_date$|',       'date',     $_key);
            $_key = preg_replace('|^\w+_day|',         'day',      $_key);
            $_key = preg_replace('|^\w+_month$|',      'month',    $_key);
            $_key = preg_replace('|^\w+_since$|',      'since',    $_key);
            $_key = preg_replace('|^\w+_until$|',      'until',    $_key);
            $_key = preg_replace('|^\w+_loginpw$|',    'loginpw',  $_key);
            $_key = preg_replace('|^\w+_postcode$|',   'postcode', $_key);
            $_key = preg_replace('|^\w+_postcode1$|',  'postcode1',$_key);
            $_key = preg_replace('|^\w+_postcode2$|',  'postcode2',$_key);
            $_key = preg_replace('|^\w+_phone$|',      'phone',    $_key);
            $_key = preg_replace('|^\w+_fax$|',        'fax',      $_key);
            $_key = preg_replace('|^\w+_domain|',      'domain',   $_key);
            $_key = preg_replace('|^\w+_md5|',         'md5',      $_key);
            $_key = preg_replace('|^\w+_phone_search$|', 'phone_search',  $_key);
            $_key = preg_replace('/^\w+_address[1|2]?$/', 'address',      $_key);
            $_key = preg_replace('/^address[1|2]?$/', 'address',          $_key);
            $method = "_is_{$key}";

            if ( ! method_exists($this, $method))
            {
                $method = "_is_{$_key}";

                if ( ! method_exists($this, $method))
                {
                    log_message('ERROR', "Not Implemented. [{$method}]");
                    show_error("Bad Request. [{$method} is not implemented]", 400);
                }
            }
            elseif ($this->_empty($val))
            {
                continue;
            }

            $this->_run_validate($val, $method, $key, $require);
        }

        if (count($this->_errors) > 0)
        {
            log_message('ERROR', "Validation Error !");
            log_message('ERROR', var_export($this->_errors, TRUE));
            return FALSE;
        }

        log_message('INFO', 'Validation End.');
        return $this->_data;
    }

    private function _run_validate($val, $method, $key, $require)
    {
        if (is_array($val))
        {
            foreach($val as $v)
            {
                if ($require && $this->_empty($v))
                {
                    if (isset($this->_key_maps[$key]))
                        $this->_errors[$key] = "「{$this->_key_maps[$key]}」は、必須項目です。";
                    else
                        $this->_errors[$key] = "[{$key}] is required.";

                    continue;
                }
                elseif ( ! $require && $this->_empty($v))
                {
                    continue;
                }

                $this->_run_validate($v, $method, $key, $require);
            }
        }
        elseif ( ! $this->{$method}($val))
        {
            log_message('ERROR', "validation error. [{$method}]");
            if (isset($this->_key_maps[$key]))
            {
                log_message('ERROR', "{$key}: {$val}");
                $this->_errors[$key] = "「{$this->_key_maps[$key]}」の値に誤りがあります。";
            }
            else
            {
                $this->_errors[$key] = "[{$key}] is invalid.";
            }
        }
    }

    // ============================================================================================

    protected function _validate_assoc($assoc, $requires=array())
    {
        $backup_data = $this->_data;
        $this->_data = $assoc;
        $ret = $this->run($requires);
        $this->_data = $backup_data;
        return $ret;
    }

    // ============================================================================================

    protected function _empty($val)
    {
        if ($val === '0' OR ( ! is_array($val) && preg_match('|^0\.0+$|', $val)) OR $val === 0 OR $val === 0.0)
        {
            return FALSE;
        }

        return empty($val);
    }

    // --------------------------------------------------------------------------------------------

    protected function _max_len($val, $max)
    {
        if (is_string($val) && $max >= mb_strlen($val))
        {
            return TRUE;
        }
        return FALSE;
    }

    // --------------------------------------------------------------------------------------------

    protected function _min_len($val, $min)
    {
        if (is_string($val) && $min <= mb_strlen($val))
        {
            return TRUE;
        }
        return FALSE;
    }

    // --------------------------------------------------------------------------------------------

    /**
     * 整数かな?
     */
    protected function _is_int($val, $min=NULL, $max=NULL)
    {
        //log_message('INFO', "_is_int({$val}, {$min}, {$max}) run.");
        if (filter_var($val, FILTER_VALIDATE_INT) === FALSE)
        {
            log_message('ERROR', "value is not 'integer'.");
            return FALSE;
        }

        if (filter_var($min, FILTER_VALIDATE_INT) !== FALSE && $min > $val)
        {
            log_message('ERROR', "value is less than 'min value'.");
            return FALSE;
        }

        if (filter_var($max, FILTER_VALIDATE_INT) !== FALSE && $max < $val)
        {
            log_message('ERROR', "value is more than 'min value'.");
            return FALSE;
        }

        return TRUE;
    }

    // --------------------------------------------------------------------------------------------

    /**
     * FLOATかな?
     */
    protected function _is_float($val, $min=NULL, $max=NULL)
    {
        log_message('INFO', "_is_float({$val}, {$min}, {$max}) run.");
        if (filter_var($val, FILTER_VALIDATE_FLOAT) === FALSE)
        {
            return FALSE;
        }

        if ((filter_var($min, FILTER_VALIDATE_FLOAT) !== FALSE OR filter_var($min, FILTER_VALIDATE_INT) !== FALSE) && $min > $val)
        {
            return FALSE;
        }

        if ((filter_var($max, FILTER_VALIDATE_FLOAT) !== FALSE OR filter_var($max, FILTER_VALIDATE_INT) !== FALSE) && $max < $val)
        {
            return FALSE;
        }

        return TRUE;
    }

    // --------------------------------------------------------------------------------------------

    /**
     * 数字かな?
     */
    protected function _is_digit($val, $min_len=NULL, $max_len=NULL)
    {
        log_message('INFO', "_is_digit({$val}, {$min}, {$max}) run.");
        if ( ! ctype_digit($val))
        {
            return FALSE;
        }

        if (filter_var($min_len, FILTER_VALIDATE_INT) !== FALSE && $min_len > strlen($val))
        {
            return FALSE;
        }

        if (filter_var($max_len, FILTER_VALIDATE_INT) !== FALSE && $max_len < strlen($val))
        {
            return FALSE;
        }

        return TRUE;
    }

    // --------------------------------------------------------------------------------------------

    /**
     * アルファベットかな?
     */
    protected function _is_alpha($val, $min_len=NULL, $max_len=NULL)
    {
        log_message('INFO', "_is_alpha({$val}, {$min_len}, {$max_len}) run.");
        if ( ! ctype_alpha($val))
        {
            return FALSE;
        }

        if (filter_var($min_len, FILTER_VALIDATE_INT) !== FALSE && $min_len > strlen($val))
        {
            return FALSE;
        }

        if (filter_var($max_len, FILTER_VALIDATE_INT) !== FALSE && $max_len < strlen($val))
        {
            return FALSE;
        }

        return TRUE;
    }

    // --------------------------------------------------------------------------------------------


    /**
     * アルファベットor数字かな?
     */
    protected function _is_alnum($val, $min_len=NULL, $max_len=NULL)
    {
        log_message('INFO', "_is_alnum({$val}, {$min_len}, {$max_len}) run.");
        if ( ! ctype_alnum($val))
        {
            return FALSE;
        }

        if (filter_var($min_len, FILTER_VALIDATE_INT) !== FALSE && $min_len > strlen($val))
        {
            return FALSE;
        }

        if (filter_var($max_len, FILTER_VALIDATE_INT) !== FALSE && $max_len < strlen($val))
        {
            return FALSE;
        }

        return TRUE;
    }

    protected function _is_alnum_word($val, $min_len=NULL, $max_len=NULL)
    {
        log_message('INFO', "_is_alnum_word({$val}, {$min_len}, {$max_len}) run.");
        if ( ! preg_match('|^[\w-]+$|', $val))
        {
            return FALSE;
        }

        if (filter_var($min_len, FILTER_VALIDATE_INT) !== FALSE && $min_len > strlen($val))
        {
            return FALSE;
        }

        if (filter_var($max_len, FILTER_VALIDATE_INT) !== FALSE && $max_len < strlen($val))
        {
            return FALSE;
        }

        return TRUE;
    }

    // --------------------------------------------------------------------------------------------

    /**
     * ASCII文字かな?
     */
    protected function _is_ascii($val, $min_len=NULL, $max_len=NULL)
    {
        log_message('INFO', "_is_ascii({$val}, {$min_len}, {$max_len}) run.");
        if ( ! preg_match('|^[\x21-\x7E]+$|', $val))
        {
            return FALSE;
        }

        if (filter_var($min_len, FILTER_VALIDATE_INT) !== FALSE && $min_len > strlen($val))
        {
            return FALSE;
        }

        if (filter_var($max_len, FILTER_VALIDATE_INT) !== FALSE && $max_len < strlen($val))
        {
            return FALSE;
        }

        return TRUE;
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_bool($val)
    {
        log_message('INFO', "_is_bool({$val}) run.");
        if ($val == '0' || $val == '1')
        {
            return TRUE;
        }
        return FALSE;
    }

    // --------------------------------------------------------------------------------------------


    protected function _is_url($val)
    {
        log_message('INFO', "_is_url({$val}) run.");
        $len = strlen($val);

        if ($len > 100 OR (filter_var($val, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) === FALSE) OR ! preg_match('|^https?://|', $val))
        {
            return FALSE;
        }

        return TRUE;
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_mail($val, $max=50)
    {
        log_message('INFO', "_is_mail({$val}) run.");
        if (filter_var($val, FILTER_VALIDATE_EMAIL) !== FALSE && $this->_max_len($val, $max) && strlen($val) > 5)
        {
            return TRUE;
        }

        return FALSE;
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_md5($val)
    {
        log_message('INFO', "_is_md5({$val}) run.");
        return preg_match('|^[0-9a-f]{32}$|', $val);
    }

    // --------------------------------------------------------------------------------------------


    protected function _is_time($val)
    {
        log_message('INFO', "_is_time({$val}) run.");
        return preg_match('/^([0-1]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $val);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_date($val)
    {
        log_message('INFO', "_is_date({$val}) run.");
        return preg_match('/^(19|20)\d\d-[0-1]\d-[0-3]\d$/', $val);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_day($val)
    {
        log_message('INFO', "_is_day({$val}) run.");
        if ($this->_is_int($val) && (int)$val > 0 && (int)$val < 29)
        {
            return TRUE;
        }
        return FALSE;
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_id($val, $include_zero=FALSE)
    {
        log_message('INFO', "_is_id({$val}, ".var_export($include_zero, TRUE).") run.");
        $min = 1;

        if ($include_zero === TRUE)
            $min = 0;

        return $this->_is_int($val, $min, 99999999999);
    }


    // --------------------------------------------------------------------------------------------

    protected function _is_phone($val)
    {
        log_message('INFO', "_is_phone($val) run.");
        $len = strlen($val);

        if ((preg_match('|^0[5-9]0-?\d{4}-?\d{4}$|', $val) OR preg_match('|^0\d-?\d{4}-?\d{4}$|', $val) OR preg_match('|^0\d\d-?\d{3}-?\d{4}$|', $val) OR preg_match('|^0\d{3}-?\d{2}-?\d{4}$|', $val) OR preg_match('|^0120-?\d{3}-?\d{3}$|', $val) OR preg_match('|^\d{1,4}-?\d{4}$|', $val)) && $len <= 13 && $len >= 8)
        {
            return TRUE;
        }

        return FALSE;
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_fax($val)
    {
        log_message('INFO', "_is_fax($val) run.");
        $len = strlen($val);

        if ((preg_match('|^0\d-?\d{4}-?\d{4}$|', $val) OR preg_match('|^0\d\d-?\d{3}-?\d{4}$|', $val) OR preg_match('|^0\d{3}-?\d{2}-?\d{4}$|', $val) OR preg_match('|^0120-?\d{3}-?\d{3}$|', $val) OR preg_match('|^\d{1,4}-?\d{4}$|', $val)) && $len <= 12 && $len >= 8)
        {
            return TRUE;
        }

        return FALSE;
    }

    // --------------------------------------------------------------------------------------------


    protected function _is_postcode($val)
    {
        log_message('INFO', "_is_postcode({$val}) run.");
        return preg_match('|^\d\d\d-\d\d\d\d$|', $val);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_domain($val)
    {
        log_message('INFO', "_is_domain({$val}) run.");

        if (preg_match('|^([A-Za-z0-9][A-Za-z0-9\-]{1,61}[A-Za-z0-9]\.)+[A-Za-z]+$|', $val))
            return TRUE;
        else
            return FALSE;
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_since($val)
    {
        log_message('INFO', "_is_since({$val}) run.");
        return $this->_is_date($val);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_until($val)
    {
        log_message('INFO', "_is_until({$val}) run.");
        return $this->_is_date($val);
    }

    // --------------------------------------------------------------------------------------------


    protected function _is_status($val)
    {
        log_message('INFO', "_is_status({$val}) run.");
        return $this->_is_int($val, -1, 9);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_flag($val)
    {
        log_message('INFO', "_is_flag({$val}) run.");
        return $this->_is_bool($val);
    }

}

