<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-04-08
 * Time: 10:22
 */

namespace app\src\base\helper;


class TimeHelper
{
    public static function toDatetime($time,$format='Y-m-d H:i:s'){
        $timezone = intval(cookie('timezone'));
        if($timezone >= -12 && $timezone <= 12){
            $time = $timezone * 3600 + $time;
        }
        return date($format,$time);
    }
}