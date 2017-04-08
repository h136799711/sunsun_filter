<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-19
 * Time: 18:23
 */

namespace app\src\jobs;

use app\src\email\action\EmailSendAction;
use app\src\system\logic\ConfigLogic;
use think\Exception;
use think\queue\Job;

/**
 * Class EmailSendJob
 * 邮件发送
 * @package app\src\jobs
 */
class EmailJob
{
    private function sendToAdmin($content){
        $result = (new ConfigLogic())->getInfo(['name'=>'admin_email']);
        $admin_email = $result['info']['value'];
        if(!empty($admin_email)){

            $action = new EmailSendAction();
            $result = $action->send($admin_email,'邮件发送故障了',$content);
        }
    }

    public function fire(Job $job,$data){
        addLog("EmailSendJob/fire",'开始执行',$data,"[队列任务]");
        $attempts = $job->attempts() > 0 ? $job->attempts() : 1;
        if($attempts > 3){

            $content = serialize($data);
            $this->sendToAdmin($content);
//            $job->delete();

            return;
        }
        $interval = $attempts * 60;
        try{

            $to_email = $data['to_email'];
            $title = $data['title'];
            $content = $data['content'] ;
            $action = new EmailSendAction();
            $result = $action->send($to_email,$title,$content);

            if($result['status']){
                $job->delete();
            }else{
                addLog("EmailSendJob/fire",'发送错误',$result['info'],"[队列任务]");
            }

        }catch (Exception $exception){
            addLog("EmailSendJob/fire",'异常',$exception->getMessage(),"[队列任务]");
            $job->release($interval);
        }finally{
        }
    }
}