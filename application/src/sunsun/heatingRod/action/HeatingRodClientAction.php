<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\heatingRod\action;
use app\src\sunsun\common\helper\SnHelper;
use app\src\sunsun\heatingRod\logic\HeatingRodDeviceLogic;
use GatewayClient\Gateway;
use sunsun\heating_rod\req\HeatingRodDeviceUpdateReq;


/**
 * Class HeatingRodDeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\heatingRod
 */
class HeatingRodClientAction extends HeatingRodBaseAction
{
    public function sendMessage($did,$message){
        return $this->sendReqToClient($did,$message,new HeatingRodDeviceLogic());
    }

    public function update($did,$url,$len=0){
        $req = new HeatingRodDeviceUpdateReq();
        $req->setUrl($url);
        $req->setSn(SnHelper::getSn());
        $req->setLen($len);
        return $this->sendReqToHeatingRodClient($did,$req);
    }

    /**
     * 获取设备信息
     * @return int
     */
    public function allClientCount(){
        return Gateway::getAllClientCount();
    }

    public function getSession($did){
        $client_id = Gateway::getClientIdByUid($did);
        if(count($client_id) > 0){
            return Gateway::getSession($client_id[0]);
        }

        return null;
    }


}