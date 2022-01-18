<?php

class MY_Library {

    protected $_errors = [];
    public $CI;

    // --------------------------------------------------------------------------------

    public function __construct()
    {
        $name = get_class($this);
        $this->CI = & get_instance();
        log_info("{$name} Library Initialized");
    }

    // --------------------------------------------------------------------------------

    /**
     * return error messages
     */
    public function errors($error_message=NULL)
    {
        if (is_array($error_message))
            $this->_errors = $error_message;
        elseif (is_string($error_message))
            $this->_errors[] = $error_message;

        return $this->_errors;
    }

}

