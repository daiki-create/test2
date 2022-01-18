<?php

/* --------------------------------------------------------------------
 * Revision
 */
$config['revision'] = '2020100100';

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
    'from'  => 'monte.ishida@gmail.com',
    'to'    => 'monte.ishida@gmail.com',
);

$config['error_mail'] = array(
    'from'  => 'monte.ishida@gmail.com',
    'to'    => 'monte.ishida@gmail.com',
);

/* --------------------------------------------------------------------
 * SNS
 */
// hairlogy - development
$config['facebook'] = [
    'app_id'       => '2646779752023885',
    'app_secret'   => 'ce7d2df9859396978a769dc65435cff0',
    'callback'     => 'salon/login/callback/facebook/',
    'auth_provider'=> 'facebook',
];


$config['line'] = [
    'app_id'     => '1653637034',
    'app_secret' => '16beee35d7637a158cdee66ea3eff59e',
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
        '#4dd0e1', // rgba(77, 208, 225, 1)',
        '#26c6da', // rgba(38, 198, 218, 1)',
        '#03a9f4', // rgba(3, 169, 244, 1)',
        '#0288d1', // rgba(2, 136, 209, 1)',
        '#1976d2', // rgba(25, 118, 210, 1)',
        '#0d47a1', // rgba(13, 71, 161, 1)',
        '#1a237e', // rgba(26, 35, 126, 1)',
        '#01579b', // rgba(1, 87, 155, 1)',
        '#00796b', // rgba(0, 121, 107, 1)',
        '#00838f', // rgba(0, 131, 143, 1)',
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

