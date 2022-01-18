<?php
/**
 * Common_validation Class
 *
 * @project     Hairlogy
 * @package     Libarary
 * @author      yuki.hatano@gmail.com
 * @copyright   montecampo Co., Ltd. All Rights Reserved
 */

class Common_validation extends MY_validation {


    public function __construct($data=array())
    {
        parent::__construct($data);
        $this->_key_maps = array_merge($this->_key_maps, array(
            'loginid'       => 'ログインID',
            'loginpw'       => 'ログインパスワード',
            'phone'         => '電話番号',
            'fax'           => 'FAX番号',
            'postcode1'     => '郵便番号',
            'postcode2'     => '郵便番号',
            'address'       => '住所1',
            'note'          => 'メモ',
            'since_date'    => '開始日',
            'until_date'    => '終了日',
        ));
        log_debug('Common_validation Class Initialized');
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_month($val)
    {
        log_debug("_is_month({$val}) run.");
        return preg_match('/^(20|19)\d\d-[0-1]\d(-01)?$/', $val);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_note($val)
    {
        log_info("_is_note({$val}) run.");
        return $this->_max_len($val, 250);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_loginid($val)
    {
        log_info("_is_loginid({$val}) run.");
        return $this->_is_mail($val, 50);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_loginpw($val)
    {
        log_info("_is_loginpw(********) run.");
        return $this->_is_ascii($val, 6, 20);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_postcode1($val)
    {
        log_info("_is_postcode1({$val}) run.");
        return preg_match('|^\d\d\d$|', $val);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_postcode2($val)
    {
        log_info("_is_postcode2({$val}) run.");
        return preg_match('|^\d\d\d\d$|', $val);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_address($val)
    {
        return $this->_max_len($val, 50);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_phone_search($val)
    {
        return preg_match('|^0\d{9,10}$|', $val);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_cmyk($val)
    {
        log_debug("[{$val}]");
        return $this->_is_int($val, 0, 100);
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_kana($val, $max=20)
    {
        $len = mb_strlen($val);

        if ($len <= $max)
        {
            $val = str_replace(array(' ', '　'), '', $val);

            if (preg_match("/^[ぁ-ゞァ-ヾ（）・０-９]+$/u", $val))
            {
                return TRUE;
            }
        }
        return FALSE;
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_name($val, $max=20)
    {
        $len = mb_strlen($val);

        if ($len <= $max)
        {
            return TRUE;
        }
        return FALSE;
    }

    // --------------------------------------------------------------------------------------------

    protected function _is_csrf($val)
    {
        return preg_match('|^[0-9a-f]{32}$|', $val);
    }

}

