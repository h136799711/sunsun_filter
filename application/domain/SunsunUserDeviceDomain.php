<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-14
 * Time: 15:30
 */

namespace app\domain;
use app\src\sunsun\common\action\UserDeviceAction;
use sunsun\filter_vat\dal\FilterVatDeviceDal;


/**
 * Class SunsunFilterVatDomain
 * 过滤桶
 * @package app\domain
 */
class SunsunUserDeviceDomain extends BaseDomain
{
    public function __construct($algInstance, $data)
    {
        parent::__construct($algInstance, $data);
    }



    public function query(){
        $uid = $this->_post('uid','');
        $result = (new UserDeviceAction())->query($uid);
        $this->exitWhenError($result,true);
    }

    /**
     * 101: 增加设备温度值上下限
     *
     */
    public function change(){
        $this->checkVersion([101],"增加设备温度值上下限");
        $id = $this->_post('id','');
        $device_nickname = $this->_post('device_nickname','');
        $nickname_a = $this->_post('nickname_a','');
        $nickname_b = $this->_post('nickname_b','');
        $temp_min = $this->_post('temp_min','');
        $temp_max = $this->_post('temp_max','');
        $temp_alert = $this->_post('temp_alert','');

        $entity = array(
            'update_time'=>time()
        );
        if(!empty($device_nickname)){
            $entity['device_nickname'] = $device_nickname;
        }
        if(!empty($nickname_a)){
            $entity['nickname_a'] = $nickname_a;
        }
        if(!empty($nickname_b)){
            $entity['nickname_b'] = $nickname_b;
        }
        if(!empty($temp_min)){
            $entity['temp_min'] = $temp_min;
        }
        if(!empty($temp_max)){
            $entity['temp_max'] = $temp_max;
        }
        if(!empty($temp_alert)){
            $entity['temp_alert'] = intval($temp_alert);
        }

        $result = (new UserDeviceAction())->UserDevicechange($id,$entity);
        $this->exitWhenError($result,true);
    }

    public function add(){
        $did = $this->_post('did','');
        $uid = $this->_post('uid','');
        $device_nickname=$this->_post('device_nickname','森森设备');
        $device_type=substr($did,0,3);
        $time = time();
        $entity = array(
            'did'=>$did,
            'device_nickname'=>$device_nickname,
            'uid'=>$uid,
            'device_type'=>$device_type,
            'create_time'=>$time,
            'update_time'=>$time
        );

        $result = (new UserDeviceAction())->add($entity);
        $this->exitWhenError($result,true);
    }

    public function del(){
        $id = $this->_post('id','');
        $result = (new UserDeviceAction())->UserDeviceDel($id);
        $this->exitWhenError($result,true);
    }

}