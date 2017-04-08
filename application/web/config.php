<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-18
 * Time: 16:09
 */

return [

    'default_return_type'    => 'html',
    'view_replace_str' => [
        '__PUBLIC__' => __ROOT__ . '/static/' . request()->module() . '',
        '__JS__' => __ROOT__ . '/static/' . request()->module() . '/js',
        '__CSS__' => __ROOT__ . '/static/' . request()->module() . '/css',
        '__IMG__' => __ROOT__ . '/static/' . request()->module() . '/img',
        '__CDN__' => ITBOYE_CDN,
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => APP_PATH . '/web/view/dispatch/dispatch_jump.tpl',
    'dispatch_error_tmpl'    => APP_PATH . '/web/view/dispatch/dispatch_jump.tpl',
    
    'by_api_config'=>[
        'alg'=>'md5_v2',
        'client_id'=>'by571846d03009e1',
        'client_secret'=>'964561983083ac622f03389051f112e5',
        'api_url'=>'https://apidev.8raw.com/index.php',
        'debug'=> false
    ],
];