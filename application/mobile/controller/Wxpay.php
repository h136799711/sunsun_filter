<?php
/**
 * Created by PhpStorm.
 * User: xiao
 * Date: 2017/2/9
 * Time: 下午1:56
 */

namespace app\mobile\controller;

use app\src\order\logic\OrdersPaycodeLogic;
use app\src\repair\logic\RepairOrderLogicV2;
use app\src\wxpay\action\WxPayJsAction;
use app\src\wxpay\action\WxPayJsNotifyAction;

class wxpay extends MobileController
{
    //跳转支付
    public function jump2pay(){
        $pay_code = $this->_param('pay_code');
        $from = $this->_param('from');

        if($from=='repair'){
            //重复支付判断
            $map = [
                'pay_code' => $pay_code
            ];
            $result = (new RepairOrderLogicV2)->getInfo($map);
            if(empty($result)){
                $this->error('订单错误','repair/order');
            }
            $pay_money = $result['money'];
        }else{
            //重复支付判断
            $map = [
                'pay_code' => $pay_code
            ];
            $result = (new OrdersPaycodeLogic)->getInfo($map);
            if(!$result['status']) $this->error('未知错误');

            if(empty($result['info'])){
                $this->error('订单错误','order/index');
            }
            $pay_money = $result['info']['pay_money'];

            if((bool)$result['info']['pay_status']){
                $this->error('订单已支付', url('order/index'));
            }
        }

        $this->assignTitle('微信支付');
        $WxPayJsAction = new WxPayJsAction();

        $name = $pay_code;
        $code = $pay_code;
        $price = intval($pay_money) / 100;
        $price = 0.01;
        if($price <= 0) $this->error('错误的付款金额');
        try{
            $jsApiParameters = $WxPayJsAction->buildPay($name, $code, $price);
        }catch (\Exception $e){
            if($from == 'repair'){
                $this->error('支付已过期','repair/order');
            }else{
                $this->error('支付已过期',url('order/index'));
            }

        }

        $this->assign('jsApiParameters', $jsApiParameters);
        $this->assign('price', $price.'元');
        $this->assign('from', $from);
        return $this->fetch();
    }

    public function wxnotify(){
        $GLOBALS['HTTP_RAW_POST_DATA'] = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $WxPayJsNotifyAction = new WxPayJsNotifyAction();
        $WxPayJsNotifyAction->Handle(false);

    }

    public function paysuccess(){
        $from = $this->_param('from');
        if($from=='repair'){
            $this->success('支付成功', url('repair/order'));
        }else{
            $this->success('支付成功', url('order/index'));
        }
    }
}