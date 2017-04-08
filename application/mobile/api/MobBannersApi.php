<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-14
 * Time: 17:01
 */

namespace app\mobile\api;


use app\src\admin\api\BaseApi;
use app\src\admin\helper\ByApiHelper;

class MobBannersApi extends BaseApi
{

    public function query($position = 6198,$url_type=6070){

        $data = [
            'type'=>'By_Banners_query',
            'api_ver'=>'101',
            'notify_id'=>self::getNotifyId(),
            'position' => $position,
            'url_type'=>$url_type
        ];

        return ByApiHelper::getInstance()->callRemote($data);
    }

}