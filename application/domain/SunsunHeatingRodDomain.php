<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-14
 * Time: 15:30
 */

namespace app\domain;
use app\src\base\helper\ValidateHelper;
use app\src\sunsun\common\action\DeviceGetVersionAction;
use app\src\sunsun\common\logic\DeviceVersionLogic;
use app\src\sunsun\heatingRod\action\HeatingRodClientAction;
use app\src\sunsun\heatingRod\action\HeatingRodDeviceCtrlAction;
use app\src\sunsun\heatingRod\action\HeatingRodDeviceEventAction;
use app\src\sunsun\heatingRod\action\HeatingRodDeviceInfoAction;
use app\src\sunsun\heatingRod\logic\HeatingRodDeviceLogic;

/**
 * Class SunsunHeatingRodDomain
 * 加热棒
 * @package app\domain
 */
class SunsunHeatingRodDomain extends BaseDomain
{
    public function __construct($algInstance, $data)
    {
        parent::__construct($algInstance, $data);
    }

    public function queryLatest(){
        $did = $this->_post('did','');
        $type = substr($did,0,3);
        $result = (new DeviceVersionLogic())->getLatest($type);
        if(!ValidateHelper::legalArrayResult($result)){
            $this->apiReturnErr('不支持该设备类型');
        }
        $this->returnResult($result);
    }

    public function updateFirmware(){
        $did = $this->_post('did','');
        $version = $this->_post('version','');

        $result = (new  DeviceGetVersionAction())->version($did,$version);

        if(!ValidateHelper::legalArrayResult($result)){
            $this->apiReturnErr('找不到该设备信息');
        }
        $url = $result['info']['url'];
        $len = $result['info']['bytes'];

        $result = (new HeatingRodClientAction())->update($did,$url,$len);
        $this->returnResult($result);
    }

    public function sendMessage(){
        $did      = $this->_post('did','');
        $message = $this->_post('message','');

        $result   = (new HeatingRodClientAction())->sendMessage($did,json_decode($message,JSON_OBJECT_AS_ARRAY));
        $this->returnResult($result);
    }

    public function userDeviceInfo(){
        $did = $this->_post('did','');
        $uid = $this->_post('uid','');

        $result = (new HeatingRodDeviceInfoAction())->deviceInfo($did,$uid);

        $this->exitWhenError($result,true);
    }

    public function deviceInfo(){
        $did = $this->_post('did','');
        $result = (new HeatingRodDeviceInfoAction())->deviceInfo($did,0);
        $this->exitWhenError($result,true);
    }

    public function clientCount(){
        $data = [
            'all_client_cnt'=>(new HeatingRodClientAction())->allClientCount()
        ];
        $result = ['status'=>true,'info'=>$data];
        $this->exitWhenError($result,true);
    }

    public function sessionInfo(){
        $did = $this->_post('did','');
        $data =  (new HeatingRodClientAction())->getSession($did);
        $result = ['status'=>true,'info'=>$data];
        $this->exitWhenError($result,true);
    }

    public function devicesCtrl(){

        $this->checkVersion(["100"],'参数改下划线模式');

        $debug = $this->_post('debug',0);
        $did = $this->_post('did','');
        $tSet = $this->_post('t_set','');
        $tCyc = $this->_post('t_cyc','');
        $cfg = $this->_post('cfg','');
        $devLock = $this->_post('dev_lock','');
        $entity=array();

        if(empty($did)){
            $this->apiReturnErr('did参数缺失');
        }

        if(strlen($tSet) > 0){
            $entity['tSet'] = intval($tSet);
        }
        if(strlen($tCyc) > 0){
            $entity['tCyc'] = intval($tCyc);
        }
        if(strlen($cfg) > 0){
            $entity['cfg'] = intval($cfg);
        }
        if(strlen($devLock) > 0){
            $entity['devLock'] = intval($devLock);
        }

        if(count($entity) == 0){
            $this->apiReturnSuc('操作成功');
        }

        $result  = (new HeatingRodDeviceCtrlAction())->ctrl($did,$entity);
        if($result['status']) {
            //TODO: 正式测试会注释该方法
            if($debug) {
                $this->toDbEntity($did, $entity);
            }
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr($result['info']);
        }
    }

    private function toDbEntity($did,$data){
        $dbEntity = [];
        array_key_exists("tSet",$data)     && $dbEntity['t_set'] = $data['tSet'];
        array_key_exists("tCyc",$data)     && $dbEntity['t_cyc'] = $data['tCyc'];
        array_key_exists("cfg",$data)      && $dbEntity['cfg'] = $data['cfg'];
        array_key_exists("devLock",$data)  && $dbEntity['dev_lock'] = $data['devLock'];
        (new HeatingRodDeviceLogic())->save(['did'=>$did],$dbEntity);
        return $dbEntity;
    }

    public function deviceEvent(){
        $did = $this->_post('did','');
        $code = $this->_post('code','');

        $action = new  HeatingRodDeviceEventAction();
        $result = $action->add($did,$code);

        $this->returnResult($result);
    }

}