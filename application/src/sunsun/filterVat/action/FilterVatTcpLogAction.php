<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\filterVat\action;
use app\src\sunsun\filterVat\logic\FilterVatTcpLogLogic;


/**
 * Class FilterVatDeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\filterVat
 */
class FilterVatTcpLogAction extends FilterVatBaseAction
{

    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $result = (new FilterVatTcpLogLogic())->delete(['create_time'=>['lt',$dataTimestamp]]);
        return $result;
    }
}