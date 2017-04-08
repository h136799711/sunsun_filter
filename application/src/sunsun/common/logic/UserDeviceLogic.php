<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:41
 */

namespace app\src\sunsun\common\logic;


use app\src\base\helper\ExceptionHelper;
use app\src\base\logic\BaseLogic;
use think\Db;
use think\exception\DbException;

class UserDeviceLogic extends BaseLogic
{
    private function getQuery(){
        return  Db::table("itboye_user_device")->alias("ud")
            ->field("ud.id as id, ud.uid, cb.nickname as user_nickname, ud.device_nickname, ud.update_time")
            ->join(["common_member"=>"cb"]," cb.uid = ud.uid ","LEFT");
    }

    public function getDevOwner($map = null, $order = false, $fields = false){


            try {
                $query = $this->getQuery();
                if (!empty($map)) $query = $query->where($map);
                if (false !== $order) $query = $query->order($order);
                $query = $query->field($fields);
                $list = $query->select();


                return $this->apiReturnSuc($list);
            } catch (DbException $ex) {
                return $this->apiReturnErr(ExceptionHelper::getErrorString($ex));
            }


    }
}