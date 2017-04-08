<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-19
 * Time: 22:35
 */

namespace app\src\jobs;


use app\src\email\action\EmailSendAction;
use think\Queue;

class JobsHelper
{
    /**
     * 压入一个邮件发送任务
     * @param $data
     */
    public static function pushEmailJob($data){
        $queue = "itboye_email_task";
        Queue::push("app\\src\\jobs\\EmailJob", $data, $queue);
    }

    /**
     * 压入一个清空旧数据
     * @param $data
     */
    public static function pushClearDeviceEventJob(){
        $queue = "itboye_timing_task";
        $data  = [
        ];
        Queue::push("app\\src\\jobs\\ClearDeviceEventData", $data, $queue);
    }
}