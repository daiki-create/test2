<?php

class Stylist_validation extends Common_validation {

    public function __construct($post=[])
    {
        parent::__construct($post);
        $this->_key_maps = array_merge($this->_key_maps, array(
            'name'  => '氏名',
            'kana'  => '氏名(カナ)',
            'phone' => '電話番号(スタイリスト)',
            'manager_flag'     => '管理者',
            'new_password'     => '新しいパスワード',
            'confirm_password' => '新しいパスワード（確認）',
            'trial_limited_on' => 'トライアル終了日',
            'new_salon_id'     => '所属サロン',
        ));
        log_debug('Stylist_validation Initialized.');
    }

    public function _is_new_salon_id($val)
    {
        return parent::_is_id($val, TRUE);
    }

    public function _is_name($val, $max=30)
    {
        return parent::_is_name($val, $max);
    }

    public function _is_kana($val, $max=50)
    {
        return parent::_is_kana($val, $max);
    }

    public function _is_new_password($val)
    {
        return parent::_is_loginpw($val);
    }

    public function _is_confirm_password($val)
    {
        return parent::_is_loginpw($val);
    }

    public function _is_trial_limited_on($val)
    {
        return parent::_is_date($val);
    }

}

