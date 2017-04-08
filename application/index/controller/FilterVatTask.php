<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-11
 * Time: 14:54
 */

namespace app\index\controller;

use app\src\base\helper\ValidateHelper;
use app\src\jobs\JobsHelper;
use app\src\sunsun\common\logic\UserDeviceLogic;
use app\src\sunsun\filterVat\logic\FilterVatDeviceEventLogic;
use app\src\sunsun\filterVat\model\FilterVatDeviceEvent;
use app\src\user\logic\UcenterMemberLogic;
use think\Controller;

/**
 * Class FilterVatTask
 * 过滤桶定时任务
 * @package app\index\controller
 */
class FilterVatTask extends Controller
{
    private $now;
    public function index(){
        set_time_limit(0);
        $from = $this->request->get('from');
        $last_call_time = cache('last_call_time');
        $this->now = time();
        $interval = 24 * 60;
        //如果从crontab 或者 上次调用时间已经过去了24分钟
        if ($from == 'crontab' || $last_call_time + $interval < $this->now) {
            $this->sendNotify();
            cache('last_call_time',time());
        }

        $str  = '上一次调用时间: '.date("Y-m-d H:i:s",$last_call_time);
        $last_call_time = cache('last_call_time');
        $str .= '<br/>下一次调用时间:'.date("Y-m-d H:i:s",$last_call_time + $interval);
        echo $str;
    }


    /**
     * 清除数据库
     * 1天调用一次
     */
    public function clear_db(){
        set_time_limit(0);
        $from = $this->request->get('from');
        $last_call_time = cache('last_call_time');
        $this->now = time();
        $interval = 24 * 3600;
        //如果从crontab 或者 上次调用时间已经过去了24分钟
        if ($from == 'crontab' || $last_call_time + $interval < $this->now) {
            JobsHelper::pushClearDeviceEventJob();
            cache('last_call_time',time());
        }

        $str  = '上一次调用时间: '.date("Y-m-d H:i:s",$last_call_time);
        $last_call_time = cache('last_call_time');
        $str .= '<br/>下一次调用时间:'.date("Y-m-d H:i:s",$last_call_time + $interval);
        echo $str;
    }

    /**
     * 发送通知
     */
    private function sendNotify()
    {
        //
        $page = ['curpage'=>1,'size'=>30];
        $userDeviceLogic = new UserDeviceLogic();
        $ucenterMemberLogic = new UcenterMemberLogic();
        $deviceEventLogic = new FilterVatDeviceEventLogic();
        $result = $deviceEventLogic->query(['pro_status'=>FilterVatDeviceEvent::PRO_STATUS_NOT_PROCESS],$page,"create_time asc");

        $list = $result['info']['list'];
        $allEmail = [];
        $allEntity = [];
        $now = time();
        foreach ($list as $item){
            $id  = $item['id'];
            $did = $item['did'];
            $content = json_decode($item['event_info'],JSON_OBJECT_AS_ARRAY);
            if(array_key_exists("code_desc",$content)){
                $content = $content['code_desc'];
            }
            $entity = ['id'=>$id,'update_time'=>$now,'pro_status'=>1];
            array_push($allEntity,$entity);

            $time   = $item['create_time'];
            $gmdateTime   = gmdate('Y-m-d H:i:s\[\U\T\C\]',strtotime($time));
            $result = $userDeviceLogic->getInfo(['did'=>$did]);

            if(ValidateHelper::legalArrayResult($result)){
                $userDevice = $result['info'];
                $uid = $userDevice['uid'];
                $result = $ucenterMemberLogic->getInfo(['id'=>$uid]);
                $member = $result['info'];
                if(ValidateHelper::legalArrayResult($result)){
                    $email = $member['email'];
                    $data = [
                        'to_email'=>$email,
                        'title'=>'编号为'.$did.'的设备发生故障',
                        'content'=>"发生于世界标准时间: $gmdateTime 推断原因: $content"
                    ];

                    JobsHelper::pushEmailJob($data);
                }
            }
        }

        (new FilterVatDeviceEventLogic())->saveAll($allEntity);

    }

}