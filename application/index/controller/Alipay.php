<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-05
 * Time: 16:37
 */
namespace app\index\controller;

use app\src\alipay\action\AlipayNotifyAction;
use app\src\alipay\po\AlipayNotifyPo;
use think\Controller;

// use app\src\alipay\po\AlipayNotfiyPo;
/**
 * 支付宝
 * Class Alipay
 * @author hebidu <email:346551990@qq.com>
 * @package app\index\controller
 */
class Alipay extends Controller{


    public function notify(){

      $arr   = $_POST;
      $debug = false;
      if($debug){
        $arr = [
"total_amount"=>"2.00","buyer_id"=>"2088912004218878","trade_no"=>"2017010921001004870256035542","body"=>"\u864e\u5934\u5954","notify_time"=>"2017-01-09 14:59:40","subject"=>"WX17814585693411002C","sign_type"=>"RSA","buyer_logon_id"=>"188****0674","auth_app_id"=>"2016122704665497","charset"=>"utf-8","notify_type"=>"trade_status_sync","invoice_amount"=>"2.00","out_trade_no"=>"WX17814585693411002C","trade_status"=>"TRADE_SUCCESS","gmt_payment"=>"2017-01-09 14:59:40","version"=>"1.0","point_amount"=>"0.00","sign"=>"s5eWYFNKxvtyIVhbXR\/Z5jq0592I\/+7uuZ86u657qZtPJfB73aIu4M0yY00jtiNbbSIfcDT7rEeEZUye8NRffIpM3\/5eLntpuhYCd0c4F2FBf9QUY051xGKY+3KhDqQQVUioHQZJJhXspU04VEs5siArWUMP+N4OSe5aLo4cJKc=","gmt_create"=>"2017-01-09 14:59:40","buyer_pay_amount"=>"2.00","receipt_amount"=>"2.00","fund_bill_list"=>"[{\"amount\":\"2.00\",\"fundChannel\":\"ALIPAYACCOUNT\"}]","app_id"=>"2016122704665497","seller_id"=>"2088221666614017","notify_id"=>"9c2d5595f3753a52d393dab6b8cc900mpq","seller_email"=>"gaofei158168@sina.com"]
        ;
      }
      addLog("Alipay_notify",$_GET,$arr,"支付宝异步通知");
      $alipayNotfiyPo = new AlipayNotifyPo();
      $action = new AlipayNotifyAction($alipayNotfiyPo);
      $alipayNotfiyPo->init($arr);

      $result =  $action->notify($debug);

      echo $result['info'];
    }


}