<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\admin\controller;

use app\src\base\helper\ValidateHelper;
use app\src\i18n\logic\OrgMemberLogic;
use app\src\log\logic\ExchangeLogLogic;
use app\src\message\logic\MessageLogic;
use app\src\order\action\OrderBackAction;
use app\src\order\action\OrderConfirmAction;
use app\src\order\action\OrderShippedAction;
use app\src\order\logic\OrdersCommentLogic;
use app\src\order\logic\OrdersContactinfoLogic;
use app\src\order\logic\OrdersReFundLogic;
use app\src\order\model\OrdersExpress;
use app\src\system\logic\CityLogic;
use app\src\system\logic\DatatreeLogicV2;
use app\src\system\logic\ProvinceLogic;
use think\Log;
use app\src\order\logic\OrdersLogic;
use app\src\order\logic\OrdersInfoViewLogic;
use app\src\order\logic\OrderStatusHistoryLogic;
use app\src\goods\logic\ProductFaqLogic;
use app\src\order\logic\OrdersItemLogic;
use app\src\order\logic\OrdersExpressLogic;
use app\src\user\logic\MemberLogic;
class Orders extends Admin{
    /**
     * 初始化
     */
    protected function _initialize() {
        parent::_initialize();
    }

    /**
     * 商家主动退回订单
     */
    public function backOrder(){
        $id = $this->_param('orderid',0);
        $reason = $this->_param('reason','商家主动取消订单');

        $result = (new OrdersLogic())->getInfo(['id'=>$id],false,false,false,false,true);
        if(!$result['status']){
            $this->error($result['status']);
        }

        $orders = $result['info'];
        if(!($orders instanceof \app\src\order\model\Orders)){
            $this->error("订单信息获取失败，请重试！");
        }

        $order_code = $orders->getOrderCode();
        $result = (new OrderBackAction())->back($orders,UID);
        if($result['status']){
            //======================================
            $this->success("退回成功!");
        }else{
            $this->success("退回失败!");
        }

        //===========推送给用户消息↓
        $text = "您的订单:".$order_code." [被退回],原因:".$reason.". [查看详情]";
        $msg = array(
            'title'=>'订单退回通知',
            'content'=>$text,
            'summary'=>'订单被退回'
        );
        $this->pushOrderMessage($order_code,$msg);
        //===========推送给用户消息↑


    }

    /**
     * 数据统计
     */
    public function statics(){
        //TODO:可能数据库写一个视图效率更高吧，之后考虑
        //TODO:目前先是全表统计，之后可能考虑按时间，当天，本月，本年计算


        //地区管理员所属地区限制
        $AreaMap = $this->OrgAreaMap();

        /**
         * 订单退回
         */
        $map=array(
            'order_status'=>\app\src\order\model\Orders::ORDER_BACK,
        );
        if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//		$orderback=apiCall(OrdersInfoViewApi::COUNT,array($map));
        $orderback = (new OrdersInfoViewLogic())->count($map);
        /**
         * 待确认
         */
        $map=array(
            'order_status'=>\app\src\order\model\Orders::ORDER_TOBE_CONFIRMED,
        );
        if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//		$ordertobeconfirmed=apiCall(OrdersInfoViewApi::COUNT,array($map));
        $ordertobeconfirmed= (new OrdersInfoViewLogic())->count($map);
        /**
         * 待发货
         */
        $map=array(
            'order_status'=>\app\src\order\model\Orders::ORDER_TOBE_SHIPPED,
        );
        if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//		$ordertobeshipped=apiCall(OrdersInfoViewApi::COUNT,array($map));
        $ordertobeshipped=(new OrdersInfoViewLogic())->count($map);
        /**
         * 已发货
         */
        $map=array(
            'order_status'=>\app\src\order\model\Orders::ORDER_SHIPPED,
        );
        if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//		$ordershipped=apiCall(OrdersInfoViewApi::COUNT,array($map));
        $ordershipped=apiCall(OrdersInfoViewApi::COUNT,array($map));
        /**
         * 已收货
         */
        $map=array(
            'order_status'=>\app\src\order\model\Orders::ORDER_RECEIPT_OF_GOODS,
        );
        if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//		$orderreceiptofgoods=apiCall(OrdersInfoViewApi::COUNT,array($map));
        $orderreceiptofgoods=(new OrdersInfoViewLogic())->count($map);
        /**
         * 已退货
         */
        $map=array(
            'order_status'=>\app\src\order\model\Orders::ORDER_RETURNED,
        );
        if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//		$orderreturned=apiCall(OrdersInfoViewApi::COUNT,array($map));
        $orderreturned=(new OrdersInfoViewLogic())->count($map);
        /**
         * 已完成
         */
        $map=array(
            'order_status'=>\app\src\order\model\Orders::ORDER_COMPLETED,
        );
        if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//		$ordercompleted=apiCall(OrdersInfoViewApi::COUNT,array($map));
        $ordercompleted=(new OrdersInfoViewLogic())->count($map);
        /**
         * 取消或交易关闭
         */
        $map=array(
            'order_status'=>\app\src\order\model\Orders::ORDER_CANCEL,
        );
        if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//		$ordercancel=apiCall(OrdersInfoViewApi::COUNT,array($map));
        $ordercancel=(new OrdersInfoViewLogic())->count($map);
        //dump($orderback);
        $orderstatus=array(
            'order_back'=>$orderback['info'],
            'order_tobe_confirmed'=>$ordertobeconfirmed['info'],
            'order_tobe_shipped'=>$ordertobeshipped['info'],
            'order_shipped'=>$ordershipped['info'],
            'order_receipt_of_goods'=>$orderreceiptofgoods['info'],
            'order_returned'=>$orderreturned['info'],
            'order_completed'=>$ordercompleted['info'],
            'order_cancel'=>$ordercancel['info']
        );
        $map=array();
        if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//		$price=apiCall(OrdersInfoViewApi::SUM,array($map,"price"));
        $price=(new OrdersInfoViewLogic())->sum($map,"price");

        /**
         * 商品咨询待回复
         */
        $map=array(
            'reply_time'=>0,
        );
//		$faqtobereply = apiCall(ProductFaqApi::COUNT,array($map));
        $faqtobereply = (new ProductFaqLogic())->count($map);
        /**
         * 积分商品待发货
         */
        $map=array(
            'exchange_status'=>0,
        );
//		$score_goods_tobe_deliver = apiCall(ExchangeLogApi::COUNT,array($map));
        $score_goods_tobe_deliver = (new ExchangeLogLogic())->count($map);

        $this->assign('order_status',$orderstatus);
        $this->assign('price',$price['info']);
        $this->assign('faqtobereply',$faqtobereply['info']);
        $this->assign('score_goods_tobe_deliver',$score_goods_tobe_deliver['info']);
        return $this->boye_display();
    }

    /**
     * @param int $id
     * @discription 评价管理
     */
    public function commentEdit($id=0){
        if (IS_GET) {
//			$r  = apiCall(OrderCommentApi::GET_INFO,array(array('id'=>$id)));
            $r  = (new OrdersCommentLogic())->getInfo(['id'=>$id]);
            if($r['status']){
                $this->assign('entry',$r['info']);
                return $this->boye_display();
            }else{
                Log::record('INFO:' . $r['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
                $this->error($r['info']);
            }
        }else{
            $comment = $this->_param('comment','','内容错误');
//			$r = apiCall(OrderCommentApi::SAVE,array(array('id'=>$id),array('comment'=>$comment)));
            $r = (new OrdersCommentLogic())->save(array('id'=>$id),array('comment'=>$comment));
            if($r['status']){
                $this->success('修改成功！');
            }else{
                Log::record('INFO:' . $r['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
                $this->error($r['info']);
            }
        }
    }
    /**
     * 订单评价 - 分页
     */
    public function comment() {
        $p     = $this->_param('p',0);
        $uid   = $this->_param('uid',0);
        $map   = array();
        $param = array();
        if($uid){
            $map['c.user_id'] = $uid;
            $this->assign('uid',$uid);
        }
        $start = $this->_param('startdatetime',0);
        $this->assign('startdatetime',$start);
        $end   = $this->_param('enddatetime',0);
        $this->assign('enddatetime',$end);
        if($start>0 && $end>0){
            $param['startdatetime'] = $start;
            $param['enddatetime']   = $end;
            $start = toUnixTimestamp($start);
            $end = toUnixTimestamp($end);
            $map['createtime'] = array('BETWEEN',$start);
        }elseif($start>0){
            $param['startdatetime'] = $start;
            $start = toUnixTimestamp($start);
            $map['createtime'] = array('GT',$start);
        }elseif($end>0){
            $param['enddatetime']   = $end;
            $end = toUnixTimestamp($end);
            $map['createtime'] = array('LT',$start);
        }
        $order = $this->_param('order_code','');
        $this->assign('order',$order);
        if($order){
            $param['order_code'] = $order;
            $map['order_code']   = array('LIKE',$order_code);
        }
//		$r = apiCall(OrderCommentApi::QUERY_WITH_USER,array($map,array('curpage'=>$p,'size'=>10),false,$param,'c.*,m.nickname'));
        $r = (new OrdersCommentLogic())->queryWithUser($map,array('curpage'=>$p,'size'=>10),false,$param,'c.*,m.nickname');
        // dump($r);exit;
        if($r['status']){
            $this -> assign('list', $r['info']['list']);
            $this -> assign('show', $r['info']['show']);
            return $this -> boye_display();
        }else{
            Log::record('INFO:' . $r['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this -> error($r['info']);
        }
    }
    /**
     * 订单管理
     */
    public function index() {
        $map = array();
        $params =false;
        $payStatus     = $this->_param('paystatus', '');
        $orderStatus   = $this->_param('orderstatus', '');
        $commentStatus = $this->_param('commentstatus', '');
        $order_code    = $this->_param('order_code', '');
        $userid        = $this->_param('uid', 0);
        $nickname      = $this->_param('username','');

        $startdatetime = $this->_param('startdatetime');
        $startdatetime = toUnixTimestamp($startdatetime);
        $enddatetime   = $this->_param('enddatetime');
        $enddatetime   = toUnixTimestamp($enddatetime);
        if(!empty($startdatetime) && !empty($enddatetime)){
            if($startdatetime === FALSE || $enddatetime === FALSE){
                $params = array('startdatetime' =>$startdatetime, 'enddatetime' =>$enddatetime,'wxaccountid'=>getWxAccountID());
                $map['createtime'] = array( array('EGT', $startdatetime), array('ELT', $enddatetime), 'and');
                $startdatetime = toDatetime( $startdatetime);
                $enddatetime   = toDatetime( $enddatetime);
            }else{
                $params = array('startdatetime' => $startdatetime, 'enddatetime' =>$enddatetime,'wxaccountid'=>getWxAccountID());
                $map['createtime'] = array( array('EGT', $startdatetime), array('ELT', $enddatetime), 'and');
                $startdatetime = toDatetime( $startdatetime);
                $enddatetime   = toDatetime( $enddatetime);
            }
        }
        if (!empty($order_code)) {
            $map['order_code'] = array('like', $order_code . '%');
        }
        if ($nickname != '') {
            $map['username']   = $nickname;
            $params['username'] = $nickname;
        }
        if ($payStatus != '') {
            $map['pay_status']   = $payStatus;
            $params['paystatus'] = $payStatus;
        }
        if ($orderStatus != '') {
            $map['order_status']   = $orderStatus;
            $params['orderstatus'] = $orderStatus;
        }
        if ($commentStatus != '') {
            $map['comment_status']   = $commentStatus;
            $params['commentstatus'] = $commentStatus;
        }

        $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
        $order = " createtime desc ";

        if ($userid > 0){
            $map['uid'] = $userid;
        }

//		$result = (new OrdersInfoViewLogic())->queryWithPagingHtml($map, $page, $order);
        $result = (new OrdersInfoViewLogic())->queryWithPagingHtml($map, $page, $order,$params);
        //
        if ($result['status']) {
            $this -> assign('order_code', $order_code);
            $this -> assign('nickname', $nickname);
            $this -> assign('orderStatus', $orderStatus);
            $this -> assign('commentStatus', $commentStatus);
            $this -> assign('payStatus', $payStatus);
            $this -> assign('startdatetime', $startdatetime);
            $this -> assign('enddatetime', $enddatetime);
            $this -> assign('list', $result['info']['list']);
            $this -> assign('show', $result['info']['show']);
            return $this -> boye_display();
        } else {
            Log::ecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this -> error($result['info']);
        }
    }

    /**
     * 订单确认
     */
    public function sure() {
        $order_code = $this->_param('order_code', '');
        $nickname = $this->_param('nickname','');
        $payStatus = $this->_param('payStatus', "");
        $orderStatus = $this->_param('orderStatus', \app\src\order\model\Orders::ORDER_TOBE_CONFIRMED);
        $userid = $this->_param('uid', 0);
//		$params = array();
        $params =false;
        $map = array();
        $map['order_status'] = $orderStatus;//;
        if($payStatus !== ""){
            $map['pay_status'] = $payStatus;
        }

        //$map['wxaccountid']=getWxAccountID();
        $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
        $order = " createtime desc ";

        if (!empty($order_code)) {
            $map['order_code'] = array('like', $order_code . '%');
            $params['order_code'] = $order_code;
        }
        if (!empty($nickname)) {
            $map['nickname'] = array('like', $nickname . '%');
            $params['nickname'] = $nickname;
        }
        if ($userid > 0) {
            $map['uid'] = $userid;
            $params['uid'] = $userid;
        }

        //地区管理员所属地区限制
        $AreaMap = $this->OrgAreaMap();
        if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
        $result = (new OrdersInfoViewLogic())->queryWithPagingHtml($map, $page, $order,$params);

        //
        if ($result['status']) {

            $this -> assign('order_code', $order_code);

            $this -> assign('orderStatus', $orderStatus);
            $this -> assign('payStatus', $payStatus);
            $this -> assign('nickname', $nickname);
            $this -> assign('show', $result['info']['show']);
            $this -> assign('list', $result['info']['list']);
            return $this -> boye_display();
        } else {
            Log::record('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this -> error($result['info']);
        }
    }

    /**
     * 发货
     */
    public function deliverGoods() {
        $orderStatus = $this->_param('order_status','3');
        $ordercode = $this->_param('order_code', '');
        $nickname = $this->_param('nickname', '');
        $userid = $this->_param('uid', 0);
        $params = array();

        $map = array();
        //$map['wxaccountid']=getWxAccountID();
        //$params['uid'] = $map['uid'];
        if (!empty($ordercode)) {
            $map['order_code'] = array('like','%'. $ordercode . '%');
            $params['order_code'] = $ordercode;
        }
        if (!empty($nickname)) {
            $map['nickname'] = array('like','%'.$nickname . '%');
            $params['nickname'] = $nickname;
        }
        if($orderStatus != ''){
            $map['order_status'] = $orderStatus;
            $params['order_status'] = $orderStatus;
        }

        $map['pay_status']=array(
            'in',array(\app\src\order\model\Orders::ORDER_CASH_ON_DELIVERY,\app\src\order\model\Orders::ORDER_PAID)
        );
        $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
        $order = " createtime desc ";

        if ($userid > 0) {
            $map['u'] = $userid;
        }

        //地区管理员所属地区限制
        $AreaMap = $this->OrgAreaMap();
        if(!empty($AreaMap)) $map['_complex'] = $AreaMap;
//		$result = apiCall(OrdersInfoViewApi::QUERY, array($map, $page, $order, $params));
        $result = (new OrdersInfoViewLogic())->queryWithPagingHtml($map, $page, $order, $params);
        //
        if ($result['status']) {
            $this -> assign('order_code', $ordercode);
            $this -> assign('order_status', $orderStatus);
            $this -> assign('nickname', $nickname);
            $this -> assign('show', $result['info']['show']);
            $this -> assign('list', $result['info']['list']);
            return $this -> boye_display();
        } else {
            Log::record('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this -> error($result['info']);
        }
    }

    /**
     * 查看
     */
    public function view() {
        if (IS_GET) {
            $id = $this->_param('id',0);
            $map = array('id'=>$id);
//			$result = apiCall(OrdersInfoViewApi::GET_INFO, array($map));
            $result = (new OrdersInfoViewLogic())->getInfo($map);
            if ($result['status']) {
                // dump($result['info']);
                $order_code     = $result['info']['order_code'];
                $comment_status = $result['info']['comment_status'];
                $order_create   = $result['info']['createtime'];
                $result['info']['pay_three'] = $result['info']['price'] - $result['info']['discount_money'] - $result['info']['pay_balance'];

                $this -> assign("order", $result['info']);
                //同时查询套餐信息
//				$result = apiCall(OrdersItemApi::QUERY_NO_PAGING, array(array('order_code'=>$order_code)));
                $result = (new OrdersItemLogic())->queryNoPaging(['order_code'=>$order_code]);
                if(!$result['status']){
                    ifFailedLogRecord($result, __FILE__.__LINE__);
                    $this->error($result['info']);
                }
                // dump($result['info']);exit;
                $pacakge_info = [];
                $items = $result['info'];
                if($comment_status != \app\src\order\model\Orders::ORDER_TOBE_EVALUATE){
                    foreach ($items	as &$v) {
                        //已评价订单
                        //商品评价详情
//						$result = apiCall(OrderCommentApi::GET_INFO,array(array('order_code'=>$v['order_code'],'product_id'=>$v['p_id'],'psku_id'=>$v['psku_id'],'group_id'=>$v['group_id'],'package_id'=>$v['package_id'])));
                        $result = (new OrdersCommentLogic())->getInfo(['order_code'=>$v['order_code'],'product_id'=>$v['p_id'],'psku_id'=>$v['psku_id'],'group_id'=>$v['group_id'],'package_id'=>$v['package_id']]);
                        if(!$result['status']){
                            ifFailedLogRecord($result, __FILE__.__LINE__);
                            $this->error($result['info']);
                        }
                        $this->getAttachs($result['info']['id'],10,$result['info']);
                        $v['comment_info'] = $result['info'];
                    }
                }
                // dump($items);exit;
                $this -> assign("items", $items);

                //查询订单状态变更纪录
//				$result = apiCall(OrderStatusHistoryApi::QUERY_NO_PAGING, array(array('order_code'=>$order_code),"create_time asc"));
                $result = (new OrderStatusHistoryLogic())->queryNoPaging(['order_code'=>$order_code],"create_time asc");
//				dump($result);
                if(!$result['status']){
                    ifFailedLogRecord($result, __FILE__.__LINE__);
                    $this->error($result['info']);
                }
                array_unshift($result['info'],array('id'=>'0','reason'=>'订单生成','create_time'=>$order_create,'status_type'=>'ADD'));
                $this -> assign("statushistory", $result['info']);
                return $this -> boye_display();
            } else {
                $this -> error($result['info']);
            }
        }
    }

    /**
     * 单个发货操作
     */
    public function deliver() {
        $result = (new DatatreeLogicV2())->queryNoPaging(['parentid'=>6003]);
        $expresslist = array();
        if(!empty($result)){
            foreach($result as $val){
                $expresslist[$val['code']] = $val['name'];
            }
        }else{
            $this->error("物流公司列表获取失败！");
        }
        if (IS_GET) {
            $id = $this->_param('id',0);
            $map = array('id'=>$id);
            $result = (new OrdersInfoViewLogic())->getInfo($map);
            if($result['status']){
                $this->assign("order",$result['info']);
            }else{
                $this->error("订单信息获取失败！");
            }
            $this->assign("expresslist",$expresslist);
            return $this->boye_display();
        } elseif (IS_POST) {

            $expresscode = $this->_param('expresscode','');
            $expressno = $this->_param('expressno','');
            $uid = $this->_param('uid',0);
            $ordercode = $this->_param('ordercode','');
            $orderOfid = $this->_param('orderOfid','');
            if(empty($ordercode)){
                $this->error("订单编号不能为空");
            }
            if(empty($expresscode) || !isset($expresslist[$expresscode])){
                $this->error("快递信息错误！");
            }
            if(empty($expressno)){
                $this->error("快递单号不能为空");
            }
            $id = $this->_param('id',0);
            $entity = array(
                'expresscode'=>$expresscode,
                'expressname'=>$expresslist[$expresscode],
                'expressno'=>$expressno,
                'note'=>$this->_param('note',''),
                'order_code'=>$ordercode,
                'uid'=>$uid,
            );

            // 1. 修改订单状态为已发货
            $orders = new \app\src\order\model\Orders();

            $orders->setOrderCode($ordercode);
            $orders->setUid($uid);
            $orderExpress = new OrdersExpress($entity);
            $result = (new OrderShippedAction())->shipped($orders,$orderExpress);

            if(!$result['status']){
                ifFailedLogRecord($result['info'], __FILE__.__LINE__);
            }

            //========================================
            if(intval($id) > 0) {
                $text = "亲，您的订单($ordercode)物流信息已改变，快递单号：$expressno,快递公司：".$expresslist[$expresscode].",请注意查收";
                $summary = '物流信息已改变';
            }else{
                $text = "亲，您的订单($ordercode)已发货，快递单号：$expressno,快递公司：".$expresslist[$expresscode].",请注意查收";
                $summary = '订单已发货';
            }

            // 2.发送提醒信息给指定用户
            //===========推送给用户消息↓
            $msg = array(
                'title'=>'物流通知',
                'content'=>$text,
                'summary'=>$summary
            );
            $this->pushOrderMessage($ordercode,$msg);
            //=======================================
            $this->success(L('RESULT_SUCCESS'),url('Admin/Orders/deliverGoods'));

        }
    }

    /**
     * 单个发货操作
     */
    public function deliverEdit() {

        $result = (new DatatreeLogicV2())->queryNoPaging(['parentid'=>6003]);
        $expresslist = array();
        if(!empty($result)){
            foreach($result as $val){
                $expresslist[$val['code']] = $val['name'];
            }
        }else{
            $this->error("物流公司列表获取失败！");
        }

        if (IS_GET) {
            $id = $this->_param('id',0);
            $map = array('id'=>$id);
            $result = (new OrdersInfoViewLogic())->getInfo($map);
            if($result['status']){
                $this->assign("order",$result['info']);
            }else{
                $this->error("订单信息获取失败！");
            }
            $map = array('order_code'=>$result['info']['order_code']);
            $result = (new OrdersExpressLogic())->getInfo($map);
            if($result['status'] && is_array($result['info'])){
                $this->assign("express",$result['info']);
            }
            $this->assign("expresslist",$expresslist);
            return $this->boye_display();
        } elseif (IS_POST) {

            $expresscode = $this->_param('expresscode','');
            $expressno = $this->_param('expressno','');
            $uid = $this->_param('uid',0);
            $ordercode = $this->_param('ordercode','');
            $orderOfid = $this->_param('orderOfid','');
            if(empty($expresscode) || !isset($expresslist[$expresscode])){
                $this->error("快递信息错误！");
            }
            if(empty($expressno)){
                $this->error("快递单号不能为空");
            }
            $entity = array(
                'expresscode'=>$expresscode,
                'expressname'=>$expresslist[$expresscode],
                'expressno'=>$expressno,
                'note'=>$this->_param('note',''),
                'order_code'=>$ordercode,
                'uid'=>$uid,
            );

            if(empty($entity['order_code'])){
                $this->error("订单编号不能为空");
            }

            $result = (new OrdersExpressLogic())->saveByID($orderOfid,$entity);

            if($result['status']){
                $this->success(L('RESULT_SUCCESS'),url('Admin/Orders/deliverGoods'));
            }else{
                $this->error($result['info']);
            }
        }
    }

    /*
     * 修改订单信息
     * */
    public function editorder(){
        if(IS_GET){
            $id = $this->_param('id',0);
            $result = (new OrdersLogic())->getInfo(['id'=>$id]);
            if(!ValidateHelper::legalArrayResult($result)) {
                $this->error('id参数有误');
            }

            $this->assign("price",$result['info']['price']);
            $this->assign('post_price',$result['info']['post_price']);

            $this->assign("id",$id);
            //查询地址
            $result = (new OrdersContactinfoLogic())->getInfo(['id'=>$id]);

            if($result['status']){
                $this->assign('contactinfo',$result['info']);
            }

            return $this->boye_display();
        }else{
            $id = $this->_param('id',0);
            $price = $this->_param('price',0);
            $post_price = $this->_param('postprice','0.00');
            $entity=array('price'=>$price*100.0,'post_price'=>$post_price*100);
            $result = (new OrdersLogic())->saveByID($id,$entity);
            if($result['status']){
                $this->success('修改成功');
            }
        }
    }

    /**
     * 批量确认订单
     */
    public function bulkSure() {
        if (IS_POST) {

            $order_codes = $this->_param('order_codes', -1);
            if ($order_codes === -1) {
                $this -> error(L('ERR_PARAMETERS'));
            }


            foreach($order_codes as $code){
                $orders= new \app\src\order\model\Orders();
                $orders->setOrderCode($code);
                $result = (new OrderConfirmAction())->confirm($code , UID , 0);
                if (!$result['status']) {
                    $this -> error($result['info']);
                }else{

                    //===========推送给用户消息↓
                    $text = "亲，您的订单已被确认,请等待发货，订单号($code)";
                    $msg = array(
                        'title'=>'订单通知',
                        'content'=>$text,
                        'summary'=>'订单已确认'
                    );
                    $this->pushOrderMessage($code,$msg);
                    //===========推送给用户消息↑

                }
            }


            $this -> success(L('RESULT_SUCCESS'), url('Admin/Orders/sure'));

        }
    }

    /**
     * 消息推送
     */
    private function pushMessage($msg_type,$message=array('title'=>' ','content'=>' ','summary'=>' ','extra'=>''),$uid=0,$after_open=false,$pushAll=false){
        $result = (new MessageLogic())->pushMessageAndRecordWithType($msg_type,$message,$uid,$pushAll,$after_open);
        return $result['status'];
    }

    /**
     * 推送订单消息
     */
    private function pushOrderMessage($order_code,$msg=array('title'=>'订单通知','content'=>'订单通知内容','summary'=>'订单通知摘要')){
        //推送订单确认消息
        $result = (new OrdersLogic())->getInfo(['order_code'=>$order_code]);
        if($result['status'] && !is_null($result['info'])){
            $uid= $result['info']['uid'];
            $result = (new OrdersItemLogic())->getInfo(['order_code'=>$order_code]);
            if($result['status']){
                $img = $result['info']['img'];
            }
            $extra = json_encode(array('order_code'=>$order_code,'img'=>$img));
            $after_open = array('type'=>'go_activity','param'=>\app\src\message\model\Message::MESSAGE_ORDER_ACTIVITY,'extra'=>array('order_code'=>$order_code));
            $result = $this->pushMessage(\app\src\message\model\Message::MESSAGE_ORDER,$message=array('title'=>$msg['title'],'content'=>$msg['content'],'summary'=>$msg['summary'],'extra'=>$extra),$uid,$after_open);
        }
    }

    /**
     * 地区管理员地区集map
     */
    private function OrgAreaMap(){

        $permisson = action('OrgArea/check_manager_permisson',array(),'Widget');
        $this->assign('permisson',$permisson);
        $AreaMap = array();
        if($permisson != 7) return $AreaMap;//不是地区管理员
        $cityid = array();
        $map['type'] = 1;
        $map['member_uid'] = UID;
        $result = (new OrgMemberLogic())->queryNoPaging($map);
        if($result['status']){
            foreach($result['info'] as $val){
                array_push($cityid,$val['organization_id']);
            }
        }

        $provinceid = array();
        $map['type'] = 0;
//		$result = apiCall(OrgMemberApi::QUERY_NO_PAGING,array($map));
        $result = (new OrgMemberLogic())->queryNoPaging($map);
        if($result['status']){
            foreach($result['info'] as $val){
                array_push($provinceid,$val['organization_id']);
            }

        }
        $cityid = implode(',',$cityid);
        $provinceid = implode(',',$provinceid);

        $city = array(-1);
        $province = array(-1);
        //查询地区名称
        if(!empty($cityid)){
            $map = "cityid in($cityid)";
            $filed = 'city';
//			$result = apiCall(CityApi::QUERY_NO_PAGING,array($map,false,$filed));
            $result = (new CityLogic())->queryNoPaging($map,false,$filed);
            if($result['status']) $city = array_column($result['info'],'city');
        }

        if(!empty($provinceid)){
            $map = "provinceid in($provinceid)";
            $filed = 'province';
//			$result = apiCall(ProvinceApi::QUERY_NO_PAGING,array($map,false,$filed));
            $result = (new ProvinceLogic())->queryNoPaging($map,false,$filed);
            if($result['status']) $province = array_column($result['info'],'province');
        }
        $AreaMap = array(
            'city' => array('in',$city),
            'province' => array('in',$province),
            '_logic' => 'or'
        );
        return $AreaMap;

    }

}
