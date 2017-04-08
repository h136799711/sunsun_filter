<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-20
 * Time: 17:33
 */

namespace app\src\sunsun\common\helper;


use app\src\base\helper\ResultHelper;
use app\src\base\helper\ValidateHelper;
use app\src\base\logic\BaseLogic;
use sunsun\decoder\SunsunTDS;

class SunsunEncodeHelper
{
    public static function encode($did,$data,BaseLogic $logic){
        $result = $logic->getInfo(['did'=>$did]);
        if(ValidateHelper::legalArrayResult($result)){
            $pwd = $result['info']['pwd'];
            $tcpClientId = $result['info']['tcp_client_id'];
            if(empty($tcpClientId)){
                //
                //return ResultHelper::error('设备已离线,请重试');
            }
        }else{
            return ResultHelper::error('设备did不存在');
        }

        if(empty($pwd)){
           return ResultHelper::error('设备pwd参数缺少');
        }

        return ResultHelper::success(SunsunTDS::encode($data,$pwd));
    }
}