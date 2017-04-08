<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\filterVat\action;
use app\src\base\helper\ResultHelper;
use app\src\base\helper\ValidateHelper;
use app\src\sunsun\common\helper\SnHelper;
use app\src\sunsun\common\logic\UserDeviceLogic;
use app\src\sunsun\filterVat\logic\FilterVatDeviceLogic;
use sunsun\filter_vat\req\FilterVatDeviceInfoReq;


/**
 * Class FilterVatDeviceInfoAction
 * 获取设备信息
 * @package app\src\sunsun\filterVat
 */
class FilterVatDeviceInfoAction extends FilterVatBaseAction
{
    /**
     * 获取设备信息
     * @param $did
     * @param $uid
     * @return array
     */
    public function deviceInfo($did,$uid=0){
        if(intval($uid) < 0){
            return ResultHelper::error('该用户不存在');
        }
        if($uid ==  0){
            $deviceInfo = [];
        }else{
            $result = (new UserDeviceLogic())->getInfo(['uid'=>$uid,'did'=>$did]);
            if(!ValidateHelper::legalArrayResult($result)){
                return ResultHelper::error('该设备信息不存在');
            }
            $deviceInfo = $result['info'];
        }
        $req = new FilterVatDeviceInfoReq();
        $req->setSn(SnHelper::getSn());
        $this->sendReqToFilterVatClient($did,$req);
        $result = (new FilterVatDeviceLogic())->getInfo(['did'=>$did]);
        if(!ValidateHelper::legalArrayResult($result)){
            return ResultHelper::error('该设备信息不存在');
        }

        $result['info'] = array_merge($this->processDevice($result['info']),$deviceInfo);


        return $result;
    }

    private function processDevice($info){
        if(!is_array($info)){
            return $info;
        }
        //增加一个设备断开时间
        $update_time = strtotime($info['update_time']);
        $hb = $info['hb'];
        //3倍的心跳时间作为设备断开判断依据
        //相当于重试3次
        $info['logout_time'] = $update_time + 3*$hb;
        $info['is_disconnect'] = 0;
        if($info['logout_time'] < time()){
            $info['is_disconnect'] = 1;
        }
        //如果tcp通道id为空，则设备已断开
        if(empty($info['tcp_client_id'])){
            $info['is_disconnect'] = 2;
        }
        unset($info['logout_time']);
        return $info;
    }
}