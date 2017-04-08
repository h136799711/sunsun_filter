<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-11
 * Time: 14:54
 */

namespace app\index\controller;

use app\src\base\helper\ResultHelper;
use app\src\base\helper\ValidateHelper;
use app\src\extend\umeng\BoyePushApi;
use app\src\jobs\JobsHelper;
use app\src\sunsun\common\logic\UserDeviceLogic;
use app\src\sunsun\filterVat\logic\FilterVatDeviceEventLogic;
use app\src\sunsun\filterVat\model\FilterVatDeviceEvent;
use app\src\sunsun\heatingRod\logic\HeatingRodDeviceEventLogic;
use app\src\sunsun\heatingRod\logic\HeatingRodTempHisLogic;
use app\src\sunsun\heatingRod\model\HeatingRodDeviceEvent;
use app\src\system\logic\ConfigLogic;
use think\Controller;

/**
 * 所有设备事件任务处理
 * 设备事件处理任务
 * @package app\index\controller
 */
class DeviceEvent extends Controller
{
    private $now;
    public function process(){
        set_time_limit(0);
        $from = $this->request->get('from');
        $last_call_time = cache('last_call_time');
        $this->now = time();
        $interval = 24 * 60;
        //如果从crontab 或者 上次调用时间已经过去了24分钟
        if ($from == 'crontab' || $last_call_time + $interval < $this->now) {
            $this->sendNotifyOfFilterVat();

            $this->sendNotifyOfHeatingRod();
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
     * 发送通知 | 过滤桶
     */
    private function sendNotifyOfFilterVat()
    {
        //
        $page = ['curpage'=>1,'size'=>50];
        $userDeviceLogic = new UserDeviceLogic();
        $deviceEventLogic = new FilterVatDeviceEventLogic();
        $result = $deviceEventLogic->query(['pro_status'=>FilterVatDeviceEvent::PRO_STATUS_NOT_PROCESS],$page,"create_time asc");

        $list = $result['info']['list'];
        $now = time();
        $pushed = [];
        foreach ($list as $item){

            $id  = $item['id'];
            $did = $item['did'];
            $content = json_decode($item['event_info'],JSON_OBJECT_AS_ARRAY);
            if(array_key_exists("code_desc",$content)){
                $content = $content['code_desc'];
            }
            $entity = ['update_time'=>$now,'pro_status'=>FilterVatDeviceEvent::PRO_STATUS_PROCESSED];

            $time   = $item['create_time'];
            $gmdateTime   = gmdate('Y-m-d H:i:s\[\U\T\C\]',strtotime($time));
            $result = $userDeviceLogic->getInfo(['did'=>$did]);

            if(ValidateHelper::legalArrayResult($result)){
                $userDevice = $result['info'];
                $uid = $userDevice['uid'];

                $key = 'u'.$uid;
                if(!array_key_exists($key,$pushed)){
                    $pushed[$key] = [];
                }
                $flag = false;
                $pushedItems = $pushed[$key];
                foreach ($pushedItems as $event_type){
                    if($event_type == $item['event_type']){
                        $flag = true;
                        break;
                    }
                }

                //如果已经推送过同类型故障信息，则直接更新
                if($flag){
                    (new FilterVatDeviceEventLogic())->saveByID($id,$entity);
                    continue;
                }
                //保存已发送，防止频繁推送
                array_push($pushed[$key],$item['event_type']);

                $data = [
                    'to_uid'=>$uid,
                    'title'=>'Your equipment notification',
                    'content'=>"Occurrence time: $gmdateTime reason: $content"
                ];

                if($this->pushUMeng($data)){
                    (new FilterVatDeviceEventLogic())->saveByID($id,$entity);
                }else{
                    if($item['trys_cnt'] > 3){
                        $entity['pro_status'] = FilterVatDeviceEvent::PRO_STATUS_FAILED;
                        (new FilterVatDeviceEventLogic())->saveByID($id,$entity);
                    }else{
                        (new FilterVatDeviceEventLogic())->setInc(['id'=>$id],'trys_cnt',1);
                    }
                }
            }
        }

    }



    /**
     * 发送通知 | 加热棒
     */
    private function sendNotifyOfHeatingRod()
    {
        //
        $page = ['curpage'=>1,'size'=>50];
        $userDeviceLogic = new UserDeviceLogic();
        $deviceEventLogic = new HeatingRodDeviceEventLogic();
        $result = $deviceEventLogic->query(['pro_status'=>HeatingRodDeviceEvent::PRO_STATUS_NOT_PROCESS],$page,"create_time asc");

        $list = $result['info']['list'];
        $now = time();
        $pushed = [];
        foreach ($list as $item){
            $id  = $item['id'];
            $did = $item['did'];
            $entity = ['update_time'=>$now,'pro_status'=>HeatingRodDeviceEvent::PRO_STATUS_PROCESSED];
            $content = json_decode($item['event_info'],JSON_OBJECT_AS_ARRAY);
            if($item['event_type'] == 1){
                $ret = $this->addTempHis($did,$content,$item);
                if($ret){
                    (new HeatingRodDeviceEventLogic())->saveByID($id,$entity);
                }
                continue;
            }
            if($item['event_type'] == 0){
                //无操作 | 温度数据 过滤掉不推送
                continue;
            }
            if(array_key_exists("code_desc",$content)){
                $content = $content['code_desc'];
            }

            $time   = $item['create_time'];
            $gmdateTime   = gmdate('Y-m-d H:i:s\[\U\T\C\]',strtotime($time));
            $result = $userDeviceLogic->query(['did'=>$did],['curpage'=>1,'size'=>20]);

            if(ValidateHelper::legalArrayResult($result)){
                $list = $result['info']['list'];

                foreach ($list as $deviceItem){
                    $uid = $deviceItem['uid'];
                    //同一个用户同一台设备同一个故障类型
                    $key = 'u'.$uid.'d'.$did.'t'.$item['event_type'];
                    if(!array_key_exists($key,$pushed)){
                        $pushed[$key] = 1;
                    }else{
                        //如果已经推送过同类型故障信息，则直接更新
                        (new HeatingRodDeviceEventLogic())->saveByID($id,$entity);
                        continue;
                    }

                    //推送消息
                    $data = [
                        'to_uid'=>$uid,
                        'title'=>'Equipment notification',
                        'content'=>"Occurrence time: $gmdateTime reason: $content"
                    ];

                    if($this->pushUMeng($data)){
                        (new HeatingRodDeviceEventLogic())->saveByID($id,$entity);
                    }else{
                        if($item['trys_cnt'] > 3){
                            $entity['pro_status'] = HeatingRodDeviceEvent::PRO_STATUS_FAILED;
                            (new HeatingRodDeviceEventLogic())->saveByID($id,$entity);
                        }else{
                            (new HeatingRodDeviceEventLogic())->setInc(['id'=>$id],'trys_cnt',1);
                        }
                    }

                }




            }
        }

    }

    /**
     * 记录实时温度
     * @param $did
     * @param $content
     * @param $item
     * @return bool
     */
    private function addTempHis($did,$content,$item){
        addLog("device_event/sendNotifyOfHeatingRod",$content,$item,"实时温度记录",false);


        $tempHisEntity = [
            'did'=>$did,
            'temp'=>-1,
            'create_time'=>$item['create_time'],
            'ymd'=>date('Ymd',$item['create_time'])
        ];
        //实时温度数据
        if(array_key_exists("t",$content)){
            $tempHisEntity['temp'] = intval($content['t']);
        }
        if($tempHisEntity['temp'] <= 0){
            return false;
        }



        $action = new HeatingRodTempHisLogic();
        $result = $action->add($tempHisEntity);

        if($result['status']){
            $temp = $tempHisEntity['temp'];
            $this->checkTemp($did,$item['create_time'],$temp);
            return true;
        }else{
            addLog("device_event/sendNotifyOfHeatingRod",$result,$item,"[error]实时温度记录",false);
        }
    }

    /**
     * 检查温度 | 如果超过限制则报警
     * @param $did
     * @param $temp
     */
    public function checkTemp($did,$time,$temp){

        $result = (new UserDeviceLogic())->query(['did'=>$did],['curpage'=>1,'size'=>20]);
        if($result['status'] && count($result['info']['list']) > 0){
            $list = $result['info']['list'];
            foreach ($list as $vo){
                $uid = $vo['uid'];
                $temp_min = $vo['temp_min'];
                $temp_max = $vo['temp_max'];
                $temp_alert = $vo['temp_alert'];
                if(intval($temp_alert) == 1){

                    if($temp < $temp_min){
                        //温度低温报警
                        $data = [
                            'to_uid'=>$uid,
                            'title'=>lang('equipment_error_notification'),
                            'content'=>lang('equipment_temp_min',['time'=>toDatetime($time),'temp'=>toTemperate($temp),'temp_min'=>toTemperate($temp_min)]),
                        ];
                        $result = $this->pushUMeng($data);
                        addLog("device_event/checkTemp",$result,$data,"[error] 温度低温报警",true);

                    }elseif ($temp > $temp_max){
                        //温度高温报警
                        $data = [
                            'to_uid'=>$uid,
                            'title'=>lang('equipment_error_notification'),
                            'content'=>lang('equipment_temp_max',['time'=>$time,'temp'=>toTemperate($temp),'temp_max'=>toTemperate($temp_max)]),
                        ];

                        $result = $this->pushUMeng($data);
                        addLog("device_event/checkTemp",$result,$data,"[error] 温度高温报警",true);

                    }


                }

            }
        }
    }

    public function test_push(){
        $to_uid = $this->request->param('uid');
        $title = $this->request->param('title','test_push_umeng');
        $content = $this->request->param('content','content'.date("Y-m-d H:i:s",time()));
        var_dump($this->pushUMeng(['to_uid'=>$to_uid,'title'=>$title,'content'=>$content]));
    }

    public function pushUMeng($data){
        $to_uid = $data['to_uid'];
        $content = $data['content'];
        $title   =  $data['title'];
        $msg_type = "00Q002001";

        $pushApi = new BoyePushApi();
        $after_open  = [
            'type'  => 'go_activity',
            'param' => $msg_type,
            'extra' => [],//'uid'=>$to_uid]
        ];
        $body = [
            // 'alert'  =>$summary,
            'alert'  =>$content,
            'ticker' =>$title,
            'title'  =>$title,
            'text'   =>$content
        ];


        $result = (new ConfigLogic())->queryNoPaging(['group'=>11]);

        //TODO: 增加缓存
        if(ValidateHelper::legalArrayResult($result)){
            $list = $result['info'];
            foreach ($list  as $vo){
//                if(strpos($vo['name'],'umeng') !== 0){
//                    continue;
//                }
                $value = $vo['value'];
                $value = $this->_parse($vo['type'],$value);
                if(is_array($value)){
                    $pushApi->setConfig($value);
                }

                $result = $pushApi->send($to_uid,$body,$after_open);
                if(!$result['status']) {
                    addLog('push_task','config='.json_encode($value),$result['info'],'app_push推送');
                }

            }
        }

        return ResultHelper::success('success');
    }

    private function _parse($type, $value) {
        switch ($type) {
            case 3 :
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val,2);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }
}