<?php

class Salon_validation extends Common_validation {

    public function __construct($post=[])
    {
        parent::__construct($post);
        $this->_key_maps = array_merge($this->_key_maps, array(
            'name'  => 'サロン名',
            'phone' => '電話番号(サロン)',
        ));
        log_debug('Salon_validation Initialized.');
    }

    public function _is_name($val, $max=50)
    {
        return parent::_is_name($val, $max);
    }

}

