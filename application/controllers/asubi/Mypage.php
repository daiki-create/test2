<?php

/**
 * Class Mypage
 */
class Mypage extends MYASUBI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    // --------------------------------------------------------------------------------------------------------------------------

    public function index()
    {
        $facebook_group = config_item('asubi_facebook_group');
        $this->view->assign('facebook_group_url', $facebook_group['url']);
    }

}
