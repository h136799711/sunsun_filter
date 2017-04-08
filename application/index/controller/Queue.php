<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-10
 * Time: 17:21
 */
namespace  app\index\controller;

use app\src\email\action\EmailSendAction;
use app\src\jobs\Hello;
use app\src\jobs\JobsHelper;
use think\controller\Rest;

class  Queue extends Rest {
    public function test(){
        $content = '<html></html><p>中文yingwen English</p>';
        $data = [
            'to_email'=>'136799711@qq.com',
            'title'=>'123test_queue_email_task',
            'content'=>$content
        ];JobsHelper::pushEmailJob($data);
        echo "push";
    }
}