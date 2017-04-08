<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-29
 * Time: 11:44
 */

namespace app\mobile\controller;


use app\mobile\api\MobAddressApi;
use app\mobile\api\MobOrderApi;
use app\mobile\api\MobProductApi;
use app\mobile\api\MobShopApi;
use app\mobile\api\MobSpCartApi;
use app\pc\helper\PcFunctionHelper;
use app\src\shoppingCart\action\ShoppingCartQueryAction;

class Shop extends LoginedMobileController
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->assignNav('商城');
    }

    public function index()
    {

        $this->redirect(url('shop/product_kind'));
        return $this->fetch();
    }

    //购物车
    public function cart()
    {

        $this->assignTitle('购物车');
        $result = MobSpCartApi::query(UID);

        if(!$result['status']) $this->error($result['info']);

        foreach ($result['info'] as &$val){
            $val['price'] = intval($val['price']) / 100;
            $val['ori_price'] = intval($val['ori_price']) / 100;
            $val['icon_url'] = PcFunctionHelper::getImgUrl($val['icon_url'], 120);
        }

        $this->assign('spcart', $result['info']);

        return $this->fetch();
    }
    
    //购物车删除
    public function cart_delete()
    {
        if(IS_POST){

            $id = $this->_param('id');
            $result = MobSpCartApi::delete(UID, $id);
            if($result['status']){
                $this->success('删除成功');
            }else{
                $this->error($result['info']);
            }
        }
    }
    
    //购物车修改数量
    public function cart_edit()
    {
        if(IS_POST){
            $id = $this->_param('id');
            $count = $this->_param('count');
            $result = MobSpCartApi::edit(UID, $id, $count);
            if($result['status']){
                $this->success('操作成功','');
            }else{
                $this->error($result['info']);
            }
        }
    }

    public function join_cart()
    {

        return $this->fetch();
    }

    /**
     * 添加到购物车
     */
    public function cart_add(){
        if(IS_POST){
            $pid = $this->_param('pid','','商品错误');
            $sku_pkid = $this->_param('sku_pkid','','规格错误');
            $count = intval($this->_param('count'));
            if($count <=0) $this->error('数量必须大于0');

            $result = MobSpCartApi::add(UID, $pid,  $sku_pkid, $count);

            if($result['status']){
                $this->success('添加成功');
            }else{
                $this->error($result['info'],'');
            }
        }
    }

    public function product_kind()
    {
        $this->assignTitle('商城');

        $result = MobShopApi::querySubCategory(1);
        if(!$result['status']){
            $this->error($result['info']);
        }

        //获取类目
        $cate = [];
        foreach ($result['info'] as $val){
            $cate[] = [
                'name' => $val['name'],
                'img'  => PcFunctionHelper::getImgUrl($val['img_id']),
                'id'   => $val['id']
            ];
        }
        $this->assign('cate', $cate);

        return $this->fetch();
    }

    public function product_list()
    {
        $this->assignTitle('商品列表');
        $keyword = trim($this->_param('keyword'));
        $order = $this->_param('order','d');
        $data = [
            'cate_id' => $this->_param('id'),
            'page_size' => 10,
            'page_index' => $this->_param('p', 1),
            'order' => $order
        ];

        $price = $this->_param('price', 0);
        switch ($price){
            case 1:
                $data['l_price'] = 1;
                $data['r_price'] = 999.99*100;
                break;
            case 2:
                $data['l_price'] = 1000*100;
                $data['r_price'] = 1999.99*100;
                break;
            case 3:
                $data['l_price'] = 2000*100;
                $data['r_price'] = 2999.99*100;
                break;
            case 4:
                $data['l_price'] = 3000*100;
                $data['r_price'] = 999999999*100;
                break;
        }

        if(!empty($keyword)) $data['keyword'] = $keyword;

        $result = MobProductApi::search($data);
        if(!$result['status']) $this->error($result['info']);

        $list = [];
        foreach ($result['info']['list'] as $val){
            $list[] = [
                'id' => $val['id'],
                'name' => $val['name'],
                'synopsis' => $val['synopsis'],
                'price' => number_format(intval($val['price']) / 100, 2),
                'main_img' => PcFunctionHelper::getImgUrl($val['main_img'],120)
            ];
        }

        if(IS_POST){
            $this->success($list, '');
        }else{
            $this->assign('list', $list);

            return $this->fetch();
        }
    }

    public function product_detail()
    {
        $this->assignTitle('商品详情');
        $pid = $this->_param('pid','','商品错误');
        $result = MobProductApi::detail($pid);
        if(!$result['status']) $this->error($result['info']);

        $info = $result['info'];

        $detail = $info;
        $detail['main_img'] = PcFunctionHelper::getImgUrl($detail['main_img']);

        foreach ($detail['carousel_images'] as &$img){
            $img = PcFunctionHelper::getImgUrl($img);
        }
        unset($img);

        $this->assign('detail', $detail);

        $sku_list = [];
        foreach ($detail['sku_list'] as $val){
            $sku_list[] = [
                'sku_pkid' => $val['sku_pkid'],
                'sku_id' => $val['sku_id'],
                'sku_desc' => $val['sku_desc'],
                'ori_price' => intval($val['ori_price']) / 100,
                'price' => intval($val['price']) / 100,
                'quantity' => $val['quantity'],
                'icon_url' => PcFunctionHelper::getImgUrl($val['icon_url'], 120)
            ];
        }
        $sku = [
            'sku_list' => $sku_list,
            'sku_info' => $detail['sku_info']
        ];

        $this->assign('product_sku', json_encode($sku, JSON_UNESCAPED_UNICODE));
        $this->assign('pid', $pid);

        return $this->fetch();
    }

    public function submit_order()
    {

        $cart_ids = $this->_param('cart_ids');
        $this->assign('cart_ids', $cart_ids);
        $cart_ids = explode(',',$cart_ids);
        $cart_info = [];

        $result = (new ShoppingCartQueryAction)->getInfo(UID, $cart_ids);
        if($result['status']){
            $cart_info = $result['info'];
        }

        if(count($cart_info)==0){
            $this->redirect('shop/cart');
        }

        foreach($cart_info as &$val){
            $val['icon_url'] = PcFunctionHelper::getImgUrl($val['icon_url'], 80);
            $val['price'] = intval($val['price']) / 100;
            $val['ori_price'] = intval($val['ori_price']) / 100;
        }

        $this->assign('cart_info', $cart_info);

        //获取收货地址
        $result = MobAddressApi::query(UID);
        if(!$result['status']) $this->error($result['info']);
        $address = [];
        $address_default = [];
        $default = 0;
        foreach ($result['info'] as $add){
            if($add['is_default']){
                $address_default = $add;
                $default = $add['id'];
            }
            $address[] = [
                'id' => $add['id'],
                'is_default' => (bool)$add['is_default'],
                'name' => $add['contactname'],
                'phone' => $add['mobile'],
                'detail' => $add['province'].$add['city'].$add['area'].$add['detailinfo']
            ];
        }

        $this->assign('default', $default);
        $this->assign('address_default', $address_default);
        $this->assign('address', json_encode($address, JSON_UNESCAPED_UNICODE));

        return $this->fetch();
    }

    public function pay()
    {

        if(IS_POST){
            //生成订单
            $address_id = $this->_param('address_id','','未选择收货地址');
            $cart_ids = $this->_param('cart_ids','','没有选中商品');
            $note = '';

            $result = MobOrderApi::create(UID, $cart_ids, $address_id, true, $note);


            if(!$result['status']) $this->error($result['info']);
            $info = $result['info'];

            //跳转微信支付
            $this->redirect(config('site_url').'/wxpay/jump2pay?pay_code='.$info['pay_code']);

        }

    }

    public function coding()
    {
        $this->assignTitle('商城');
        return $this->fetch();
    }

}