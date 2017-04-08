<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-06
 * Time: 10:06
 */

namespace app\src\wallet\logic;

use app\src\base\helper\ExceptionHelper;
use app\src\base\logic\BaseLogic;
use app\src\base\utils\CodeGenerateUtils;
use think\Db;
use think\Exception;

use app\src\wallet\model\Wallet;
use app\src\wallet\model\WalletHis;
use app\src\wallet\model\WalletApply;
use app\src\wallet\logic\WalletOrderLogicV2;

use app\src\repair\logic\RepairOrderLogicV2;
use app\src\repair\logic\RepairLogicV2;

use app\src\order\model\Orders;
use app\src\order\logic\OrdersLogic;
use app\src\order\logic\OrdersPaycodeLogic;
use app\src\order\logic\OrderStatusHistoryLogic;

use app\src\message\enum\MessageType;
use app\src\message\facade\MessageFacade;

class WalletLogic extends BaseLogic
{
    const PAY_TYPE = 4;
    //初始化
    protected function _init(){
        $this->setModel(new Wallet());
    }


    /**
     * 余额充值成功回调 - 订单处理 - 外部请加事务
     * 已知支持 : 微信 余额 支付宝
     * @Author
     * @DateTime 2016-12-29T15:25:01+0800
     * @param    string     $pay_code  [支付编码]
     * @param    int        $pay_money [支付金额,分]
     * @param    int        $pay_type  [支付类型]
     * @param    string     $trade_no  [暂未使用]
     * @return   apiReturn  [处理结果 一般为订单号 - 写入到第三方支付日志]
     */
    public function paySuccess($pay_code='',$pay_money=0,$pay_type,$trade_no=''){
        // 查询支付订单
        $orderLogic = new WalletOrderLogicV2();
        $r = $orderLogic->getInfo(['pay_code'=>$pay_code]);
        if($r){
            $uid        = (int) $r['uid'];
            $order_code = $r['order_code'];
            //? 处理过
            if($r['pay_status'] == WalletOrderLogicV2::PAYED) return returnErr('重复支付['.$order_code.']');
            //修改订单
            $orderLogic->save(['pay_code'=>$pay_code],['pay_money'=>$pay_money,'pay_type'=>$pay_type,'pay_status'=>WalletOrderLogicV2::PAYED,'trade_no'=>$trade_no]);
            //增加余额+记录
            $r = $this->plusMoney($uid,$pay_money,'余额充值');
            if($r['status']){
                return returnSuc(['uid'=>$uid,'msg'=>$order_code]);
            }else{
                return returnErr('余额充值失败:'.$r['info']);
            };

        }else{
            return returnErr('未知支付码['.$pay_code.']');
        }
    }
    //业务 - 余额充值支付信息 - 每次重新生成
    public function order(array $params){
        extract($params);
        $now   = time();
        $utils = new CodeGenerateUtils();
        // 添加订单
        $order_code = $utils->getWalletOrderCode($uid);
        $pay_code   = $utils->getWalletPayCode($uid);
        $entity = [
          'uid'         => $uid,
          'money'       => $money,
          'order_code'  => $order_code,
          'pay_type'    => 0,
          'pay_status'  => WalletOrderLogicV2::TOBE_PAY,
          'pay_code'    => $pay_code,
        ];
        $id = (new WalletOrderLogicV2())->add($entity);
        //获取支付信息
        return returnSuc($utils->getPayInfo($uid,$pay_code,$money,$now));
    }

    //? 商城支付单合法 - 给下面pay()用的
    private function isLegalTobePayOrder($pay_code,$uid,$user_money){
        $logic = new OrdersPaycodeLogic();
        //? 待支付订单
        $r = $logic->getInfo(['pay_code'=>$pay_code,'uid'=>$uid,'pay_status'=>0]);
        if(!$r['status']) return returnErr($r['info']);
        if(!$r['info'])   return returnErr(Linvalid('pay_code or uid or pay_status'));
        $r = $r['info'];
        $pay_money     = $r['pay_money'];
        if($user_money<$pay_money) return returnErr(L('wallet_not_enough'));
        $order_content = $r['order_content'];
        $order_money   = 0;
        //? 总价匹配 - 查询各订单
        $r = (new OrdersLogic())->queryNoPaging(['order_code'=>['in',$order_content]]);
        if(!$r['status']) return returnErr($r['info']);
        if(!$r['info'])   return returnErr(Linvalid('pay_code or uid'));
        $orders = $r['info'];
        foreach ($orders as $v) {
            $order_money += $v['price'];
        }
        if($order_money !== $pay_money) return returnErr(L('order_money_err'));
        return returnSuc($pay_money);
    }

    //业务 - 余额充值的支付及回调
    public function pay(array $params){
        $now = time();
        extract($params);
        $order_type = substr($pay_code,0,2);
        //用户可用余额
        $user_money = $this->getBalanceAvailable($uid);
        if($order_type == 'PA'){        //商城订单
            //检查支付单
            $r = $this->isLegalTobePayOrder($pay_code,$uid,$user_money);
            if(!$r['status']) return returnErr($r['info']);
            $pay_money = (int) $r['info'];

            //业务开始
            Db::startTrans();
            //处理成功回调
            $r = (new OrdersLogic())->paySuccessCall($pay_code,$pay_money,self::PAY_TYPE,'');
            if(!$r['status']) return returnErr($r['info'],true);
            $order_content = $r['info']['msg'];
            //减少用户余额+记录
            $r = $this->minusMoney($uid,$pay_money,'支付订单['.$order_content.']');
            if(!$r['status']) return returnErr($r['info'],true);
            Db::commit();

            return returnSuc(L('success'));
        }elseif($order_type == 'WX'){        //维修订单
            //检查维修单
            $r = (new RepairOrderLogicV2())->getInfo(['pay_code'=>$pay_code,'uid'=>$uid,'pay_status'=>RepairOrderLogicV2::TOBE_PAY]);
            if(!$r) return returnErr(Linvalid('pay_code or uid or pay_status'));
            $pay_money  = $r['money'];
            $repair_id  = $r['repair_id'];
            if($user_money < $pay_money) return returnErr(L('wallet_not_enough'));
            $order_code = $r['order_code'];

            //业务开始
            Db::startTrans();
            $r = (new RepairLogicV2())->paySuccess($pay_code,$pay_money,self::PAY_TYPE,'');
            if(!$r['status']) return returnErr($r['info'],true);
            //减少用户余额+记录
            $r = $this->minusMoney($uid,$pay_money,'支付订单['.$order_code.']');
            if(!$r['status']) return returnErr($r['info'],true);
            Db::commit();

            return returnSuc(L('success'));
        }else{
            return returnErr(Linvalid('pay_code'));
        }
    }

    public function isApplying($uid){
        return (new WalletApply())->where(['uid'=>$uid,'valid_status'=>0])->find();
    }

    //业务 - 提现申请
    public function apply(array $params){
        extract($params);
        //? 申请中
        if($this->isApplying($uid)) return returnErr(L('err_applying'));
        //? 余额够
        if($this->getBalanceAvailable($uid) < $money) return returnErr(L('wallet_not_enough'));
        if($money > 99999999999) return returnErr(L('apply_too_much'));
        //添加申请记录
        $map = [
            'create_time'  => time(),
            'reason'       => $reason ? $reason : '提现申请',
            'valid_status' => 0,
            'reply_msg'    => '',
            'uid'          => $uid,
            'op_uid'       => 0,
            'money'        => $money,
            'account_id'   => $account_id,
        ];
        $model = new WalletApply();
        return returnSuc($model->insertGetId($map));
    }

    //获取可用余额 - 账户冻结或其他返回0
    public function getBalanceAvailable($uid){
        $model = $this->getModel();
        $r =  $model->where(['uid'=>$uid])->find();
        if($r){
            if($r['wallet_type'] === 1) return 0;
            else return max(intval($r['account_balance']) - intval($r['frozen_funds']),0);
        }else{
            return 0;
        }
    }
    /**
     * 检查用户可用余额
     * @param int $uid
     * @param int $money
     * @return array
     */
    public function checkUserMoney($uid=0,$money=0){
        $uid   = intval($uid);
        $money = floatval($money);

        if($money <=0)  return $this->apiReturnSuc('pass');
        $result = $this->getInfo(['uid'=>$uid]);

        if(!$result['status']){
            return $this->apiReturnErr($result['info']);
        }

        $wallet = $result['info'];
        if(empty($wallet)){
            return $this->apiReturnErr("用户($uid)的钱包信息不存在");
        }

        if(1 === intval($wallet['wallet_type'])) return $this->apiReturnErr('用户余额账户冻结中');

        $u_money = floatval($wallet['account_balance']) - floatval($wallet['frozen_funds']);
        if($u_money < $money) return $this->apiReturnErr('余额不足');

        return $this->apiReturnSuc('pass');
    }

    /**
     * 获取可用余额与钱包状态
     * @param $uid
     * @return array
     */
    public function getBalance($uid){
        $r = $this->getInfoIfNotExistThenAdd($uid);
        $info = $r['info'];
        if($r['status']){
            if(!$info)  return $this->apiReturnErr(Linvalid('uid'));
            return $this->apiReturnSuc([
                'total'       => $info['account_balance'],
                'frozen'      => $info['frozen_funds'],
                'wallet_type' => $info['wallet_type']
            ]);
        }else{
            return $this->apiReturnErr($info);
        }
    }

    /**
     * 有钱包则返回钱包，没有则添一个
     * @author 老胖子-何必都 <hebiduhebi@126.com>
     * @param $uid
     * @return array
     */
    public function getInfoIfNotExistThenAdd($uid){

        if($uid <= 0){
            return $this->apiReturnErr('用户非法');
        }

        //UID
        $map = ['uid'=>$uid];

        $result = $this->getInfo($map);

        if($result['status'] && empty($result['info'])) {
            $entity = [
                'uid' => $uid,
                'wallet_type' => 0,
                'frozen_funds' => 0,
                'account_balance' => 0,
                'create_time' => NOW_TIME,
            ];

            $result = $this->add($entity);
            if ($result['status']) {
                $entity = [
                    'id' => $result['info'],
                    'uid' => $uid,
                    'wallet_type' => 0,
                    'frozen_funds' => 0,
                    'account_balance' => 0,
                    'create_time' => NOW_TIME,
                ];
                return $this->apiReturnSuc($entity);
            }
        }
        else if(is_array($result['info'])){
            return $this->apiReturnSuc($result['info']);
        }

        return $this->apiReturnErr($result['info']);
    }

    /**
     * 冻结指定余额 - push
     * @param $uid
     * @param $money - 分
     * @return array
     */
    public function freezeMoney($uid, $money){
        $result = $this->getInfoIfNotExistThenAdd($uid);

        if(!$result['status']){
            return $this->apiReturnErr($result['info']);
        }

        $wallet_info = $result['info'];
        if($wallet_info['wallet_type'] == 1){
            return $this->apiReturnErr('账户已冻结，无法操作');
        }

        if($money <= 0){
            return $this->apiReturnErr('操作金额必须大于0');
        }

        if($wallet_info['account_balance'] - $wallet_info['frozen_funds'] <$money){
            return $this->apiReturnErr('余额不足');
        }

        $data = [
            'frozen_funds' => $wallet_info['frozen_funds'] + $money
        ];

        Db::startTrans();
        try{
            $this->saveByID($wallet_info['id'], $data);

            //添加操作记录
            $WalletHis = new WalletHis;
            $WalletHis->data([
                'uid' => $uid,
                'before_money' => $wallet_info['frozen_funds'],
                'after_money' => $wallet_info['frozen_funds'] + $money,
                'plus' => $money,
                'minus' => 0,
                'create_time' => NOW_TIME,
                'dtree_type' => 0,
                'reason' => '冻结指定余额',
                'wallet_type' => 1,
            ])->save();

            Db::commit();
            //推送
            $r = $this->pushWalletMsg($uid, '余额冻结通知', '您的余额被冻结'.round($money/100,2).'元');
            return $this->apiReturnSuc($r['status'] ? L('success'): $r['info']);
        }catch (Exception $e){
            Db::rollback();
            return $this->apiReturnErr(ExceptionHelper::getErrorString($e));
        }
    }

    public function pushWalletMsg($to_uid, $title = '', $content = '', $client = '', $debug = false){
        if ($debug) {
            $to_uid = 73;
            $client = "driver";
        }
        $params = [
            'uid'      => 0,
            'to_uid'   => $to_uid,
            'msg_type' => MessageType::SYSTEM,
            'push'     => 1,
            'record'   => 1,
            'client'   => $client,

            'content'  => $content,
            'extra'    => $content,
            'title'    => $title,
            'summary'  => $content,
        ];
        $facade = new MessageFacade();
        return $facade->pushMsg($params);
    }
    /**
     * 解除冻结指定余额 - push
     * @param $uid
     * @param $money
     * @return array
     */
    public function unFreezeMoney($uid, $money){
        $result = $this->getInfoIfNotExistThenAdd($uid);

        if(!$result['status']){
            return $this->apiReturnErr($result['info']);
        }

        $wallet_info = $result['info'];
        if($wallet_info['wallet_type']==1){
            return $this->apiReturnErr('账户已冻结，无法操作');
        }

        if($money<=0){
            return $this->apiReturnErr('操作金额必须大于0');
        }

        if($wallet_info['frozen_funds']<$money){
            return $this->apiReturnErr('冻结余额小于操作金额');
        }

        $data = [
            'frozen_funds' => $wallet_info['frozen_funds'] - $money
        ];

        Db::startTrans();
        try{
            $this->saveByID($wallet_info['id'], $data);

            //添加操作记录
            $WalletHis = new WalletHis;
            $WalletHis->data([
                'uid' => $uid,
                'before_money' => $wallet_info['frozen_funds'],
                'after_money' => $wallet_info['frozen_funds'] - $money,
                'plus' => 0,
                'minus' => $money,
                'create_time' => NOW_TIME,
                'dtree_type' => 0,
                'reason' => '取消冻结指定余额',
                'wallet_type' => 1,
            ])->save();

            Db::commit();
            //推送
            $r = $this->pushWalletMsg($uid, '余额解冻通知', '您的余额被解冻'.round($money/100,2).'元');
            return $this->apiReturnSuc($r['status'] ? L('success'): $r['info']);
        }catch (Exception $e){
            Db::rollback();
            return $this->apiReturnErr(ExceptionHelper::getErrorString($e));
        }
    }

    /**
     * 减少余额 - push
     */
    public function minusMoney($uid, $money, $reason = ''){
        $result = $this->getInfoIfNotExistThenAdd($uid);

        if(!$result['status']){
            return $this->apiReturnErr($result['info']);
        }

        $wallet_info = $result['info'];
        if($wallet_info['wallet_type']==1){
            return $this->apiReturnErr('账户已冻结，无法操作');
        }

        if($money<=0){
            return $this->apiReturnErr('操作金额必须大于0');
        }

        if($wallet_info['account_balance'] - $wallet_info['frozen_funds'] <$money){
            return $this->apiReturnErr('余额不足');
        }

        $data = [
            'account_balance' => $wallet_info['account_balance'] - $money
        ];

        Db::startTrans();
        try{
            $this->saveByID($wallet_info['id'], $data);

            //添加操作记录
            $WalletHis = new WalletHis;
            $WalletHis->data([
                'uid' => $uid,
                'before_money' => $wallet_info['account_balance'],
                'after_money' => $wallet_info['account_balance'] - $money,
                'plus' => 0,
                'minus' => $money,
                'create_time' => NOW_TIME,
                'dtree_type' => 0,
                'reason' => $reason,
                'wallet_type' => 0,
            ])->save();

            Db::commit();
            //推送
            $r = $this->pushWalletMsg($uid, '余额减少通知', '您的余额减少'.round($money/100,2).'元');
            return $this->apiReturnSuc($r['status'] ? L('success'): $r['info']);
        }catch (Exception $e){
            Db::rollback();
            return $this->apiReturnErr(ExceptionHelper::getErrorString($e));
        }
    }


    /**
     * 增加余额 - push
     * @param $uid
     * @param $money
     * @param string $reason
     * @return array
     */
    public function plusMoney($uid, $money, $reason = ''){
        $result = $this->getInfoIfNotExistThenAdd($uid);

        if(!$result['status']){
            return $this->apiReturnErr($result['info']);
        }

        $wallet_info = $result['info'];
        if($wallet_info['wallet_type']==1){
            return $this->apiReturnErr('账户已冻结，无法操作');
        }

        if($money<=0){
            return $this->apiReturnErr('操作金额必须大于0');
        }

        // if($wallet_info['account_balance'] - $wallet_info['frozen_funds'] <$money){
        //     return $this->apiReturnErr('余额不足');
        // }

        $data = [
            'account_balance' => $wallet_info['account_balance'] + $money
        ];

        Db::startTrans();
        try{
            $this->saveByID($wallet_info['id'], $data);

            //添加操作记录
            $WalletHis = new WalletHis();
            $WalletHis->data([
                'uid' => $uid,
                'before_money' => $wallet_info['account_balance'],
                'after_money' => $wallet_info['account_balance'] + $money,
                'plus' => $money,
                'minus' => 0,
                'create_time' => NOW_TIME,
                'dtree_type' => 0,
                'reason' => $reason,
                'wallet_type' => 0,
            ])->save();

            Db::commit();
            //推送
            $r = $this->pushWalletMsg($uid, '余额增加通知', '您的余额增加'.round($money/100,2).'元');
            return $this->apiReturnSuc($r['status'] ? L('success'): $r['info']);
        }catch (Exception $e){
            Db::rollback();
            return $this->apiReturnErr(ExceptionHelper::getErrorString($e));
        }
    }

    /**
     * 余额转账
     * @param $uid
     * @param $to_uid
     * @param $money
     * @param string $reason
     * @return array
     */
    public function transfer($uid, $to_uid, $money, $reason = '余额转账'){

        if($uid <= 0 || $to_uid <= 0){
            return $this->apiReturnErr('用户id非法');
        }

        if($money <= 0){
            return $this->apiReturnErr('操作金额必须大于0');
        }

        $result = $this->getInfoIfNotExistThenAdd($uid);

        if(!$result['status']){
            return $this->apiReturnErr($result['info']);
        }

        $wallet_info1 = $result['info'];
        if($wallet_info1['wallet_type']==1){
            return $this->apiReturnErr('转账方账户已冻结，无法操作');
        }

        if($wallet_info1['account_balance'] - $wallet_info1['frozen_funds'] <$money){
            return $this->apiReturnErr('余额不足');
        }

        $result = $this->getInfoIfNotExistThenAdd($to_uid);

        if(!$result['status']){
            return $this->apiReturnErr($result['info']);
        }

        $wallet_info2 = $result['info'];
        if($wallet_info2['wallet_type']==1){
            return $this->apiReturnErr('被转账方账户已冻结，无法操作');
        }


        $data1 = [
            'account_balance' => $wallet_info1['account_balance'] - $money
        ];

        $data2 = [
            'account_balance' => $wallet_info2['account_balance'] + $money
        ];

        Db::startTrans();
        try{
            $this->saveByID($wallet_info1['id'], $data1);

            //添加操作记录
            $WalletHis = new WalletHis;
            $WalletHis->data([
                'uid' => $uid,
                'before_money' => $wallet_info1['account_balance'],
                'after_money' => $wallet_info1['account_balance'] - $money,
                'plus' => $money,
                'minus' => 0,
                'create_time' => NOW_TIME,
                'dtree_type' => 0,
                'reason' => $reason,
                'wallet_type' => 0,
            ])->save();

            $this->saveByID($wallet_info2['id'], $data2);

            //添加操作记录
            $WalletHis = new WalletHis;
            $WalletHis->data([
                'uid' => $uid,
                'before_money' => $wallet_info2['account_balance'],
                'after_money' => $wallet_info2['account_balance'] + $money,
                'plus' => $money,
                'minus' => 0,
                'create_time' => NOW_TIME,
                'dtree_type' => 0,
                'reason' => $reason,
                'wallet_type' => 0,
            ])->save();

            Db::commit();
            return $this->apiReturnSuc(lang("success"));
        }catch (Exception $e){
            Db::rollback();
            return $this->apiReturnErr(ExceptionHelper::getErrorString($e));
        }

    }

}