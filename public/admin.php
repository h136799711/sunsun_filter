<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All ights reserved
 */

// [ 应用入口文件 ]
//UTC: Etc/GMT
//东八区：Asia/Shanghai

$self = strip_tags($_SERVER['REQUEST_URI']);
if(strlen($self) > 9 && strpos($self,"admin.php") == strlen($self) - 9){
    header("Location: $self/admin/index/login");
}
// URL常量
define('__SELF__',strip_tags($_SERVER['REQUEST_URI']));
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');


define('RUNTIME_PATH',__DIR__ . '/../runtime/');

// 开始运行时间和内存使用
define('START_TIME', microtime(true));
define('START_MEM', memory_get_usage());
//环境变量
define('IS_CLI_', PHP_SAPI == 'cli' ? true : false);
define('NOW_TIME', $_SERVER['REQUEST_TIME']);


// 当前文件名
if(!defined('_PHP_FILE_')) {
    $_temp  = explode('.php',$_SERVER['PHP_SELF']);
    define('_PHP_FILE_',    rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/'));
}
if(!defined('__ROOT__')) {
    $_root  =   rtrim(dirname(_PHP_FILE_),'/');
    define('__ROOT__',  (($_root=='/' || $_root=='\\')?'':$_root));
}
// 加载框架引导文件
require __DIR__ . '/../vendor/topthink/framework/start.php';