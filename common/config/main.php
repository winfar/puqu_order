ssssssss<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
//         'cache' => [
//             'class' => 'yii\caching\MemCache',
//             'servers' => [
//                 [
//                     'host' => '127.0.0.1',
//                     'port' => 11211,
//                     'weight' => 60,
//                 ],
//             ],
//         ],

        // 'db' => [
        //     'class' => 'yii\db\Connection',
        //     'dsn' => 'mysql:host=192.168.109.216;dbname=yiiboot_test',
        //     'username' => 'root',
        //     'password' => '123456',
        //     'charset' => 'utf8',
        // ],

        // 'db' => [
        //     'class' => 'yii\db\Connection',
        //     'dsn' => 'mysql:host=127.0.0.1;dbname=yiiboot_test',
        //     'username' => 'root',
        //     'password' => 'root',
        //     'charset' => 'utf8',
        // ],

        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=order',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => true,
        ],
    ],
    
];
