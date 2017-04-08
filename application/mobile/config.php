<?php
return [
    // 默认输出类型
    'default_return_type' => 'html',
    'view_replace_str'    => [
        '__PUBLIC__' => __ROOT__ . '/static/' . request()->module() . '',
        '__JS__'     => __ROOT__ . '/static/' . request()->module() . '/js',
        '__CSS__'    => __ROOT__ . '/static/' . request()->module() . '/css',
        '__IMG__'    => __ROOT__ . '/static/' . request()->module() . '/img',
        '__SELF__' => request()->url(),
        '__CDN__'    => ITBOYE_CDN,

    ],

    'site_url' => 'https://apidev.8raw.com/mobile.php',
    //'site_url' => 'http://127.0.0.1/github/itboye_hutouben_api/public/mobile.php',
    'by_api_config'       =>[
    'alg'=>'md5_v2',
    'client_id'=>'by571846d03009e1',
    'client_secret'=>'964561983083ac622f03389051f112e5',
    'api_url'=>'https://apidev.8raw.com/index.php',
    'debug'=> false
    ]
];