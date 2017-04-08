<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-28
 * Time: 17:09
 */

namespace app\src\sunsun\common\model;


use think\Model;

class DeviceVersion extends Model
{
    protected $table = "sunsun_device_version";
    protected $insert = ['create_time','update_time'];
    protected $update = ['update_time'];

    public function setCreateTimeAttr(){
        return time();
    }
    public function setUpdateTimeAttr(){
        return time();
    }
}