<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-04-07
 * Time: 11:40
 */

namespace app\src\sunsun\common\helper;


class SnHelper
{
    public static function getSn(){
        $str = "".time().rand(0,1000);
        $str = substr($str,2,strlen($str));
        $sn =  intval($str);
        if($sn > 2147483648){
            return $sn % 2147483648;
        }
        return $sn;
    }
}