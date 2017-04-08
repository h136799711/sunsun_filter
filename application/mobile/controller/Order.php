<?php
/**
 * Copyright (c) 2017.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-18
 * Time: 17:30
 */

namespace app\mobile\controller;


use app\mobile\api\MobOrderApi;
use app\pc\helper\PcFunctionHelper;

class Order extends LoginedMobileController
{
    public function index()
    {
        $this->assignTitle('我的订单');

        $status = $this->_param('status',0);
        $p = $this->_param('p',1);
        $size = $this->_param('page_size', 15);

        /**
         *  0: 全部订单 1: 待付款 2:待发货 3: 待收货 7: 待评价
         */
        if(!in_array($status,[0,1,2,3,7])) $status = 0;

        //订单查询
        $result = MobOrderApi::queryV2(UID, $status, $p, $size);

        if(!$result['status']) $this->error($result['info']);

        $list = [];
        foreach ($result['info']['list'] as $val){
            $tmp = [
                'id' => $val['id'],
                'order_code' => $val['order_code'],
                'note' => $val['note'],
                'price' => intval($val['price']) / 100,
                'query_status' => $val['query_status'],
                'items_count' => count($val['items']),
                'items' => [],
            ];
            foreach ($val['items'] as $item){
                $tmp['items'][] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'img' => PcFunctionHelper::getImgUrl($item['img'],120),
                    'price' => intval($item['price']) / 100,
                    'count' => $item['count']
                ];
            }
            $list[] = $tmp;
        }

        if(IS_POST){
            $this->success($list,'');

        }else{
            $this->assign('status',$status);
            $this->assign('list', $list);

            return $this->fetch();
        }

    }

    //订单详情
    public function detail(){
        $this->assignTitle('订单详情');
        $order_code = $this->_param('order_code','','订单号错误');

        $result = MobOrderApi::detail(UID, $order_code);

        if(!$result['status']) $this->error($result['info']);

        $info = $result['info'];
        $info['price'] = intval($info['price']) / 100;
        $info['goods_amount'] = intval($info['goods_amount']) / 100;
        foreach ($info['items'] as &$val){
            $val['img'] = PcFunctionHelper::getImgUrl($val['img'], 120);
            $val['price'] = intval($val['price']) / 100;
            $val['ori_price'] = intval($val['ori_price']) / 100;
        }
        $this->assign('detail', $info);
        return $this->fetch();
    }

    //重新支付
    public function repay(){
        $order_code = $this->_param('order_code');
        $result = MobOrderApi::detail(UID, $order_code);

        if(!$result['status']) $this->error($result['info']);

        if((bool)$result['info']['pay_status']) $this->error('订单已支付');

        $result = MobOrderApi::repay(UID, $order_code);
        if(!$result['status']) $this->error($result['info']);

        $info = $result['info'];

        //跳转微信支付
        $this->redirect(config('site_url').'/wxpay/jump2pay?pay_code='.$info['pay_code']);
    }

    //确认收货
    public function receive_goods(){
        $order_code = $this->_param('order_code');
        $result = MobOrderApi::receiveGoods(UID, $order_code);
        if($result['status']){
            $this->success('操作成功');
        }else{
            $this->error($result['info'], '');
        }
    }
}