<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-14
 * Time: 11:22
 */

namespace app\src\material\logic;


use app\src\base\logic\BaseLogic;
use app\src\material\model\Material;

class MaterialLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Material());
    }
}