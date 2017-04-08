<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

namespace app\mobile\helper;

use app\src\base\helper\ConfigHelper;
use app\src\base\utils\CacheUtils;
use think\Config;

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-29
 * Time: 14:24
 * 电脑网页端配置信息帮助工具类
 */
class MobileConfigHelper extends ConfigHelper
{
    /**
     * 配置初始化
     */
    public static function init(){
        //TODO: 这里获取的是全部，之后考虑只获取电脑端配置信息，进行过滤
        $config = CacheUtils::initAppConfig();
        foreach ($config as $key=>$value){
            Config::set($key,$value);
        }
    }
}