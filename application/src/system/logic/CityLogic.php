<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-16
 * Time: 16:48
 */

namespace app\src\system\logic;


use app\src\base\logic\BaseLogic;
use app\src\system\model\City;

class CityLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new City());
    }
}