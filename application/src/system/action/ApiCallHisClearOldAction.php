<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-19
 * Time: 23:27
 */

namespace app\src\system\action;


use app\src\base\action\BaseAction;
use app\src\system\logic\ApiCallHisLogic;

class ApiCallHisClearOldAction extends BaseAction
{

    /**
     * 清除过期数据
     * @param $dataTimestamp
     * @return array
     */
    public function clearExpiredData($dataTimestamp){
        $result = (new ApiCallHisLogic())->delete(['call_time'=>['lt',$dataTimestamp]]);
        return $result;
    }
}