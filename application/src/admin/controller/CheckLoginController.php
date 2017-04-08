<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

namespace app\src\admin\controller;

use app\src\admin\helper\AdminSessionHelper;
use app\src\base\helper\ConfigHelper;
use app\src\base\helper\SessionHelper;
use app\src\user\action\LoginAction;
use app\src\user\facade\DefaultUserFacade;

class CheckLoginController extends BaseController{
	//初始化
	protected function _initialize(){
		parent::_initialize();
        $uid = AdminSessionHelper::isLogin();
        $auto_login_code = AdminSessionHelper::getAutoLoginCode();
        $error = L('ERR_SESSION_TIMEOUT');
		if ($uid > 0 && !empty($auto_login_code)) {

            $result = (new LoginAction())->autoLogin($uid,$auto_login_code,"",ConfigHelper::getValue("login_session_expire_time"));
		    if($result['status']){
		        if(!defined("UID")){
                    define('UID',$uid);
                }

                //须return
                return;
            }else{
                $error = $result['info'];
            }
		}

        $this->error($error,url('index/logout'),'',2);
	}
}
