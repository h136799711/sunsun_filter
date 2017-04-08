<?php

/**
 * Created by PhpStorm.
 * User: Zhoujinda
 * Date: 2017/2/6
 * Time: 10:34
 */

namespace app\mobile\api;

use app\pc\helper\PcApiHelper;
use app\src\shoppingCart\logic\ShoppingCartLogic;

class MobSpCartApi {

    /**
     * 购物车添加商品
     * @param $uid
     * @param $id
     * @param $sku_pkid
     * @param $count
     */
    public static function add($uid, $id, $sku_pkid, $count){

        $data = [
            'uid' => $uid,
            'id' => $id,
            'sku_pkid' => $sku_pkid,
            'count' => $count
        ];

        return PcApiHelper::callRemote('By_ShoppingCart_add', $data);
    }

    /**
     * 购物车删除商品
     * @param $uid
     * @param $id
     */
    public static function delete($uid, $id){

        $data = [
            'uid' => $uid,
            'id' => $id
        ];

        return PcApiHelper::callRemote('By_ShoppingCart_delete', $data);
    }

    /**
     * 购物车商品修改
     * @param $uid
     * @param $id
     * @param $count
     */
    public static function edit($uid, $id, $count){

        $logic = new ShoppingCartLogic;
        $result = $logic->save(['id'=>$id,'uid'=>$uid],['count'=>$count]);

        return $result;
    }

    /**
     * 购物车查询
     * @param $uid
     */
    public static function query($uid){

        $data = [
            'api_ver' => 102,
            'uid' => $uid
        ];

        return PcApiHelper::callRemote('By_ShoppingCart_query', $data);
    }
}