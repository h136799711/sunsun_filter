<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\common\action;
use app\src\base\action\BaseAction;
use app\src\sunsun\common\logic\LogLogic;


/**
 * Class TcpLogAction
 * tcp
 * @package app\src\sunsun\filterVat
 */
class TcpLogAction extends BaseAction
{

    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $result = (new LogLogic())->delete(['create_time'=>['lt',$dataTimestamp]]);
        return $result;
    }
}