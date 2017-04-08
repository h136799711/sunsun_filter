<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-20
 * Time: 09:21
 */

namespace app\callback\controller;


use think\controller\Rest;

class QcloudSms extends Rest
{
    public function notify(){

        addLog("qcloud_sms_notify",$_GET,$_POST,'短信通知',true,"",file_get_contents("php://input"));

        $this->response(['result'=>0,'errmsg'=>0],"json");
    }

    public function reply(){
        echo "reply";
    }
}