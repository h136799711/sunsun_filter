<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-11
 * Time: 14:16
 */

namespace app\src\category\action;


use app\src\base\action\BaseAction;
use app\src\category\logic\CategorySkuLogic;

class CategorySkuDelRelationAction extends BaseAction
{

    public function delRelation($sku_id)
    {
        $map = array('sku_id'=>$sku_id);
        $result = (new CategorySkuLogic())->delete($map);
        return $this->result($result);
    }
}