<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\common\action;
use app\src\base\action\BaseAction;
use app\src\base\helper\ValidateHelper;
use app\src\sunsun\common\logic\DeviceVersionLogic;
use app\src\sunsun\common\logic\UserDeviceLogic;



/**
 * Class DeviceGetVersionAction
 * 设备版本信息获取
 */
class DeviceGetVersionAction extends BaseAction
{
    /**
     *
     * @param $did
     * @param string $version 为空或不传的时候返回最新版本
     * @return array
     */
    public function version($did,$version=''){

        $type = substr($did,0,3);
        $version = strtolower($version);
        if(empty($version)){
            $result = (new DeviceVersionLogic())->getLatest($type);
        }else{
            $result = (new DeviceVersionLogic())->getInfo(['device_type'=>$type,'version'=>$version]);
        }
        return $result;
    }
}