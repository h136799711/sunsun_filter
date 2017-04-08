<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 16:49
 */

namespace app\admin\controller;
use app\src\sunsun\common\action\UserDeviceAction;
use app\src\sunsun\heatingRod\logic\HeatingRodDeviceLogic;
use app\src\sunsun\common\logic\DeviceVersionLogic;
/**
 * Class SunsunUserDevice
 * 用户设备
 * @package app\admin\controller
 */
class SunsunUserDevice extends Admin
{
    public function index(){

        $map = array();
        $params =false;
        $did   = $this->_param('did', 0);
        $ip    = $this->_param('ip', 0);
        $version   = $this->_param('version', 0);
        $startdatetime = $this->_param('startdatetime');
        $startdatetime = toUnixTimestamp($startdatetime);
        $enddatetime   = $this->_param('enddatetime');
        $enddatetime   = toUnixTimestamp($enddatetime);
        if(!empty($startdatetime) && !empty($enddatetime)){
            if($startdatetime === FALSE || $enddatetime === FALSE){
                $params = array('startdatetime' =>$startdatetime, 'enddatetime' =>$enddatetime);
                $map['last_login_time'] = array( array('EGT', $startdatetime), array('ELT', $enddatetime), 'and');
                $startdatetime = toDatetime( $startdatetime);
                $enddatetime   = toDatetime( $enddatetime);
            }else{
                $params = array('startdatetime' => $startdatetime, 'enddatetime' =>$enddatetime);
                $map['last_login_time'] = array( array('EGT', $startdatetime), array('ELT', $enddatetime), 'and');
                $startdatetime = toDatetime( $startdatetime);
                $enddatetime   = toDatetime( $enddatetime);
            }
        }
       $filterVer=(new DeviceVersionLogic())->queryNoPaging(['device_type'=>'S02']);
        $this -> assign('HeatingRod', $filterVer['info']);
        $this -> assign('version', $version);
        if (!empty($did)){
            $map['did'] = $did;
            $params['did'] = $did;

        }
        if (!empty($version)){
            $map['ver'] = $version;
            $params['ver'] = $version;
        }

        if (!empty($ip)){
            $map['last_login_ip'] = $ip;
            $params['last_login_ip'] = $ip;
        }
        $page = array('curpage' => $this->_param('p', 1), 'size' => config('LIST_ROWS'));
        $order = " last_login_time desc ";
        $result = (new HeatingRodDeviceLogic())->queryWithPagingHtml($map,$page,$order,$params);
        if($result['status']){
            $this -> assign('did', $did);
            $this -> assign('ip', $ip);
            $this -> assign('startdatetime', $startdatetime);
            $this -> assign('enddatetime', $enddatetime);
            $this->assign('show',$result['info']['show']);
            $this->assign('list',$result['info']['list']);
            return $this->boye_display();
        }else {
            $this -> error($result['info']);
        }
    }
    public function del(){

        $id   = $this->_param('id', 0);
        $result = (new UserDeviceAction())->userDeviceDel($id);
        if($result['status']){
            $this -> success('操作成功');
        }else{
            $this -> error($result['info']);
        }
    }
}