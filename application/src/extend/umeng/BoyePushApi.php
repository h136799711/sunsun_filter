<?php
/**
 * 虎头奔 友盟推送 广播&别名单播
 * User  : hebidu
 * Editor: rainbow
 * Date  : 2016-12-30 14:31:21
 */

namespace app\src\extend\umeng;

use app\src\user\logic\MemberLogic;

require_once('UmengPushApi.php');

class BoyePushApi {

    protected $config = [
        'alias_type'=>'sunsun_xiaoli',
        'device_type'=>'ios',
        'appkey'=>'',
        'secret'=>'',
        'production_mode'=>true
    ];

    public function setConfig($config){
        if(array_key_exists("alias_type",$config)){
            $this->config['alias_type'] = $config['alias_type'];
        }
        if(array_key_exists("device_type",$config)){
            $this->config['device_type'] = $config['device_type'];
        }
        if(array_key_exists("appkey",$config)){
            $this->config['appkey'] = $config['appkey'];
        }
        if(array_key_exists("secret",$config)){
            $this->config['secret'] = $config['secret'];
        }
        if(array_key_exists("production_mode",$config)){
            $this->config['production_mode'] = $config['production_mode'];
        }
    }

    public function getAliasType(){
        return $this->config['alias_type'];
    }
    public function getCfgDeviceType(){
        return $this->config['device_type'];
    }
    public function getAppkey(){
        return $this->config['appkey'];
    }
    public function getSecret(){
        return $this->config['secret'];
    }
    public function getProductionMode(){
        return $this->config['production_mode'];
    }

    /**
     * 别名 - 单播(1)/多播(50-)/文件博(50+)
     * @param $uid   string/int uids(逗号分隔<=50)
     * @param array $param client     客户端[worker,driver,其他任意字符]
     * @param array $after_open
     * @return array
     * @internal param string $client
     */
    public function send($uid='',$param,$after_open=['type'=>'go_app','param'=>'','extra'=>'']){

        //检查 uid
        $file = false;

        $uid_arr = array_unique(explode(',', $uid));
        $size = count($uid_arr);
        if($size > 50) $file = true;
        $uid_arr = implode("\n", $uid_arr);//一定要 "\n"

        if($file){
            //获取file_id
            $r = new \UmengPushApi($this->getAppkey(),$this->getSecret());
            $file_id = $r->getFileId($uid_arr);
        }
        //检查消息主题
        $r = $this->checkMsgBody($param);
        if(!$r['status']) return $r;
        if(strtolower($this->getCfgDeviceType()) == 'android'){
            $entity = [
                'alias_type'      => $this->getAliasType(),
                'ticker'          => $param['ticker'],
                'title'           => $param['title'],
                'text'            => $param['text'],
                'after_open'      => $after_open['type'],
                'production_mode' => $this->getProductionMode(),
            ];
        }else {
            $entity = [
                'alias_type'      =>$this->getAliasType(),
                'alert' => $param['text'],
                'badge' => 0, //角标
                'sound' => 'default',
                'production_mode' =>$this->getProductionMode(),
            ];
        }
        if($file) $entity['file_id'] = $file_id;
        else $entity['alias'] = $uid;


        $pusher  = new \UmengPushApi($this->getAppkey(),$this->getSecret());
        if($this->getCfgDeviceType() == 'ios') {
            if(!empty($after_open['extra'])) {
                if (isset($after_open['extra']['sound'])) {
                    //自定义sound
                    $entity['sound'] = $after_open['extra']['sound'].'.caf';
                }
            }

            $entity['payload']['aps'] = ['alert'=> $param['text']];
            $entity['payload']['extra'] = $after_open['extra'];

            var_dump($entity);
            $result = $pusher->sendIOSCustomizedcast($entity);
        }else {
            if(!empty($after_open['extra'])) {
                if (isset($after_open['extra']['sound'])) {
                    //自定义sound
                    $entity['sound'] = $after_open['extra']['sound'];
                }
            }
            //自定义打开指定页面
            if($after_open['type'] == 'go_url'){
                $entity['url'] = $after_open['param'];
            }elseif($after_open['type'] == 'go_activity'){
                $entity['activity'] = $after_open['param'];
            }
            $result = $pusher->sendAndroidCustomizedcast($entity);
        }

        return $result;
    }

    /**
     * 广播
     * @param $param array
     */
    public function sendAll($param,$after_open=['type'=>'go_app','param'=>'','extra'=>'','sound'=>''],$client=""){

        $worker = true;
        $driver = true;
        if($client == 'worker') $driver = false;
        if($client == 'driver') $worker = false;

        // 检查消息主题
        $r = $this->checkMsgBody($param);
        if(!$r['status']) return $r;

        //发送安卓设备广播消息
        if($worker) $Android_w = new \UmengPushApi($this->and_worker_appkey,$this->and_worker_secret);
        if($driver) $Android_d = new \UmengPushApi($this->and_driver_appkey,$this->and_driver_secret);

        $entity_w = [
            'ticker'          =>$param['ticker'],
            'title'           =>$param['title'],
            'text'            =>$param['text'],
            'after_open'      =>$after_open['type'],
            'production_mode' =>$this->and_worker_mode,
        ];

        $entity_d = $entity_w;$entity_d['production_mode'] = $this->and_driver_mode;
        //自定义打开指定页面
        if($after_open['type'] == 'go_url'){
            $entity_w['url'] = $after_open['param'];
            $entity_d['url'] = $after_open['param'];
        }
        if($after_open['type'] == 'go_activity'){
            $entity_w['activity'] = $after_open['param'];
            $entity_d['activity'] = $after_open['param'];
        }
        if(!empty($after_open['extra'])){
            //Android自定义sound
            if(isset($after_open['extra']['sound'])){
                $entity_w['sound'] = $after_open['sound'];
                $entity_d['sound'] = $after_open['sound'];
            }
            $entity_w['payload']['extra']['after_open'] = $after_open['extra'];
            $entity_d['payload']['extra']['after_open'] = $after_open['extra'];
        }
        if($worker) $result_a_w = $Android_w->sendAndroidBroadcast($entity_w);
        if($driver) $result_a_d = $Android_d->sendAndroidBroadcast($entity_d);

        //发送IOS设备广播消息
        if($worker) $IOS_w = new \UmengPushApi($this->ios_worker_appkey,$this->ios_worker_secret);
        if($driver) $IOS_d = new \UmengPushApi($this->ios_driver_appkey,$this->ios_driver_secret);

        $entity_w = [
            'alert'           =>$param['alert'],
            'badge'           =>0,
            'sound'           =>'default',
            'production_mode' =>$this->ios_worker_mode,
        ];
        $entity_d = $entity_w;$entity_d['production_mode'] = $this->ios_driver_mode;

        //自定义打开指定页面
        if($after_open['type'] == 'go_activity'){
            $entity_w['payload']['after_open'] = $after_open['param'];
            $entity_d['payload']['after_open'] = $after_open['param'];
        }
        if(!empty($after_open['extra'])){
            if(isset($after_open['extra']['sound'])){
                //自定义sound
                $entity_w['sound'] = $after_open['extra']['sound'].'.caf';
                $entity_d['sound'] = $after_open['extra']['sound'].'.caf';
                // unset($after_open['extra']['sound']);
            }
            $entity_w['payload']['extra']['after_open'] = $after_open['extra'];
            $entity_d['payload']['extra']['after_open'] = $after_open['extra'];
        }
        if($worker) $result_i_w = $IOS_w->sendIOSBroadcast($entity_w);
        if($driver) $result_i_d = $IOS_d->sendIOSBroadcast($entity_d);

        if(
            ($worker && (!$result_a_w['status'] || !$result_i_w['status'])) ||
            ($driver && (!$result_a_d['status'] || !$result_i_d['status']))
        ){
            $err = '';
            if($worker && !$result_a_w['status']) $err .= 'and_worker:'.$result_a_w['info'].';';
            if($worker && !$result_i_w['status']) $err .= 'ios_worker:'.$result_i_w['info'].';';
            if($driver && !$result_a_d['status']) $err .= 'and_driver:'.$result_a_d['info'].';';
            if($driver && !$result_i_d['status']) $err .= 'ios_dirver:'.$result_i_d['info'].';';
            return returnErr($err);
        }else{
            return returnSuc(L('success'));
        }

    }

    /**
     * 检查消息体
     * @Author
     * @DateTime 2016-12-30T14:08:36+0800
     * @return   [type]                   [description]
     */
    private function checkMsgBody(array $param){
        if(empty($param)){
            return returnErr(Llack('param'));
        }
        if(empty($param['ticker'])){
            return returnErr(Llack("param['ticker']"));
        }
        if(empty($param['title'])){
            return returnErr(Llack("param['title']"));
        }
        if(empty($param['text'])){
            return returnErr(Llack("param['text']"));
        }
        if(empty($param['alert'])){
            return returnErr(Llack("param['alert']"));
        }
        return returnSuc(L('success'));
    }
}