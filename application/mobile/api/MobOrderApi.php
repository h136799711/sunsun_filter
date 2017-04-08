<?php
/**
 * Created by PhpStorm.
 * User: Zhoujinda
 * Date: 2017/1/9
 * Time: 10:37
 */

namespace app\mobile\api;


use app\pc\helper\PcApiHelper;
use app\src\base\helper\PageHelper;
use app\src\base\helper\ValidateHelper;
use app\src\order\action\OrderQueryAction;
use app\src\order\helper\OrdersBusinessStatusHelper;
use app\src\order\model\Orders;

class MobOrderApi {

    /**
     * 订单分页查询接口
     * @param $uid
     * @param $keyword
     * @param int $page_index
     * @param int $page_size
     * @return array
     */
    public static function query($uid, $query_status = 0, $page_index = 1, $page_size = 15, $keyword = ''){
        $data = [
            'api_ver' => 101,
            'uid' => $uid,
            'query_status' => $query_status,
            'keyword' => $keyword,
            'page_index' => $page_index,
            'page_size' => $page_size
        ];

        return PcApiHelper::callRemote('By_Order_query', $data);
    }

    /**
     * 订单分页查询接口V2
     * @param $uid
     * @param $keyword
     * @param int $page_index
     * @param int $page_size
     * @return array
     */
    public static function queryV2($uid, $query_status = 0, $page_index = 1, $page_size = 15, $keyword = ''){
        //对查询状态进行分析
        //[0=>全部,1=>待付款,2=>待发货,3=>待收货,4=>已收货,5=>退款/售后,6=>待评价,7=>已完成]

        $status = OrdersBusinessStatusHelper::analysisQueryStatus($query_status);

        $orders = new Orders();
        $orders->setOrderStatus($status['order_status']);
        $orders->setPayStatus($status['pay_status']);
        $orders->setUid($uid);

        $result = (new OrderQueryAction())->query($keyword,$orders,
            new PageHelper(['page_index' => $page_index, 'page_size' => $page_size]));

        if(ValidateHelper::legalArrayResult($result)){
            foreach ($result['info']['list'] as &$item){
                $item['query_status'] = OrdersBusinessStatusHelper::convertQueryStatus($item);
            }
        }
        return $result;
    }

    /**
     * 订单重新支付
     * @param $uid
     * @param $order_code
     * @param string $s_id
     * @return array
     */
    public static function repay($uid, $order_code, $s_id = 'itboye'){
        $data = [
            'api_ver' => 101,
            'uid' => $uid,
            's_id' => $s_id,
            'order_code' => $order_code
        ];

        return PcApiHelper::callRemote('By_Order_repay', $data);
    }

    /**
     * 订单创建
     * @param $uid
     * @param $ids
     * @param $address_id
     * @param int $del
     * @param $note
     * @param $s_id
     * @return array
     */
    public static function create($uid, $ids, $address_id, $del = 0, $note, $s_id = 'itboye'){
        $data = [
            'api_ver' => 101,
            'uid' => $uid,
            's_id' => $s_id,
            'ids' => $ids,  //购物车id
            'address_id' => $address_id,
            'del' => $del,
            'note' => $note
        ];

        return PcApiHelper::callRemote('By_Order_create', $data);
    }

    /**
     * 订单立即购买
     * @param $uid
     * @param $sku_pkid
     * @param $count
     * @param $address_id
     * @param string $s_id
     * @return array
     */
    public static function createNow($uid, $sku_pkid, $count, $address_id, $s_id = 'itboye'){
        $data = [
            'api_ver' => 101,
            'uid' => $uid,
            's_id' => $s_id,
            'ids' => $sku_pkid,
            'count' => $count,
            'address_id' => $address_id,
            'note' => $note
        ];

        return PcApiHelper::callRemote('By_Order_createNow', $data);
    }

    /**
     * 订单取消
     * @param $uid
     * @param $order_code
     * @param string $s_id
     * @return array
     */
    public static function cancel($uid, $order_code, $s_id = 'itboye'){
        $data = [
            'api_ver' => 101,
            'uid' => $uid,
            's_id' => $s_id,
            'order_code' => $order_code
        ];

        return PcApiHelper::callRemote('By_Order_cancel', $data);
    }

    /**
     * 订单详情
     * @param $uid
     * @param $order_code
     * @param string $s_id
     * @return array
     */
    public static function detail($uid, $order_code, $s_id = 'itboye'){
        $data = [
            'api_ver' => 101,
            'uid' => $uid,
            's_id' => $s_id,
            'order_code' => $order_code
        ];

        return PcApiHelper::callRemote('By_Order_detail', $data);
    }

    /**
     * 订单确认收货
     * @param $uid
     * @param $order_code
     * @param string $s_id
     * @return array
     */
    public static function receiveGoods($uid, $order_code, $s_id = 'itboye'){
        $data = [
            'api_ver' => 101,
            'uid' => $uid,
            's_id' => $s_id,
            'order_code' => $order_code
        ];

        return PcApiHelper::callRemote('By_Order_receiveGoods', $data);
    }
}