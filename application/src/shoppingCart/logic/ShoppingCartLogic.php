<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-20
 * Time: 15:07
 */

namespace app\src\shoppingCart\logic;


use app\src\base\logic\BaseLogic;
use app\src\shoppingCart\model\ShoppingCart;
use think\Db;
use think\exception\DbException;

class ShoppingCartLogic extends BaseLogic
{

    public function _init()
    {
        $this->setModel(new ShoppingCart());
    }


    /**
     * 查询所有购物车项
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @return array
     */
    public function queryAll($uid){
        try{
            $result = Db::table("itboye_shopping_cart")->alias("cart")
            ->field("store.name as store_name,store.id as store_id,m.nickname as store_boss_name,store.uid as store_boss_uid,store.logo as store_logo,p.product_code,m.nickname as publisher_name ,p.place_origin,dt.name as unit_desc,attr.contact_name,attr.expire_time,sku.quantity,cart.*,p.onshelf,p.status as product_status")
            ->join(["itboye_product"=>"p"],"p.id = cart.p_id","left")
            ->join(["itboye_store"=>"store"],"store.id = p.store_id","left")
            ->join(["common_member"=>"m"],"m.uid = store.uid","left")
            ->join(["itboye_product_sku"=>"sku"],"cart.psku_id = sku.id ","left")
            ->join(["itboye_product_attr"=>"attr"],"p.id = attr.pid","left")
            ->join(["common_datatree"=>"dt"],"p.dt_goods_unit = dt.code and dt.parentid = 37","left")
            ->where('cart.uid',$uid)
            ->select();
            return $this->apiReturnSuc($result);
        }catch (DbException $ex){
            return $this->apiReturnErr($ex);
        }
    }

}