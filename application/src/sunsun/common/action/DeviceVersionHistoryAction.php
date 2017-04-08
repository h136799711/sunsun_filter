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
 * 设备历史版本
 */
class DeviceVersionHistoryAction extends BaseAction
{
    public function history($did,$curPage=1,$size=30){

        $type = substr($did,0,3);
        $map  = ['device_type'=>$type];
        $result = (new DeviceVersionLogic())->getLatest($type);
        if(ValidateHelper::legalArrayResult($result)){
            $maxVersion = $result['info']['version'];
            $map['version'] = ['elt',$maxVersion];
        }
        $page = ['curpage'=>$curPage,'size'=>$size];
        $result = (new DeviceVersionLogic())->query($map,$page,'version desc');
        return $result;
    }
}