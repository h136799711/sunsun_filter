<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-22
 * Time: 11:50
 */

namespace app\mobile\controller;


use app\src\bjpay\po\BjLoginReqPo;
use app\src\bjpay\request\BjRequest;

class BjPay extends MobileController
{
    public function test(){
//        if(IS_GET){
//            return $this->fetch();
//        }
        $user_id = $this->_param('user_id','','user_id缺失');
        $user_pwd = $this->_param('user_pwd','','user_pwd缺失');

        $loginReq = new BjLoginReqPo();

        $loginReq->setUserId($user_id);
        $loginReq->setUserPwd($user_pwd);

        $result = BjRequest::login($loginReq);

        var_dump($result);
        return $this->fetch();
    }
}