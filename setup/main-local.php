<?php


$config = [
    'components' => [
        'request' => [
            // itakda ang cookie validation key dito (awtomatiko itong may laman)
            'cookieValidationKey' => '8mLmX7OmarXq3zInYPIH0ISD1aZhBQBu',
        ],
    ],
];

if (YII_ENV_DEV) {
    /*$config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.*.*'], // Idagdag ang iyong IP dito
    ];*/
}
return $config;
