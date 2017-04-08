<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/2/3
 * Time: 下午2:02
 */

namespace app\mobile\api;


use app\pc\helper\PcApiHelper;

class MobShopApi
{
    /**
     * 子类目查询接口
     * @param $cate_id
     * @return array
     */
    public static function querySubCategory($cate_id){
        $data = [
            'api_ver' => 101,
            'cate_id' => 1
        ];

        return PcApiHelper::callRemote('By_Category_querySubCategory', $data);
    }
}