<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-20
 * Time: 15:27
 */

namespace app\src\shoppingCart\action;


use app\src\goods\model\Product;
use app\src\base\action\BaseAction;
use app\src\shoppingCart\logic\ShoppingCartLogic;
use app\src\shoppingCart\model\ShoppingCart;

class ShoppingCartQueryV103Action extends BaseAction
{
    /**
     * 1. 处理了购物车正常状态的情况
     * @param $uid
     * @return array
     */
    public function query($uid){

        $logic = new ShoppingCartLogic();

        $result = $logic->queryAll($uid);

        
        if($result['status'] && is_array($result['info'])){
            $cartItems = $result['info'];

            $cartItems = $this->checkCartItems($cartItems);

            $cartItems = $this->filterFields($cartItems);

            $cartItems = $this->combineItems($cartItems);

            return $this->success($cartItems);
        }

        return $this->result($result);
    }

    /**
     * 合并商品
     * 1. 同一发布人
     * 2. 同一产地
     * @param $cartItems
     * @return array
     */
    private function combineItems($cartItems){
        $store = [];
        foreach ($cartItems as $item){
            $key = 'K'.$item['store_id'];
            if(!isset($store[$key])){
                $store[$key] = [
                    'store_name'=>$item['store_name'],
                    'store_id'=>$item['store_id'],
                    'store_boss_name'=>$item['store_boss_name'],
                    'store_boss_uid'=>$item['store_boss_uid'],
                    'store_logo'=>$item['store_logo'],
                    'goods'=>[]
                ];
            }
            unset($item['store_name']);
//            unset($item['store_id']);
            unset($item['store_boss_name']);
            unset($item['store_boss_uid']);
            unset($item['store_logo']);
            array_push($store[$key]['goods'],$item);
        }

        return array_values($store);
    }

    /**
     * 过滤部分key
     * @param $items
     * @return mixed
     */
    private function filterFields($items){
        foreach ($items as &$item){
            unset($item['product_status']);
        }
        return $items;
    }

    /**
     * 检查购物车项的有效性
     *
     * 1. 检查是否下架了
     * 2. 检查是否过期了
     * 3. 检查库存是否没了
     * 4. 商品已经被删除
     * @author hebidu <email:346551990@qq.com>
     * @param $cartItems
     */
    private function checkCartItems($cartItems){

        $updateEntities = [];

        foreach ($cartItems as &$item){

            $cart_status = $this->checkItem($item);

            if($cart_status != ShoppingCart::CART_STATUS_NORMAL){
                array_push($updateEntities,[
                    'id'=>$item['id'],
                    'item_status'=>$cart_status,
                ]);

                $item['item_status_desc'] = lang('tip_cart_status_'.$cart_status);
            }else{
                $item['item_status_desc'] = 'ok';
            }

            $item['item_status'] = $cart_status;
        }

        if(count($updateEntities) > 0){

            $logic = new ShoppingCartLogic();
            $logic->saveAll($updateEntities);

        }

        return $cartItems;

    }

    private function checkItem($item){

        if(is_null($item['onshelf'])){
            return ShoppingCart::CART_STATUS_GOODS_DELETE;
        }

        if($item['onshelf'] == Product::SHELF_OFF){
            return ShoppingCart::CART_STATUS_SHELF_OFF;
        }

        if($item['product_status'] == Product::SHELF_OFF){
            return ShoppingCart::CART_STATUS_SHELF_OFF;
        }

        if($item['expire_time'] < time() && $item['expire_time'] > 0){
            return ShoppingCart::CART_STATUS_INVALID;
        }

        if($item['quantity'] < $item['count']){
            return ShoppingCart::CART_STATUS_INVENTORY_LACK;
        }

        return ShoppingCart::CART_STATUS_NORMAL;
    }
}