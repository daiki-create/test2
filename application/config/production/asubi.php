<?php

/*  --------------------------------------------------------------------
 * Pay.jp
 */
$config['payjp'] = [
    'private_key' => 'sk_live_0fe183c1921484fbaeaf55e7c922c31c099fd2e219c1a24c4f7fb56b',
    'public_key'  => 'pk_live_351d1ec0b1c174899055c4d6',
    'customer_prefix'  => 'asubi',
    'default_currency' => 'jpy',
    'subscription_plan' => [
        'id'          => 'asubi_subscription_plan',
        'name'        => 'asubi月会費',
        'amount'      => 1000,
        'currency'    => 'jpy',
        'interval'    => 'month',
        'trial_days'  => 0,
        'billing_day' => 1,
    ],
];

/* --------------------------------------------------------------------
 * Mail
 */
$config['asubi_notice_mail'] = array(
    'from'  => 'asubi@montecampo.co.jp',
    'to'    => 'asubi@montecampo.co.jp',
);

/* --------------------------------------------------------------------
 * Facebook Group
 */
$config['asubi_facebook_group'] = [
    'url' => 'https://www.facebook.com/groups/asubi',
];


