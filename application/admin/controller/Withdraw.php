<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/6
 * Time: 19:42
 */

namespace app\admin\controller;


use app\src\admin\helper\DateHelper;
use app\src\wallet\logic\WalletApplyLogic;
use app\src\wallet\logic\WalletLogic;
use app\src\wallet\logic\WalletOrderLogicV2;
use app\src\wallet\model\WalletApply;
use app\src\user\logic\MemberLogic;

class Withdraw extends Admin
{
    //充值订单管理
    public function order(){
        $map = [];

        $uid = $this->_param('uid',0);
        $this->assign('uid',$uid);
        $nickname = '';
        if($uid){
            $map['uid'] = $uid;
            $nickname = (new MemberLogic())->getOneInfo($uid);
        }
        $this->assign('nickname',$nickname);

        $order_code = $this->_param('order_code','');
        $this->assign('order_code',$order_code);
        if($order_code) $map['order_code'] = ['like','%'.$order_code.'%'];

        $pay_status = $this->_param('pay_status','');
        $this->assign('pay_status',$pay_status);
        if($pay_status !== '') $map['pay_status'] = $pay_status;

        $page = ['curpage' => $this->_param('p', 1), 'size' => 15];
        $r = (new WalletOrderLogicV2())->queryWithPagingHtml($map, $page, 'id desc');
        $this->assign('list',$r['list']);
        $this->assign('show',$r['show']);
        return $this->boye_display();
    }

    public function query(){
        $uid = $this->_param('uid',0);
        $arr = DateHelper::getDataRange(3);
        $where = array();
        $params = array();
        $status = $this->_param('valid_status',"");
        if($uid != 0){
            $where['apply.uid'] = $uid;
            $params['uid'] = $uid;
        }

        $startdatetime = urldecode(urldecode($arr[0]));
        $enddatetime = urldecode(urldecode($arr[1]));

        $params = array('startdatetime' => $startdatetime, 'enddatetime' => ($enddatetime) );
        $startdatetime = toUnixTimestamp($startdatetime);
        $enddatetime = toUnixTimestamp($enddatetime);
        $where['apply.create_time'] = array( array('EGT', $startdatetime), array('elt', $enddatetime), 'and');

        if($status != "" && $status != -1 ){
            $where['apply.valid_status'] = $status;
        }

        $page = array('curpage'=>$this->_param('p',0),'size'=>10);
        $order = " apply.create_time desc ";

        $result = (new WalletApplyLogic())->queryWithPagingHtml($where,$page,$order,$params);

        if(isset($result['info']['list'])){
            $list = $result['info']['list'];
            $this->assign('valid_status',$status);
            $this->assign("list",$list);
            $this->assign("show",$result['info']['show']);
        }else{
            $this->error($result['info']);
        }
        $this->assign("startdatetime",$startdatetime);
        $this->assign("enddatetime",$enddatetime);
        $this->assign('uid',$uid);
        return $this->boye_display();

    }


    public function verify(){
        $uid = $this->_param('uid',0);
        $arr = DateHelper::getDataRange(3);
        $where = array();
        $params = array();
        $status = $this->_param('status',"");
        if($uid != 0){
            $where['apply.uid'] = $uid;
            $params['uid'] = $uid;
        }
        $startdatetime = urldecode($arr[0]);
        $enddatetime = urldecode($arr[1]);

        $params = array('startdatetime' => urlencode($startdatetime), 'enddatetime' => urlencode($enddatetime),'wxaccountid'=>getWxAccountID());

        $startdatetime = toUnixTimestamp($startdatetime);
        $enddatetime = toUnixTimestamp($enddatetime);
        $where['apply.create_time'] = array( array('EGT', $startdatetime), array('elt', $enddatetime), 'and');

        $where['apply.valid_status'] = 0;

        $page = array('curpage'=>$this->_param('p',0),'size'=>10);
        $order = " apply.create_time desc ";

        $result = (new WalletApplyLogic())->queryWithPagingHtml($where,$page,$order,$params);

        if(isset($result['info']['list'])){
            $list = $result['info']['list'];
            $this->assign("list",$list);
            $this->assign("show",$result['info']['show']);
        }else{
            $this->error($result['info']);
        }
        $this->assign("startdatetime",$startdatetime);
        $this->assign("enddatetime",$enddatetime);
        $this->assign('uid',$uid);
        return $this->boye_display();
    }

    public function deny(){
        $id = $this->_param('id','');
        $reason = $this->_param('deny_reason','');
        $result = (new WalletApplyLogic())->saveByID($id,['valid_status'=>2,'op_uid'=>UID,'reply_msg'=>$reason]);
        if($result['status']){
            $this->success('操作成功',url('Withdraw/verify'));
        }else{
            $this->success('操作失败',url('Withdraw/verify'));
        }
    }

    public function pass(){
        $id = $this->_param('id','');
        $result = (new WalletApplyLogic())->pass($id,UID);
        if($result['status']){
            $this->success('操作成功',url('Withdraw/verify'));
        }else{
            $this->error($result['info'],url('Withdraw/verify'));
        }
    }

}