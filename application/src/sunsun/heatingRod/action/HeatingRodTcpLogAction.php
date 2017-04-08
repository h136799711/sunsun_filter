<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\heatingRod\action;
use app\src\sunsun\heatingRod\logic\HeatingRodTcpLogLogic;


/**
 * Class HeatingRodDeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\heatingRod
 */
class HeatingRodTcpLogAction extends HeatingRodBaseAction
{

    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $result = (new HeatingRodTcpLogLogic())->delete(['create_time'=>['lt',$dataTimestamp]]);
        return $result;
    }
}