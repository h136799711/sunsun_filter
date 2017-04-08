<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/1/12
 * Time: 下午5:28
 */

namespace app\mobile\api;


use app\pc\helper\PcApiHelper;

class MobProductApi
{
    /**
     * 商品详情
     * @param $id
     * @return array
     */
    public static function detail($id){
        $data = [
            'api_ver'  => 102,
            'id' => $id
        ];

        return PcApiHelper::callRemote('By_Product_detail', $data);
    }

    /**
     * 商品搜索(分页)
     * @param $entity
     * @return array
     */
    public static function search($entity){
        $params = [
            'keyword',
            'cate_id',
            'prop_id',
            'page_size',
            'page_index',
            'order',
            'l_price',
            'r_price'
        ];
        $data = [
            'api_ver' => 101
        ];
        foreach ($params as $val){
            if(isset($entity[$val])){
                $data[$val] = $entity[$val];
            }
        }

        return PcApiHelper::callRemote('By_Product_search', $data);
    }

    /**
     * 商品搜索关键词(最多10条)
     * @param $keyword
     * @return array
     */
    public static function searchKeywords($keyword){
        $data = [
            'keyword' => $keyword
        ];

        return PcApiHelper::callRemote('By_Product_searchKeywords', $data);
    }
}