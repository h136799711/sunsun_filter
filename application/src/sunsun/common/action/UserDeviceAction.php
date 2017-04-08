<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:37
 */

namespace app\src\sunsun\common\action;
use app\src\base\action\BaseAction;
use app\src\base\helper\ValidateHelper;
use app\src\sunsun\common\logic\UserDeviceLogic;



/**
 * Class FilterVatDeviceInfoAction
 * tcp 客户端通用操作
 * @package app\src\sunsun\filterVat
 */
class UserDeviceAction extends BaseAction
{
    public function query($uid){

        $result = (new UserDeviceLogic())->queryNoPaging(['uid'=>$uid]);

        return $result;
    }
    public function deviceUserInfo($did){

        $result = (new UserDeviceLogic())->queryNoPaging(['did'=>$did]);

        return $result;
    }
    public function userDeviceChange($id,$entity){

        $result = (new UserDeviceLogic())->saveByID($id,$entity);

        return $result;
    }


    public function add($entity){
        $did = $entity['did'];
        $logic = new UserDeviceLogic();
        $result = $logic->getInfo(['did'=>$did]);
        //存在则更新
        if(ValidateHelper::legalArrayResult($result)){
            unset($entity['did']);
            return $logic->save(['did'=>$did],$entity);
        }

        $result = $logic->add($entity);
        return $result;
    }

    public function userDeviceDel($id){
        $map=array('id'=>$id);
        $result = (new UserDeviceLogic())->delete($map);

        return $result;
    }
}