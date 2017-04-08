<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-17
 * Time: 11:07
 */

namespace app\domain;


use app\src\i18n\helper\LangHelper;
use app\src\session\action\LoginSessionLogoutAction;
use app\src\session\action\LoginSessionQueryAction;
use Detection\MobileDetect;

class LoginDeviceDomain extends BaseDomain
{
    /**
     *
     */
    public function query(){
        $uid = $this->_post('uid','',LangHelper::lackParameter("uid"));
        $result = (new LoginSessionQueryAction())->query($uid);
        $list = $result['info']['list'];
        if(is_array($list)){
            foreach ($list as &$item){
                $item['login_ip'] = "";
                $item['ua'] = "";
                $login_info = json_decode($item['login_info'],JSON_OBJECT_AS_ARRAY);
                if(array_key_exists("ua",$login_info)){
                    $item['login_ip'] = $login_info['login_ip'];
                }
                if(array_key_exists("ua",$login_info)){
                    $item['ua'] = $login_info['ua'];
                }
            }
        }
        $this->returnResult($result);
    }

    public function logout(){
        $uid = $this->_post('uid','',LangHelper::lackParameter("uid"));
        $s_id = $this->_post('s_id','',LangHelper::lackParameter("s_id"));
        $result =  (new LoginSessionLogoutAction())->logout($uid,$s_id);
        $this->returnResult($result);
    }
}