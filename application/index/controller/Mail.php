<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-23
 * Time: 00:28
 */
namespace  app\index\controller;

use app\src\jobs\JobsHelper;
use app\src\sign\logic\UserSignLogic;
use app\src\user\action\LoginAction;
use app\src\user\action\RegisterAction;
use app\src\user\logic\MemberConfigLogic;
use app\src\wxapp\helper\LoginHelper;
use think\controller\Rest;
use think\Request;

class Mail extends Rest{

    /**
     * 小程序登录
     */
    public function send(){
        $title = $this->_param('title','');
        $content = $this->_param('content','');
        $toEmail = $this->_param('to_email','346551990@qq.com');

        JobsHelper::pushEmailJob(['title'=>$title,'content'=>$content,'to_email'=>$toEmail]);

        return "ok";
    }



    private function _param($key,$default='',$emptyErrMsg=''){
        $value = Request::instance()->param($key,$default);

        if($default == $value && !empty($emptyErrMsg)){
            $this->jsonReturnErr($emptyErrMsg);
        }
        return $value;
    }

    private function jsonReturnSuc($msg){
        return $this->jsonReturn(0,$msg);
    }

    private function jsonReturnErr($msg){
        return $this->jsonReturn(-1,$msg);
    }

    private function jsonReturn($code,$msg=''){
        $data = ['code'=>$code,'msg'=>$msg];
        $response = $this->response($data, "json",200);
        $response->header("X-Powered-By","WWW.ITBOYE.COM")->send();
        exit(0);
    }
}