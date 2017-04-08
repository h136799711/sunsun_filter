<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-06
 * Time: 10:26
 */

namespace app\src\banners\action;


use app\src\banners\logic\BannersLogic;
use app\src\base\action\BaseAction;

/**
 * Class BannersQueryAction
 * banner查询操作类
 * @package app\src\banners\action
 */
class BannersQueryAction extends BaseAction
{

    /**
     * banner查询操作
     * @param $position integer 图片位置
     * @author hebidu <email:346551990@qq.com>
     * @return array
     */
    public function query($position){
        $map = ['position'=>$position];
        $order ="sort asc";
        $field ='url,url_type,notes,img,title';

        $result = (new BannersLogic())->queryNoPaging($map,$order,$field);
        return $this->result($result);
    }

}