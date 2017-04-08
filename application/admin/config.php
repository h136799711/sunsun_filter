<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 15:23
 */

return [
    'USER_ADMINISTRATOR'=>1,
    'session'             => [
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => 'itboye_sid',
        // SESSION 前缀
        'prefix'         => 'itboye_admin',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true
    ],
    'by_api_config'=>[
        'client_id'=>'by571846d03009e1',
        'client_secret'=>'964561983083ac622f03389051f112e5',
        'api_url'=>'http://devsunsun.8raw.com/index.php'
    ],
    'baidu_map_key'=> 'bV4eyokvXF2Z36PeTbww7fHQ',
    'default_module'         => 'admin',
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'login',

    'default_return_type'    => 'html',
    'view_replace_str' => [
        '__UPLOAD__' => __ROOT__.'/static/upload', // 更改默认的/Public 替换规则
        '__PUBLIC__' => __ROOT__ . '/static/' . request()->module(),
        '__JS__' => __ROOT__ . '/static/' . request()->module() . '/js',
        '__CSS__' => __ROOT__ . '/static/' . request()->module() . '/css',
        '__IMG__' => __ROOT__ . '/static/' . request()->module() . '/imgs',
        '__LEARUN__' => __ROOT__ . '/static/'. request()->module() . '/learun',
        '__CDN__' => ITBOYE_CDN,
        '__APP_VERSION__'=> time(),
        '__SELF__'=>__SELF__
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => APP_PATH . '/web/view/dispatch/dispatch_jump.tpl',
    'dispatch_error_tmpl'    => APP_PATH . '/web/view/dispatch/dispatch_jump.tpl',

];