<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 15:02
 */

namespace app\src\admin\helper;

use app\src\base\helper\ConfigHelper;
use app\src\base\utils\CacheUtils;
use think\Config;

class AdminConfigHelper extends ConfigHelper
{

    /**
     * 获取博也接口参数配置
     * @return mixed
     */
    public static function getByApiConfig(){
        return config('by_api_config');
    }




}