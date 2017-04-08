<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-17
 * Time: 11:53
 */

namespace app\src\session\action;


use app\src\base\action\BaseAction;
use app\src\base\helper\ValidateHelper;
use app\src\session\logic\LoginSessionLogic;

class LoginSessionCheckAction extends BaseAction
{
    public function check($uid,$log_session_id,$device_type,$session_expire_time=1296000){
        $log_session_id = empty($log_session_id) ? "-1":$log_session_id;
        $logic  = new LoginSessionLogic();
        $map    = ['uid'=>$uid,'log_session_id'=>$log_session_id];
        $result = $logic->getInfo($map);
        $now  = time();
        if(ValidateHelper::legalArrayResult($result)){
            $info = $result['info'];
            $id = $info['id'];
            $expire_time = intval($info['expire_time']);

            if($now > $expire_time){
                $result = (new LoginSessionLogoutAction())->logout($uid,$log_session_id);
                return ['status'=>false,'info'=>lang("err_re_login")];
            }

            if($log_session_id != "-1" && $log_session_id != $info['log_session_id']){
                $result = (new LoginSessionLogoutAction())->logout($uid,$log_session_id);
                return ['status'=>false,'info'=>lang("err_login_".$info['login_device_type'],['time'=>date("Y-m-d H:i",$info['update_time'])])];
            }
            //至少5分钟
            if(empty($session_expire_time) || $session_expire_time <= 10){
                $session_expire_time = 300;
            }
            $session_expire_time = intval($session_expire_time);
            //检测成功,更新过期时间
            $result = $logic->saveByID($id,['expire_time'=>$now + $session_expire_time]);

            return $result;
        }

        $result = (new LoginSessionLogoutAction())->logout($uid,$log_session_id);
        return ['status'=>false,'info'=>lang("err_re_login")];
    }
}