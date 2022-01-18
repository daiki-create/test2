<?php

/* --------------------------------------------------------------------
 * Revision
 */
$config['revision'] = '2020100101';

/* --------------------------------------------------------------------
 * Login
 */
$config['login_ttl'] = config_item('sess_expiration');

/* --------------------------------------------------------------------
 * Data Directory
 */
$config['tmp_dir']  = PROJECTPATH.'/tmp';
$config['data_dir'] = PROJECTPATH.'/data';

/* --------------------------------------------------------------------
 * Mail
 */
$config['notice_mail'] = array(
    'from'  => 'yuki.hatano@gmail.com',
    'to'    => 'yuki.hatano@gmail.com',
);

$config['error_mail'] = array(
    'from'  => 'yuki.hatano@gmail.com',
    'to'    => 'yuki.hatano@gmail.com',
);

/* --------------------------------------------------------------------
 * SNS
 */
// hairlogy - production と同値
$config['facebook'] = [
    'app_id'       => '472531300062373',
    'app_secret'   => '68c34bab85c0f43d4d8b3499373b1f0a',
    'callback'     => 'salon/login/callback/facebook/',
    'auth_provider'=> 'facebook',
];

$config['line'] = [
    'app_id'     => '1653638784',
    'app_secret' => '116a6400b153478c4268e4d321b38f9b',
    'callback'   => 'salon/login/callback/line/',
    'auth_provider' => 'line',
    'login_url'     => 'https://access.line.me/oauth2/v2.1/authorize',
    'request_token_url'  => 'https://api.line.me/oauth2/v2.1/token',
    'request_verify_url' => 'https://api.line.me/oauth2/v2.1/verify',
];

/* --------------------------------------------------------------------
 * Questionaire
 */
$config['reply_interval_days'] = 14;

$config['default_colors'] = [
    'selections' => [
        '#E83B1D', //0
        '#F5660D', //1
        '#FF6600', //2
        '#F8DA52', //3
        '#7CCD7C', //4
        '#9AFF9A', //5
        '#549644', //6
        '#326B96', //7
        '#0000FF', //8
        '#87CEFA', //9
        '#AFEEEE'  //10
    ],
];

/*  --------------------------------------------------------------------
 * Youtube
 */
$config['youtube'] = [
    'url'        => 'https://www.googleapis.com/youtube/v3/search?part=snippet&order=date',
    'channel_id' => 'UCOi0MOg5c2mH9Orarp6A8WA&',
    'key'        => 'AIzaSyBLzd6RgCXYzoJREwsyRL5DZym5OmHi9ok'
];