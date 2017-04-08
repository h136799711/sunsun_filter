<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-04-08
 * Time: 10:27
 */

namespace app\src\base\model;


use app\src\base\helper\TimeHelper;
use think\Model;

class BaseModel extends Model
{

//    public function getCreateTimeAttr(){
//        if(is_null($this->getData($this->createTime))){
//            return 0;
//        }
//        return TimeHelper::toDatetime($this->getData($this->createTime));
//    }
//
//    public function getUpdateTimeAttr(){
//        if(is_null($this->getData($this->updateTime))){
//            return 0;
//        }
//        return TimeHelper::toDatetime($this->getData($this->updateTime));
//    }

}