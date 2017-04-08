<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 14:45
 */

namespace app\mobile\helper;


use app\src\base\helper\SessionHelper;
use app\src\base\utils\DataSignUtils;

class MobileSessionHelper extends SessionHelper
{


    public static function getValue($key){
        return session($key);
    }

    public static function hasValue($key){
        return session('?'.$key);
    }
    public static function setValue($key,$value){
        return session($key,$value);
    }

    /**
     * 获取当前语言
     */
    public static function getCurrentLang(){
        $lang = self::getValue("current_lang");
        if(empty($lang)){
            self::setValue("current_lang","zh-cn");
        }

        return self::getValue("current_lang");
    }

    /**
     * 设置当前语言
     * @param $lang
     * @return mixed
     */
    public static function setCurrentLang($lang){
        return self::setValue("current_lang",$lang);
    }

    /**
     * 获取当前店铺ID
     */
    public static function getCurrentStoreId(){
        return self::getValue("current_store_id");
    }

    /**
     * 设置当前店铺id
     * @param $id
     * @return mixed
     */
    public static function setCurrentStoreId($id){
        return self::setValue("current_store_id",$id);
    }





    public static function getSessionId(){
        return  session_id();
    }

    /**
     * 后台用户注销
     */
    public static function logout(){
        session(null);
        session("[destroy]");
    }

    /**
     * 获取登录后的用户会话code
     * @return int
     */
    public static function getAutoLoginCode(){
        $user = self::getUserInfo();
        if($user === false){
            return "";
        }
        return $user['auto_login_code'];
    }


    /**
     * 获取用户id
     * @return int 0 | 大于0
     */
    public static function getUserId(){
        $user = self::getUserInfo();
        if($user === false){
            return 0;
        }

        return $user['id'];
    }

    /**
     * 判断当前是否已经登录
     * 0: 未登录 大于0: 已登录
     * @return int 0 | 大于0
     */
    public static function isLogin(){
        $user = self::getUserInfo();
        if($user === false){
            return 0;
        }

        return $user['id'];
    }

    /**
     * 从session中获取登录用户的信息
     * @return bool|mixed
     */
    public static function getUserInfo(){
        $user = session(MobileSessionKeys::Mobile_USER);
        if (empty($user)) {
            return false;
        } else {
            return session(MobileSessionKeys::Mobile_USER_SIGN) == DataSignUtils::sign($user) ? $user : false;
        }
    }

    /**
     * mobile模块登录的用户信息存入session
     * @param $userinfo
     */
    public static function setLoginUserInfo($userinfo){
        session(MobileSessionKeys::Mobile_USER,$userinfo);
        session(MobileSessionKeys::Mobile_USER_SIGN,DataSignUtils::sign($userinfo));
    }



}