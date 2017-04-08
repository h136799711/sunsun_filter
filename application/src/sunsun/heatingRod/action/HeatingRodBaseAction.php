<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:38
 */

namespace app\src\sunsun\heatingRod\action;

use app\src\base\helper\ResultHelper;
use app\src\base\helper\ValidateHelper;
use app\src\base\logic\BaseLogic;
use app\src\sunsun\common\helper\SunsunEncodeHelper;
use app\src\sunsun\heatingRod\logic\HeatingRodDeviceLogic;
use GatewayClient\Gateway;
use sunsun\po\BaseReqPo;
use think\Exception;


require_once __DIR__ . '/../../../../../vendor/autoload.php';

//设置register服务地址
//过滤桶register服务 端口1237
Gateway::$registerAddress = "101.37.37.167:1239";

class HeatingRodBaseAction
{

    /**
     * 发送数据到 过滤桶设备
     * @param $did
     * @param BaseReqPo $req
     * @return array
     */
    protected function sendReqToHeatingRodClient($did,BaseReqPo $req){
        return $this->sendReqToClient($did,$req->toDataArray(),new HeatingRodDeviceLogic());
    }

    public function sendReqToClient($did,$data,BaseLogic $logic){
        if(empty($data)){
            return ResultHelper::error('不能发送空数据');
        }
        addLog('HeatingRodBase',serialize($data),'未加密数据','发送给设备');
        $ret = SunsunEncodeHelper::encode($did,$data,$logic);
        if($ret['status']){
            $result = $logic->getInfo(['did'=>$did]);
            if(ValidateHelper::legalArrayResult($result)){
                $tcp_client_id = $result['info']['tcp_client_id'];
                $ret = $this->sendToClient($did,$ret['info'],$tcp_client_id);
            }
        }
        return $ret;
    }

//    protected function sendReqToClient($did,BaseReqPo $req,BaseLogic $logic){
//        $msg = $req->toDataArray();
//        if(empty($msg)){
//            return ResultHelper::error('不能发送空数据');
//        }
//        addLog('HeatingRodBase',serialize($msg),'未加密数据','发送给设备');
//        $ret = SunsunEncodeHelper::encode($did,$msg,$logic);
//        if($ret['status']){
//            $result = $logic->getInfo(['did'=>$did]);
//            if(ValidateHelper::legalArrayResult($result)){
//                $tcp_client_id = $result['info']['tcp_client_id'];
//                $ret = $this->sendToClient($did,$ret['info'],$tcp_client_id);
//            }
//        }
//        return $ret;
//    }

    protected function sendToClient($did,$msg,$tcp_client_id=''){
        if(empty($msg)){
            return ResultHelper::error('不能发送空数据');
        }
        try{
            if(!empty($tcp_client_id)){
                Gateway::sendToClient($tcp_client_id,$msg);
            }else{
                Gateway::sendToUid($did,$msg);
            }

        }catch (Exception $ex){
            return ResultHelper::error('通讯异常，请重试'.$ex->getMessage());
        }
        return ResultHelper::success('发送成功');
    }
}