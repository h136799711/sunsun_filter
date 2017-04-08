<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-29
 * Time: 11:44
 */

namespace app\mobile\controller;
use app\mobile\api\MobAddressApi;

use app\src\repair\logic\RepairLogicV2;

class User extends LoginedMobileController
{

    protected function _initialize()
    {
        parent::_initialize();
        $this->assignNav('我的');
    }

    public function index()
    {

        $this->assignTitle('个人中心');
        return $this->fetch();
    }

    public function address()
    {
        $this->assignTitle('我的收货地址');
        $result = MobAddressApi::query(UID);
        if($result['status']){
            $this->assign('address',$result['info']);
        }else{
            $this->error($result['info']);
        }
        return $this->fetch();
    }

    /**
     * 收货地址添加
     */
    public function address_add()
    {

        $this->assignTitle('添加地址');
        if(IS_POST){
            $entity = [
                'country' => '中国',
                'country_id' => 1,
                'provinceid' => $this->_param('provinceid'),
                'cityid' => $this->_param('cityid',0),
                'areaid' => $this->_param('areaid',0),
                'detailinfo' => $this->_param('detailinfo'),
                'contactname' => $this->_param('contactname'),
                'mobile' => $this->_param('mobile'),
                'postal_code' => $this->_param('postal_code','0000'),
                'province' => $this->_param('province'),
                'city' => $this->_param('city',' '),
                'area' => $this->_param('area',' '),
                'default' => $this->_param('default')
            ];
            $result = MobAddressApi::add(UID, $entity);
            if($result['status']){
                $this->success('添加成功',url('user/address'));
            }else{
                $this->error($result['info'],'');
            }

        }else{
            return $this->fetch();
        }

    }

    /**
     * 收货地址更新
     */
    public function address_edit()
    {

        $this->assignTitle('修改地址');
        if(IS_POST){
            $id = $this->_param('id');
            $entity = [
                'country' => '中国',
                'country_id' => 1,
                'provinceid' => $this->_param('provinceid'),
                'cityid' => $this->_param('cityid',0),
                'areaid' => $this->_param('areaid',0),
                'detailinfo' => $this->_param('detailinfo'),
                'contactname' => $this->_param('contactname'),
                'mobile' => $this->_param('mobile'),
                'postal_code' => $this->_param('postal_code','0000'),
                'province' => $this->_param('province'),
                'city' => $this->_param('city',' '),
                'area' => $this->_param('area',' '),
                'default' => $this->_param('default')
            ];
            $result = MobAddressApi::update(UID, $id, $entity);

            if($result['status']){
                $this->success('修改成功',url('user/address'));
            }else{
                $this->error($result['info'],'');
            }
        }else{
            $id = $this->_param('id');
            $result  = MobAddressApi::query(UID);
            if($result['status']){
                $list = $result['info'];
                $address = [];
                foreach ($list as $val){
                    if($val['id'] == $id){
                        $address = $val;
                        break;
                    }
                }
                if(empty($address)){
                    $this->error('错误的收货地址');
                }
                $this->assign('address', $address);
            }else{
                $this->error($result['info']);
            }
            return $this->fetch();
        }

    }

    /**
     * 收货地址删除
     */
    public function address_delete(){
        if(IS_POST){
            $id = $this->_param('id');
            $result = MobAddressApi::delete(UID, $id);
            if($result['status']){
                $this->success('删除成功',url('user/address'));
            }else{
                $this->error($result['info'],'');
            }
        }
    }

    public function report_busy()
    {

        return $this->fetch();
    }

    public function reporting()
    {

        return $this->fetch();
    }


    public function map(){
        $this->assignTitle('选择地点');
               return $this->fetch();


    }

}