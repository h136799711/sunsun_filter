<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\common\action;
use app\src\base\action\BaseAction;
use app\src\sunsun\heatingRod\logic\HeatingRodTempHisLogic;


/**
 * Class HeatingRodTempHisAction
 * tcp
 * @package app\src\sunsun\filterVat
 */
class HeatingRodTempHisAction extends BaseAction{
    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $result = (new HeatingRodTempHisLogic())->delete(['create_time'=>['lt',$dataTimestamp]]);
        return $result;
    }
}