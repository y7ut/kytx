<?php
/*
 * 测试环境配置文件
 */
return [
    'settings' => [
        'displayErrorDetails' => true,
        'enableQueryLog' => true,// 开启sql 日志记录
        'DIR_LOG' => __DIR__.'/../../runtime/log',
        'DIR_CACHE' => false,
        'mysql' => [
            'default' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => 'kytx',
                'username' => 'root',
                'password' => '',
                'charset'   => 'utf8',
                'prefix'    => '',
            ]
        ],
    ]
];
