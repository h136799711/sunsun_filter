<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-19
 * Time: 15:37
 */

namespace app\src\jobs;


use app\src\email\action\EmailSendAction;
use app\src\sunsun\common\action\HeatingRodTempHisAction;
use app\src\sunsun\common\action\TcpLogAction;
use app\src\sunsun\filterVat\action\FilterVatDeviceEventAction;
use app\src\sunsun\filterVat\action\FilterVatTcpLogAction;
use app\src\sunsun\heatingRod\action\HeatingRodDeviceEventAction;
use app\src\sunsun\heatingRod\action\HeatingRodTcpLogAction;
use app\src\system\action\ApiCallHisClearOldAction;
use app\src\system\logic\ConfigLogic;
use think\Exception;
use think\queue\Job;

/**
 * Class ClearDeviceEventData
 * 1天执行一次
 * @package app\src\jobs
 */
class ClearDeviceEventData
{
    private function sendToAdmin($content){
        $result = (new ConfigLogic())->getInfo(['name'=>'admin_email']);
        $admin_email = $result['info']['value'];
        if(!empty($admin_email)){

            $action = new EmailSendAction();
            $result = $action->send($admin_email,'清理数据库日志故障了',$content);
        }
    }
    /**
     * 清理 设备事件表
     * @param Job $job
     * @param $data
     */
    public function fire(Job $job,$data){
        addLog("ClearDeviceEventData/fire",'开始执行',$data,"[队列任务]");
        $attempts = $job->attempts() > 0 ? $job->attempts() : 1;
        if($attempts > 3){
            $content = serialize($data);
            $this->sendToAdmin($content);
            $job->delete();
            return;
        }
        $interval = $attempts * 60;
        $now = time();
        try{

            //1. 设备事件日志30天内保留
            $dataTimestamp = $now  - 30*24*3600;
            $result = (new FilterVatDeviceEventAction())->clearExpiredData($dataTimestamp);
            addLog("ClearDeviceEventData/fire",'清理过滤桶设备日志',json_encode($result),"[队列任务]");
            //1. 设备事件日志30天内保留
            $dataTimestamp = $now  - 30*24*3600;
            $result = (new HeatingRodDeviceEventAction())->clearExpiredData($dataTimestamp);
            addLog("ClearDeviceEventData/fire",'清理加热棒设备日志',json_encode($result),"[队列任务]");
            //1. tcp接口日志7天内保留
            $dataTimestamp = $now - 7*24*3600;
            $result = (new FilterVatTcpLogAction())->clearExpiredData($dataTimestamp);
            addLog("ClearDeviceEventData/fire",'清理过滤桶TCP通信日志',json_encode($result),"[队列任务]");
            //2. tcp接口日志7天内保留
            $dataTimestamp = $now - 7*24*3600;
            $result = (new HeatingRodTcpLogAction())->clearExpiredData($dataTimestamp);
            addLog("ClearDeviceEventData/fire",'清理加热棒TCP通信日志',json_encode($result),"[队列任务]");
            //3. tcp接口日志7天内保留
//            $dataTimestamp = $now - 24*3600;
//            $result = (new TcpLogAction())->clearExpiredData($dataTimestamp);

            //1. 接口日志7天内保留
            $dataTimestamp = $now - 3*24*3600;
            $result = (new ApiCallHisClearOldAction())->clearExpiredData($dataTimestamp);
            addLog("ClearDeviceEventData/fire",'清理api_call历史数据',json_encode($result),"[队列任务]");


            // 历史温度数据
            $dataTimestamp = $now - 30*24*3600;
            $result = (new HeatingRodTempHisAction())->clearExpiredData($dataTimestamp);
            addLog("ClearDeviceEventData/fire",'清理加热棒温度历史数据',json_encode($result),"[队列任务]");

            $job->delete();

        }catch (Exception $exception){
            addLog("ClearDeviceEventData/fire",'异常',$exception->getMessage(),"[队列任务]");
            $job->release($interval);
        }finally{

        }
    }
}