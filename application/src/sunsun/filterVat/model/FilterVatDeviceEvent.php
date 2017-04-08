<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 15:41
 */

namespace app\src\sunsun\filterVat\model;


use think\Model;

class FilterVatDeviceEvent extends Model
{
    protected $table = "sunsun_filter_vat_device_event";

    const PRO_STATUS_NOT_PROCESS = 0;
    const PRO_STATUS_PROCESSED = 1;
    const PRO_STATUS_FAILED = 2;
}