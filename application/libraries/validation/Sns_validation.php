<?php

class Sns_validation extends Common_validation
{
    public function __construct($post=[])
    {
        parent::__construct($post);
        $this->_key_maps = array_merge($this->_key_maps, array(
            'auth_provider' => '認証プロバイダ',
        ));
        log_debug('Sns_validation Initialized.');
    }

    public function _is_auth_provider($val)
    {
        if ($val == 'facebook' OR $val == 'line' OR $val == 'yahoo' OR
            $val == 'twitter' OR $val == 'google' OR $val == 'instagram')
            return TRUE;

        return FALSE;
    }

    public function _is_state($val)
    {
        return preg_match('|^[0-9a-f]{32}$|', $val);
    }

}