<?php

/*  --------------------------------------------------------------------
 * Pay.jp
 */
$config['payjp'] = [
    'private_key' => 'sk_test_5bcfeabf231e2bc23ddbeca7',
    'public_key'  => 'pk_test_67a9874ff148de807dda64e8',
    'customer_prefix'  => defined('ASUBI_PAYJP_CUSTOMER_PREFIX') ? ASUBI_PAYJP_CUSTOMER_PREFIX : 'asubi_dev_ishida',
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
$config['asubi_notice_mail'] = [
    'from' => 'asubi@montecampo.co.jp',
    'to'   => 'monte.ishida@gmail.com',
];

/* --------------------------------------------------------------------
 * Facebook Group
 */
$config['asubi_facebook_group'] = [
    'url' => 'https://facebook.com/groups/',
];