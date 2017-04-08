<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\heatingRod\action;
use app\src\base\helper\ResultHelper;
use app\src\sunsun\common\helper\SnHelper;
use sunsun\heating_rod\req\HeatingRodCtrlDeviceReq;


/**
 * Class HeatingRodDeviceInfoAction
 * 获取设备信息
 * @package app\src\sunsun\heatingRod
 */
class HeatingRodDeviceCtrlAction extends HeatingRodBaseAction
{
    /**
     * 获取设备信息
     * @param $did
     * @param $data
     * @return array
     */
    public function ctrl($did,$data){
        if(empty($data)){
            return ResultHelper::error('操作失败(参数缺少)');
        }
        $req = new HeatingRodCtrlDeviceReq();
        $req->setSn(SnHelper::getSn());
        $req->setData($data);
        $result = $this->sendReqToHeatingRodClient($did,$req);
        return $result;
    }
}