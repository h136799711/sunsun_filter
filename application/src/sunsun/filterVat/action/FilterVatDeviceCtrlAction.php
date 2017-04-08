<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\filterVat\action;
use app\src\base\helper\ResultHelper;
use app\src\sunsun\common\helper\SnHelper;
use sunsun\filter_vat\req\FilterVatCtrlDeviceReq;


/**
 * Class FilterVatDeviceInfoAction
 * 获取设备信息
 * @package app\src\sunsun\filterVat
 */
class FilterVatDeviceCtrlAction extends FilterVatBaseAction
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
        $req = new FilterVatCtrlDeviceReq();
        $req->setSn(SnHelper::getSn());
        $req->setData($data);
        $result = $this->sendReqToFilterVatClient($did,$req);
        return $result;
    }
}