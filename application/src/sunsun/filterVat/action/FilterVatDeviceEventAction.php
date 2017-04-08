<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\filterVat\action;

use app\src\sunsun\filterVat\logic\FilterVatDeviceEventLogic;
use app\src\sunsun\filterVat\model\FilterVatDeviceEvent;
use GatewayClient\Gateway;
use sunsun\filter_vat\req\FilterVatDeviceEventReq;


/**
 * Class FilterVatDeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\filterVat
 */
class FilterVatDeviceEventAction extends FilterVatBaseAction
{

    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $map['create_time'] = ['lt',$dataTimestamp];
        $result = (new FilterVatDeviceEventLogic())->delete($map);
        return $result;
    }

    public function add($did,$eventType){
        $req = new FilterVatDeviceEventReq();
        $req->setCode($eventType);
        $eventInfo = json_encode(['code_desc'=>$req->getCodeDesc()]);
        $data = [
            'did'=>$did,
            'event_type'=>$eventType,
            'event_info'=>$eventInfo,
            'create_time'=>time(),
            'update_time'=>time(),
            'pro_status'=>FilterVatDeviceEvent::PRO_STATUS_NOT_PROCESS
        ];
        $result = (new FilterVatDeviceEventLogic())->add($data);
        return $result;
    }

}