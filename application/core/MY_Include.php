<?php

require_once APPPATH.'core/MY_Library.php';
define('CI_MODULE', getenv('CI_MODULE'));

if (is_cli())
{
    require_once APPPATH.'core/MYCLI_Controller.php';
}
elseif (defined('CI_MODULE') && CI_MODULE == 'dl')
{
    require_once APPPATH."core/MYDL_Controller.php";
}
elseif (defined('CI_MODULE') && CI_MODULE == 'api')
{
    require_once APPPATH."core/MYAPI_Controller.php";
}
elseif (defined('CI_MODULE') && CI_MODULE == 'ajax')
{
    require_once APPPATH."core/MYAJAX_Controller.php";
}
elseif (defined('CI_MODULE') && CI_MODULE == 'salon')
{
    require_once APPPATH."core/MYWWW_Controller.php";
    require_once APPPATH."core/MYSALON_Controller.php";
}
elseif (defined('CI_MODULE') && CI_MODULE == 'sysadm')
{
    require_once APPPATH."core/MYWWW_Controller.php";
    require_once APPPATH."core/MYSYSADM_Controller.php";
}
elseif (defined('CI_MODULE') && CI_MODULE == 'asubi')
{
    require_once APPPATH."core/MYWWW_Controller.php";
    require_once APPPATH."core/MYASUBI_Controller.php";
}
else
{
    require_once APPPATH."core/MYWWW_Controller.php";
}


