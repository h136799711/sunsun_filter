<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:41
 */

namespace app\src\sunsun\heatingRod\model;


use think\Model;

class HeatingRodDeviceEvent extends Model
{
    protected $table = "sunsun_heating_rod_device_event";

    const PRO_STATUS_NOT_PROCESS = 0;
    const PRO_STATUS_PROCESSED = 1;
    const PRO_STATUS_FAILED = 2;
}