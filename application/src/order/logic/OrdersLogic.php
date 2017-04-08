<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-09
 * Time: 20:54
 */

namespace app\src\order\logic;


use app\src\base\helper\ValidateHelper;
use app\src\base\logic\BaseLogic;
use app\src\order\model\Orders;
use think\Db;
use think\exception\DbException;
use app\src\order\enum\PayType;

use app\src\order\model\OrdersContactinfo;
use app\src\order\model\OrdersExpress;
use app\src\order\model\OrdersItem;
use app\src\order\logic\OrdersPaycodeLogic;
use app\src\order\logic\OrderStatusHistoryLogic;

class OrdersLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Orders());
    }

    /**
     * 订单确认操作
     * @param Orders $orders
     * @return \app\src\base\logic\status|array|bool
     */
    public function confirm(Orders $orders){

        $result = $this->getInfo(['order_code'=>$orders->getOrderCode()]);

        if(!ValidateHelper::legalArrayResult($result)){
            return $this->apiReturnErr(lang("err_order_code"));
        }

        $order_info = $result['info'];

        //不是已发货
        if($order_info['order_status'] != Orders::ORDER_TOBE_CONFIRMED){
            return $this->apiReturnErr(lang("err_order_status"));
        }

        //不是已支付
        if($order_info['pay_status'] != Orders::ORDER_PAID){
            return $this->apiReturnErr(lang("err_pay_status"));
        }


        $map    = ['uid'=>$orders->getUid(),'order_code'=>$orders->getOrderCode()];
        $update = ['order_status'=>Orders::ORDER_TOBE_SHIPPED,'updatetime'=>time()];

        $result = $this->save($map,$update);

        return $result;

    }

    /**
     * 订单发货操作
     * @author hebidu <email:346551990@qq.com>
     * @param Orders $orders
     * @param OrdersExpress $ordersExpress
     * @return \app\src\base\logic\status|array|bool
     */
    public function shipped(Orders $orders,OrdersExpress $ordersExpress){

        $result = $this->getInfo(['order_code'=>$orders->getOrderCode()]);

        if(!ValidateHelper::legalArrayResult($result)){
            return $this->apiReturnErr(lang("err_order_code"));
        }

        $order_info = $result['info'];

        //不是已发货
        if($order_info['order_status'] != Orders::ORDER_TOBE_SHIPPED){
            return $this->apiReturnErr(lang("err_order_status"));
        }

        //不是已支付
        if($order_info['pay_status'] != Orders::ORDER_PAID){
            return $this->apiReturnErr(lang("err_pay_status"));
        }

        //1. 开启事务
        Db::startTrans();
        $hasError  = false;
        $error  = "";
        $map    = ['uid'=>$orders->getUid(),'order_code'=>$orders->getOrderCode()];
        $update = ['order_status'=>Orders::ORDER_SHIPPED,'updatetime'=>time()];

        $result = $this->save($map,$update);

        if(!$result['status']){
            $hasError = true;
            $error = $result['info'];
        }else{

            $entity = $ordersExpress->getPoArray();
            $ordersExpressLogic = (new OrdersExpressLogic());
            $result = $ordersExpressLogic->getInfo(['order_code'=>$ordersExpress->getOrderCode()]);

            if(ValidateHelper::legalArrayResult($result)) {
                unset($entity['order_code']);
                unset($entity['uid']);
                $result = $ordersExpressLogic->saveByID($result['info']['id'],$entity);
            }else{
                $result = $ordersExpressLogic->add($entity);
            }

            if(!$result['status']) {
                $hasError = true;
                $error = $result['info'];
            }
        }

        if($hasError){
            Db::rollback();
            return ['status'=>false,'info'=>$error];
        }else{
            Db::commit();
            return ['status'=>true,'info'=>lang('success')];
        }

    }

    /**
     * 确认收货订单
     * @author hebidu <email:346551990@qq.com>
     * @param Orders $orders
     * @return \app\src\base\logic\status|array|bool
     */
    public function receiveGoods(Orders $orders){

        $result = $this->getInfo(['order_code'=>$orders->getOrderCode()]);

        if(!ValidateHelper::legalArrayResult($result)){
            return $this->apiReturnErr(lang("err_order_code"));
        }

        $order_info = $result['info'];

        //不是已发货
        if($order_info['order_status'] != Orders::ORDER_SHIPPED){
            return $this->apiReturnErr(lang("err_order_status"));
        }

        //不是已支付
        if($order_info['pay_status'] != Orders::ORDER_PAID){
            return $this->apiReturnErr(lang("err_pay_status"));
        }

        $map    = ['uid'=>$orders->getUid(),'order_code'=>$orders->getOrderCode()];
        $update = ['order_status'=>Orders::ORDER_RECEIPT_OF_GOODS,'updatetime'=>time()];

        $result = $this->save($map,$update);

        return $result;
    }


    /**
     * 商城订单支付成功 - 回调处理 - 外部请加事务
     * 已知支持 : 微信 余额
     * @Author
     * @DateTime 2016-12-29T15:25:01+0800
     * @param    string     $pay_code  [支付编码]
     * @param    int        $pay_money [支付金额,分]
     * @param    int        $pay_type  [支付类型]
     * @param    string     $trade_no  [暂未使用]
     * @return   apiReturn  [处理结果 一般包含订单 - 写入到第三方支付日志]
     */
    public function paySuccessCall($pay_code='',$pay_money=0,$pay_type,$trade_no=''){
      $now   = time();
      $logic = new OrdersPaycodeLogic();
      //? 支付码
      $r = $logic->getInfo(['pay_code'=>$pay_code]);
      if($r['status'] && $r['info']){
        $r      = $r['info'];
        $uid      = $r['uid'];
        $pay_status = $r['pay_status'];
        $order_content = $r['order_content'];
        if(!$order_content) return returnErr('未知订单[order_content null]');
        //? 处理过
        if(!$pay_status){
          $orders = explode(',', $order_content);

          //业务开始
          foreach ($orders as $v) {
            //订单状态修改
            $this->save(['order_code'=>$v],['pay_status'=>1,'updatetime'=>$now,'pay_type'=>PayType::WXPAY,'pay_code'=>$pay_code,'order_status'=>Orders::ORDER_TOBE_SHIPPED]);
            //写入订单历史
            $map = [
              'reason'      =>'支付订单',
              'create_time' =>$now,
              'isauto'      =>0,
              'order_code'  =>$v,
              'cur_status'  =>Orders::ORDER_TOBE_CONFIRMED,
              'next_status' =>Orders::ORDER_TOBE_SHIPPED,
              'status_type' =>'PAY',
              'operator'    =>$uid,
            ];
            $r = (new OrderStatusHistoryLogic())->add($map);
            if(!$r['status']) return returnErr($r['info']);
          }
          //支付信息修改
          $logic->save(['pay_code'=>$pay_code],['pay_type'=>$pay_type,'true_pay_money'=>$pay_money,'pay_status'=>1,'trade_no'=>$trade_no,'update_time'=>$now]);

          return returnSuc(['uid'=>$uid,'msg'=>$order_content]);
        }else{
          return returnErr('重复支付['.$order_content.']');
        }
      }
      return returnErr('未知支付码['.$pay_code.']');
    }
    /**
     * 订单支付成功
     * @param $order_info
     * @param $pay_info
     */
    public function paySuccess($order_info,$pay_info){
        //1. 订单已支付，则返回
        if($order_info['pay_status'] == Orders::ORDER_PAID){
            return $this->apiReturnErr('payed');
        }

        $update = [
            'pay_status'=>Orders::ORDER_PAID,
            'pay_type'=>$pay_info['pay_type'],
            'pay_code'=>$pay_info['pay_code'],
            'pay_balance'=>$pay_info['pay_balance'],
            'updatetime'=>time()
        ];

        $map = [
            'uid'=>$order_info['uid'],
            'order_code'=>$order_info['order_code']
        ];

        return $this->save($map,$update);

    }


    /**
     * 完成订单
     * @param Orders $orders
     * @return \app\src\base\logic\status|array|bool
     */
    public function autoCompleteOrder($orders){

        if(!isset($orders['uid']) || !isset($orders['order_code'])){
            return false;
        }

        if($orders['order_status'] != Orders::ORDER_RECEIPT_OF_GOODS){
            return $this->apiReturnErr(lang("err_order_status"));
        }

        if($orders['pay_status'] != Orders::ORDER_PAID){
            return $this->apiReturnErr(lang("err_pay_status"));
        }
        $map = ['uid'=>$orders['uid'],'order_code'=>$orders['order_code']];

        $result = $this->save($map,['order_status'=>Orders::ORDER_COMPLETED,'updatetime'=>time()]);

        return $result;
    }
    /**
     * 自动关闭订单
     * @param Orders $orders
     * @return \app\src\base\logic\status|array|bool
     */
    public function autoCloseOrder($orders){
        return $this->cancel($orders);
    }

    /**
     * 取消订单
     * @author hebidu <email:346551990@qq.com>
     * @param Orders $orders
     * @return \app\src\base\logic\status|array|bool
     */
    public function cancel(Orders $orders){

        $result = $this->getInfo(['order_code'=>$orders->getOrderCode()]);

        if(!ValidateHelper::legalArrayResult($result)){
            $this->apiReturnErr(lang("err_order_code"));
        }

        $order_info = $result['info'];

        if($order_info['order_status'] != Orders::ORDER_TOBE_CONFIRMED){
            return $this->apiReturnErr(lang("err_order_status"));
        }

        if($order_info['pay_status'] != Orders::ORDER_TOBE_PAID){
            return $this->apiReturnErr(lang("err_pay_status"));
        }
        $map = ['uid'=>$orders->getUid(),'order_code'=>$orders->getOrderCode()];
        $result = $this->save($map,['order_status'=>Orders::ORDER_CANCEL,'updatetime'=>time()]);

        return $result;
    }

    /**
     * 退回订单
     * @param Orders $orders
     * @return array|string
     */
    public function backOrder(Orders $orders){
        if($orders->getOrderStatus() != Orders::ORDER_TOBE_CONFIRMED){
            return $this->apiReturnErr(lang("err_order_status"));
        }

        $map = ['uid'=>$orders->getUid(),'order_code'=>$orders->getOrderCode()];
        $updateEntity = ['order_status'=>Orders::ORDER_BACK,'updatetime'=>time()];
//        dump($map);
        $result = $this->save($map,$updateEntity);

        return $result;
    }

    /**
     * 添加订单信息
     * @author hebidu <email:346551990@qq.com>
     * @param $items
     * @param Orders $orders
     * @param OrdersContactinfo $contactInfo
     * @return array
     * @throws \Exception
     */
    public function addOrder($items,Orders $orders,OrdersContactinfo $contactInfo){

        Db::startTrans();
        $flag = true;
        $info = "";
        $result = $this->add($orders->getModelArray());

        if(!$result['status']){
            $flag = false;
            $info = empty($result['info'])? lang('err_add_order_info_fail'):$result['info'];
        }
        $info = $result['info'];

        $result = $contactInfo -> data($contactInfo->getModelArray()) ->isUpdate(false) -> save();

        if ($result === false) {
            $flag = false;
            $info = $contactInfo -> getError();
        }

        $order_items = [];
        foreach ($items as $item){
            if($item instanceof OrdersItem){
                array_push($order_items ,  $item->getModelArray() );
            }
        }

        $ordersItemModel = new OrdersItem();

        $result = $ordersItemModel->saveAll($order_items,true);

        if ($result === false) {
            $flag = false;
            $info = $ordersItemModel -> getError();
        }



        if($flag){
            Db::commit();
            return $this->apiReturnSuc($info);
        }else{
            Db::rollback();
            return $this->apiReturnErr($info);
        }
    }

    /**
     * 获取订单信息包含订单拥有者昵称
     * @param $map
     * @return array
     */
    public function getInfoWithPublisherName($map){

        try{

            $result = Db::table("itboye_orders")->alias("orders")
                ->field("oc.city,oc.postal_code,oc.id_card,oc.detailinfo,oc.mobile,oc.area,oc.province,oc.contactname,oc.country,orders.*,m.nickname as publisher_name")
                ->join(["itboye_orders_contactinfo"=>"oc"],"oc.order_code = orders.order_code","LEFT")
            ->join(["itboye_store"=>"store"],"store.id = orders.storeid","LEFT")
            ->join(["common_member"=>"m"],"m.uid = store.uid","LEFT")
                ->where($map)
            ->find();

            return $this->apiReturnSuc($result);
        }catch (DbException $ex){
            return $this->apiReturnErr($ex->getMessage());
        }
    }
}