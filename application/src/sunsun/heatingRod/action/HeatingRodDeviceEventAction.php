<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\heatingRod\action;

use app\src\sunsun\heatingRod\logic\HeatingRodDeviceEventLogic;
use app\src\sunsun\heatingRod\model\HeatingRodDeviceEvent;
use GatewayClient\Gateway;
use sunsun\heating_rod\req\HeatingRodDeviceEventReq;


/**
 * Class HeatingRodDeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\heatingRod
 */
class HeatingRodDeviceEventAction extends HeatingRodBaseAction
{
    public function sendMessage($did,$message){
        $this->sendToClient($did,$message);
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
//        return $client_id;
        return Gateway::getSession($client_id[0]);
    }

    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $map['create_time'] = ['lt',$dataTimestamp];
        $result = (new HeatingRodDeviceEventLogic())->delete($map);
        return $result;
    }


    public function add($did,$eventType){
        $req = new HeatingRodDeviceEventReq();
        $req->setCode($eventType);
        $eventInfo = json_encode(['code_desc'=>$req->getCodeDesc()]);
        $data = [
            'did'=>$did,
            'event_type'=>$eventType,
            'event_info'=>$eventInfo,
            'create_time'=>time(),
            'update_time'=>time(),
            'pro_status'=>HeatingRodDeviceEvent::PRO_STATUS_NOT_PROCESS
        ];
        $result = (new HeatingRodDeviceEventLogic())->add($data);
        return $result;
    }

}