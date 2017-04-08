<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\filterVat\action;
use app\src\sunsun\common\helper\SnHelper;
use app\src\sunsun\filterVat\logic\FilterVatDeviceLogic;
use GatewayClient\Gateway;
use sunsun\filter_vat\req\FilterVatDeviceUpdateReq;


/**
 * Class FilterVatDeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\filterVat
 */
class FilterVatClientAction extends FilterVatBaseAction
{
    public function sendMessage($did,$message){
        return $this->sendReqToClient($did,$message,new FilterVatDeviceLogic());
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


    public function update($did,$url,$len=0){
        $req = new FilterVatDeviceUpdateReq();
        $req->setUrl($url);
        $req->setSn(SnHelper::getSn());
        $req->setLen($len);
        return $this->sendReqToFilterVatClient($did,$req);
    }
}