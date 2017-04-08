<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-17
 * Time: 10:53
 */

namespace app\mobile\api;


use app\pc\helper\PcApiHelper;
use app\src\user\enum\RoleEnum;

class MobUserApi
{

    /**
     * 用户登陆接口
     * @param $username
     * @param $password
     * @return array
     */
    public static function login($username, $password){
        $data = [
            'api_ver' => 104,
            'username' => $username,
            'password' => $password,
            'role' => RoleEnum::ROLE_Driver,
            'device_token' => $username,
            'device_type'  => 'pc',
            'country' => '+86'
        ];

        return PcApiHelper::callRemote('By_User_login', $data);
    }

    /**
     * 用户注册接口
     * @param $username
     * @param $password
     * @param $code
     * @return array
     */
    public static function register($username, $password, $code){
        $data = [
            'api_ver'  => 102,
            'country'  => '+86',
            'username' => $username,
            'password' => $password,
            'from'     => 0,  //来自内部应用登录
            'code'     => $code
        ];

        return PcApiHelper::callRemote('By_User_register', $data);
    }

    public static function updatePwdByOldPwd($uid, $password, $new_password){
        $data = [
            'uid' => $uid,
            'password' => $password,
            'new_password' => $new_password
        ];

        return PcApiHelper::callRemote('By_User_updatePwdByOldPwd', $data);
    }
}