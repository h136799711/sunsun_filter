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


use app\mobile\api\MobBannersApi;
use app\mobile\api\MobUserApi;
use app\mobile\helper\MobileSessionHelper;
use app\pc\helper\PcApiHelper;
use app\src\securitycode\enum\CodeTypeEnum;
use app\src\securitycode\model\SecurityCode;
use app\src\user\action\LoginAction;
use app\src\user\action\UserLogoutAction;
use app\src\user\enum\RoleEnum;

class Index  extends MobileController
{


    public function index(){
        $this->assignNav('首页');
        $this->assignTitle('首页');
        $result = (new MobBannersApi())->query();
        $banners = [];
        foreach ($result['info'] as $vo){
            $href = empty($vo['url']) ? "#" : $vo['url'];
            array_push($banners,[
                'href'=>$href,
                'img'=>getImgUrl($vo['img'])
            ]);
//            array_push($banners, getImgUrl($vo['img']) );
        }

        $this->assign("banners",json_encode($banners));
        return $this->fetch();
    }

    public function logout(){
        MobileSessionHelper::logout();
        $uid = MobileSessionHelper::getUserId();
        $auto_login_code = MobileSessionHelper::getAutoLoginCode();

        (new UserLogoutAction())->logout($uid,$auto_login_code);

        //会话
        MobileSessionHelper::logout();

        $this -> redirect(url('index/login'));
    }

    /**
     * 登录
     * @return mixed
     */
    public function login(){
        if(IS_GET){
            $this->assignTitle('登陆');
            return $this->fetch();
        }else{
            $username = $this->_param('phone','');
            $password = $this->_param('password','');

            if(empty($username)){
                $this->error('手机号不能为空');
            }
            if(empty($password)){
                $this->error('密码不能为空','');
            }

            $result = (new LoginAction())->login($username, $password,"+86",MobileSessionHelper::getSessionId(),"mobile_web",RoleEnum::ROLE_Driver);

            if($result['status']){
                MobileSessionHelper::setLoginUserInfo($result['info']);
                $this->success('登录成功',url('index/index'));
            }else{
                $this->error('账号或密码错误','');
            }

        }
        return "";
    }

    /**
     * 注册
     * @return mixed
     */
    public function register()
    {
        $this->assignTitle('注册');
        if(IS_POST){
            $username = $this->_param('mobile','');
            $password = $this->_param('psw','');
            $code = $this->_param('code','');

            if(empty($username)){
                $this->error('手机号不能为空');
            }
            if(empty($password)){
                $this->error('密码不能为空');
            }
            if(empty($code)){
                $this->error('验证码不能为空');
            }

            $result = MobUserApi::register($username, $password, $code);

            if($result['status']){

                if($result['status']){
                    $this->success('注册成功',url('index/logout'));
                }else{
                    $this->error('未知错误','');
                }
            }else{
                $this->error($result['info'],'');
            }
        }
        return $this->fetch();
    }

    /**
     * 发送注册验证码
     */
    public function send_reg_sms(){
        if(IS_POST){
            $mobile = $this->_param('mobile','');
            if(empty($mobile)) $this->error('错误的手机号码','');

            $data = [
                'api_ver' => 101,
                'mobile' => $mobile,
                'country'   => '+86',
                'code_type' => SecurityCode::TYPE_FOR_REGISTER,
                'send_type' => CodeTypeEnum::Sms
            ];

            $result = PcApiHelper::callRemote('By_SecurityCode_send',$data);

            if($result['status']){
                $this->success('验证码已发送~'.$result['info'],'');
            }else{
                $this->error($result['info'],'');
            }
        }
    }



}