<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-11
 * Time: 14:18
 */

namespace app\src\sku\action;


use app\src\base\action\BaseAction;
use app\src\base\helper\ValidateHelper;
use app\src\category\logic\CategorySkuLogic;
use app\src\goods\logic\SkuvalueLogic;
use app\src\sku\logic\SkuLogic;

class SkuDeleteAction extends BaseAction
{
    public function delete($sku_id){
        $map = ['sku_id'=>$sku_id];
        $result = (new SkuvalueLogic())->queryNoPaging($map);
        if(ValidateHelper::legalArrayResult($result)){
            return $this->error('存在规格值，请先删除规格值！');
        }

        $result = (new CategorySkuLogic())->queryWithCount($map);
        if(intval($result['info']) > 0){
           return  $this->error("有类目引用，请删除类目引用关系！");
        }
        $result = (new SkuLogic())->delete(['id'=>$sku_id]);
        return $this->result($result);
    }
}