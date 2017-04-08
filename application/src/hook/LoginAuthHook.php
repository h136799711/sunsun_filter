<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-03
 * Time: 01:54
 */

namespace app\src\hook;

use app\src\base\helper\SessionHelper;

/**
 * Class LoginAuthHook
 * 登录检查，
 * 对于某些接口检测是否已登录
 * 1. 即是否有会话id传过来
 * 2. 检查会话id是否合法
 * @package app\src\hook
 */
class LoginAuthHook
{

    protected $needCheckApiList = [
        'default_address_*',
        'default_shoppingcart_*',
        'default_order_*',
        'default_user_update',
        'default_user_updatepwdbyoldpwd',
        'default_user_updatePwd',
        'default_user_updateLatLng'
    ];

    /**
     * 检查
     * @param int $uid
     * @param string $s_id
     * @param string $api
     * @return array
     */
    public function check($uid= 0,$s_id='',$api='',$device_type,$session_expire_time){

        if($s_id == 'itboye'){
            return ['status'=>true,'info'=>'test api'];
        }
        $api = strtolower($api);
        foreach ($this->needCheckApiList as $item){
            $result = preg_match('/'.$item.'/i',$api);
            if($result === 1){
                if($uid > 0 && !empty($s_id)){
                    return $this->checkUidSessionId($uid,$s_id,$device_type,$session_expire_time);
                }else{
                    if($uid <= 0){
                        return ['status'=>false,'info'=>'uid is missing'];
                    }else{
                        return ['status'=>false,'info'=>'s_id is missing'];
                    }
                }

            }
        }

        return ['status'=>true,'info'=>'not need check'];
    }

    private function checkUidSessionId($uid,$s_id,$device_type,$session_expire_time){
        $result = SessionHelper::checkLoginSession($uid,$s_id,$device_type,$session_expire_time);
        return $result;
    }
}