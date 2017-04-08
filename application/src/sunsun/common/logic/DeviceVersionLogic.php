<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-28
 * Time: 17:10
 */

namespace app\src\sunsun\common\logic;


use app\src\base\logic\BaseLogic;
use think\Db;



class DeviceVersionLogic extends BaseLogic
{
    public function getLatest($type)
    {
        $map = [
            'device_type' => strtoupper($type),
            'is_latest' => 1
        ];
        $order = "update_time desc";
        $result = $this->getInfo($map, $order);
        return $result;
    }

    private function getQuery()
    {
        return Db::table("sunsun_device_version");

    }

    public function getVer($map = null, $order = false)
    {
        try {
            $query = $this->getQuery();
            $query = $query->distinct(true);
            if (!empty($map)) $query = $query->where($map);
            if (false !== $order) $query = $query->order($order);
            $query = $query->field("version");
            $list = $query->select();


            return $this->apiReturnSuc($list);
        } catch (DbException $ex) {
            return $this->apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }
}