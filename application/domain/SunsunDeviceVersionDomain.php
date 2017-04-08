<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-14
 * Time: 15:30
 */

namespace app\domain;
use app\src\sunsun\common\action\DeviceVersionHistoryAction;


/**
 * Class SunsunFilterVatDomain
 * 设备版本接口
 * @package app\domain
 */
class SunsunDeviceVersionDomain extends BaseDomain
{
    public function __construct($algInstance, $data)
    {
        parent::__construct($algInstance, $data);
    }

    /**
     *
     */
    public function query(){
        $did = $this->_post('did','');
        $size = $this->_post('size',30);
        $curPage = $this->_post('cur_page',1);
        $result = (new DeviceVersionHistoryAction())->history($did,$curPage,$size);
        $this->exitWhenError($result,true);
    }
}