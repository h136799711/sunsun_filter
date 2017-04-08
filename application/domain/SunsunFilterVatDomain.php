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
use app\src\sunsun\filterVat\action\FilterVatClientAction;
use app\src\sunsun\filterVat\action\FilterVatDeviceEventAction;
use app\src\sunsun\filterVat\action\FilterVatDeviceInfoAction;
use app\src\sunsun\filterVat\logic\FilterVatDeviceLogic;
use app\src\sunsun\filterVat\action\FilterVatDeviceCtrlAction;

/**
 * Class SunsunFilterVatDomain
 * 过滤桶
 * @package app\domain
 */
class SunsunFilterVatDomain extends BaseDomain
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

        $result = (new DeviceGetVersionAction())->version($did,$version);

        if(!ValidateHelper::legalArrayResult($result)){
            $this->apiReturnErr('不支持该设备类型');
        }
        $url = $result['info']['url'];
        $len = $result['info']['bytes'];

        $result = (new FilterVatClientAction())->update($did,$url,$len);
        $this->returnResult($result);
    }

    public function sendMessage(){
        $did      = $this->_post('did','');
        $message = $this->_post('message','');
        $result   = (new FilterVatClientAction())->sendMessage($did,json_decode($message,JSON_OBJECT_AS_ARRAY));
        $this->exitWhenError($result,true);
    }

    public function userDeviceInfo(){
        $did = $this->_post('did','');
        $uid = $this->_post('uid','');

        $result = (new FilterVatDeviceInfoAction())->deviceInfo($did,$uid);

        $this->exitWhenError($result,true);
    }

    public function deviceInfo(){
        $did = $this->_post('did','');

        $result = (new FilterVatDeviceInfoAction())->deviceInfo($did,0);

        $this->exitWhenError($result,true);
    }

    public function clientCount(){
        $data = [
            'all_client_cnt'=>(new FilterVatClientAction())->allClientCount()
        ];
        $result = ['status'=>true,'info'=>$data];
        $this->exitWhenError($result,true);
    }

    public function sessionInfo(){
        $did = $this->_post('did','');
        $data =  (new FilterVatClientAction())->getSession($did);
        $result = ['status'=>true,'info'=>$data];
        $this->exitWhenError($result,true);
    }

    public function devicesCtrl(){

        $this->checkVersion(["101"],'参数改下划线模式');
        $debug = $this->_post('debug',0);

        $did = $this->_post('did','');
        $clEn = $this->_post('cl_en','');
        $clWeek = $this->_post('cl_week','');
        $clTm = $this->_post('cl_tm','');
        $clDur = $this->_post('cl_dur','');
        $clState = $this->_post('cl_state','');
        $clCfg = $this->_post('cl_cfg','');
        $uvOn = $this->_post('uv_on','');
        $uvOff = $this->_post('uv_off','');
        $uvWH = $this->_post('uv_wh','');
        $uvCfg = $this->_post('uv_cfg','');
        $outStateA = $this->_post('out_state_a','');
        $outStateB = $this->_post('out_state_b','');
        $devLock = $this->_post('dev_lock','');
        $entity=array();

        if(empty($did)){
            $this->apiReturnErr('did参数缺失');
        }

        if(strlen($clEn) > 0){
            $entity['clEn'] = intval($clEn);
        }
        if(strlen($clWeek) > 0){
            $entity['clWeek'] = intval($clWeek);
        }
        if(strlen($clDur) > 0){
            $entity['clDur'] = intval($clDur);
        }
        if(strlen($clState) > 0){
            $entity['clState'] = intval($clState);
        }
        if(strlen($clCfg) > 0){
            $entity['clCfg'] = intval($clCfg);
        }
        if(strlen($uvWH) > 0){
            $entity['uvWH'] = intval($uvWH);
        }
        if(strlen($uvCfg) > 0){
            $entity['uvCfg'] = intval($uvCfg);
        }
        if(strlen($outStateA) > 0){
            $entity['outStateA'] = intval($outStateA);
        }
        if(strlen($outStateB) > 0){
            $entity['outStateB'] = intval($outStateB);
        }
        if(strlen($devLock) > 0){
            $entity['devLock'] = intval($devLock);
        }

        if(!empty($clTm) && strlen($clTm) >= 4){
            $entity['clTm'] = substr($clTm,0,4);
        }
        if(!empty($uvOn) && strlen($uvOn) >= 4){
            $entity['uvOn'] = substr($uvOn,0,4);
        }
        if(!empty($uvOff) && strlen($uvOff) >= 4){
            $entity['uvOff'] = substr($uvOff,0,4);
        }

        if(count($entity) == 0){
            $this->apiReturnSuc('操作成功');
        }

        $result  = (new FilterVatDeviceCtrlAction())->ctrl($did,$entity);
        if($result['status']) {
            //正式测试会注释该方法
            if($debug){
                $this->toDbEntity($did,$entity);
            }
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr($result['info']);
        }
    }

    private function toDbEntity($did,$data){
        $dbEntity = [];
        array_key_exists("clEn",$data) && $dbEntity['cl_en'] = $data['clEn'];
        array_key_exists("clWeek",$data)  && $dbEntity['cl_week'] = $data['clWeek'];
        array_key_exists("clTm",$data)  && $dbEntity['cl_tm'] = $data['clTm'];
        array_key_exists("clDur",$data)  && $dbEntity['cl_dur'] = $data['clDur'];
        array_key_exists("clState",$data)  && $dbEntity['cl_state'] = $data['clState'];
        array_key_exists("clCfg",$data)  && $dbEntity['cl_cfg'] = $data['clCfg'];
        array_key_exists("uvOn",$data)  && $dbEntity['uv_on'] = $data['uvOn'];
        array_key_exists("uvOff",$data)  && $dbEntity['uv_off'] = $data['uvOff'];
        array_key_exists("uvWH",$data)  && $dbEntity['uv_wh'] = $data['uvWH'];
        array_key_exists("uvCfg",$data)  && $dbEntity['uv_cfg'] = $data['uvCfg'];
        array_key_exists("outStateA",$data)  && $dbEntity['out_state_a'] = $data['outStateA'];
        array_key_exists("outStateB",$data)  && $dbEntity['out_state_b'] = $data['outStateB'];
        array_key_exists("devLock",$data)  && $dbEntity['dev_lock'] = $data['devLock'];
//        dump($did);
        (new FilterVatDeviceLogic())->save(['did'=>$did],$dbEntity);
        return $dbEntity;
    }

    public function deviceEvent(){
        $did = $this->_post('did','');
        $code = $this->_post('code','');

        $action = new FilterVatDeviceEventAction();
        $result = $action->add($did,$code);

        $this->returnResult($result);
    }

}