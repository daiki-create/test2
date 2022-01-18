<?php

class Inquiry_validation extends Common_validation
{
    public function __construct($post=[])
    {
        parent::__construct($post);
        $this->_key_maps = array_merge($this->_key_maps, array(
            'inquiry' => 'ご質問・お問い合わせ内容',
            'name'    => 'お名前',
            'mail'    => 'メールアドレス',
        ));
        log_debug('Inquiry_validation Initialized.');
    }

    public function _is_inquiry($val, $max="1000")
    {
        return parent::_is_note($val, $max);
    }

}