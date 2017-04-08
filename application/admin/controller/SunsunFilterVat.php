<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 16:49
 */

namespace app\admin\controller;
use app\src\sunsun\common\action\UserDeviceAction;
use app\src\sunsun\filterVat\logic\FilterVatDeviceLogic;
use app\src\sunsun\common\logic\DeviceVersionLogic;
use app\src\sunsun\common\logic\UserDeviceLogic;
/**
 * Class SunsunFilterVat
 * 过滤桶设备
 * @package app\admin\controller
 */
class SunsunFilterVat extends Admin
{
    public function index(){


        $map = array();
        $params =false;
        $did   = $this->_param('did', 0);
        $ip    = $this->_param('ip', 0);
        $version   = $this->_param('version', "");
        $startdatetime = $this->_param('startdatetime','');
        $enddatetime   = $this->_param('enddatetime','');
        if(!empty($startdatetime) && !empty($enddatetime)){
            $startdatetime = toUnixTimestamp($startdatetime);
            $enddatetime   = toUnixTimestamp($enddatetime);

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
       $filterVer=(new DeviceVersionLogic())->getVer(['device_type'=>'S01']);
        $this -> assign('filterVer', $filterVer['info']);
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
        $result = (new FilterVatDeviceLogic())->queryWithPagingHtml($map,$page,$order,$params);
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
    public function detail(){

        $did   = $this->_param('did', 0);

        $result = (new FilterVatDeviceLogic())->getInfo(array("did"=>$did));
        $owner = (new UserDeviceLogic())->getDevOwner(array("did"=>$did));
        if($result['status']) {
            $devinfo = $result['info'];

            $week = ['w1' => '周一', 'w2' => '周二', 'w4' => '周三', 'w8' => '周四', 'w16' => '周五', 'w32' => '周六', 'w64' => '周日'];
            $key = 'w' . $devinfo['cl_week'];

            if (array_key_exists($key, $week)) {
                $devinfo['cl_week'] = $week[$key];
            }
            if ($devinfo['cl_dur']) {
            $devinfo['cl_percent'] = 100 * $devinfo['cl_sche'] / $devinfo['cl_dur'];
           }else{
                $devinfo['cl_percent'] = 0;
            }
            $this->assign('devinfo',$devinfo);
            $this->assign('owner',$owner['info']);
            return $this->boye_display();
        }else {
            $this -> error($result['info']);
        }
    }

    public function del(){


    }
}