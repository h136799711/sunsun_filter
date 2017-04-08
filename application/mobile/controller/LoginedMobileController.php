<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-29
 * Time: 11:44
 */

namespace app\mobile\controller;


use app\mobile\helper\MobileConfigHelper;
use app\mobile\helper\MobileSessionHelper;
use app\src\admin\controller\BaseController;
use app\src\admin\helper\AdminFunctionHelper;
use app\src\user\action\LoginAction;
use think\Request;

/**
 * mobile端基类控制器
 * Class MobileController
 * @package app\mobile\controller
 */
class LoginedMobileController  extends MobileController
{
    protected $uid;
    protected $userInfo;

    protected function _initialize()
    {
        parent::_initialize();
        $uid = MobileSessionHelper::isLogin();
        $auto_login_code = MobileSessionHelper::getAutoLoginCode();
        $error = "请先登录";

        $uid=1;
        $auto_login_code='1';

        if ($uid > 0 && !empty($auto_login_code)) {
            $username='13484379290';
            $password='123456';
            $country='+86';
            $device_token='device_id_um';
            $device_type='pc';
            $role='';
            $result = (new LoginAction())->Login($username,$password,$country,$device_token,$device_type,$role);

            if($result['status']){
                if(!defined("UID")){
                    define('UID',$uid);
                }
                $user = $result['info'];
                $currentUserAvatar = AdminFunctionHelper::getAvatarUrl($uid);
                $this->assign('cur_user_avatar',$currentUserAvatar);

                $this->uid      = $uid;
                $this->userInfo = $user;
                $this->assign('user',$user);
                //须return
                return;
            }else{
                $error = $result['info'];
            }
        }

        $this->redirect(url('index/logout'));
    }



}