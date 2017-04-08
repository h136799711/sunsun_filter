<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-17
 * Time: 11:10
 */

namespace app\src\session\action;


use app\src\base\action\BaseAction;
use app\src\session\logic\LoginSessionLogic;
use app\src\user\logic\MemberConfigLogic;

class LoginSessionLoginAction extends BaseAction
{
    /**
     * @param $uid
     * @param $device_token
     * @param $device_type
     * @param $login_info
     * @param int $session_expire_time 默认15天
     * @return array
     */
    public function login($uid,$device_token,$device_type,$login_info,$session_expire_time=1296000){

        $logic  = new LoginSessionLogic();
        $memberCfgLogic  = new MemberConfigLogic();
        $map    = ['uid'=>$uid];
        $result = $memberCfgLogic->getInfo($map);
        if(!$result['status'] || empty($result['info']) || !is_array($result['info'])){
            return ['status'=>false,'info'=>'login session login error,uid invalid'];
        }
        $userInfo = $result['info'];
        $login_device_cnt = $userInfo['login_device_cnt'];

        $result = $logic->count($map);
        $cnt = $result['info'];
        if($cnt >= $login_device_cnt){
            //相等时，需要踢掉一个登录信息，踢掉最早的
            $result = $logic->getInfo(['uid'=>$uid],"expire_time asc");
            $info  = $result['info'];
            if(array_key_exists("log_session_id",$info)){
                $s_id  = $info['log_session_id'];
                (new LoginSessionLogoutAction())->logout($uid,$s_id);
            }
        }

        $now = time();
        $r = rand(100000,999999);
        $log_session_id = md5($device_token.$r.time()).get_36HEX($uid);
        //至少5分钟
        if(empty($session_expire_time) || $session_expire_time <= 10){
            $session_expire_time = 300;
        }
        $session_expire_time = intval($session_expire_time);
        $entity = [
            'log_session_id'=>$log_session_id,
            'uid'=>$uid,
            'update_time'=> $now,
            'login_info'=> json_encode($login_info),
            'create_time'=> $now,
            'expire_time'=> $now + $session_expire_time,
            'login_device_type'=>$device_type,
        ];
        $result = $logic->add($entity);
        if($result['status']){
            $result['info'] = $log_session_id;
        }
        return $result;
    }
}