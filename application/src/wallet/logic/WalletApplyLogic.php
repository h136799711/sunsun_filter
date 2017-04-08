<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/6
 * Time: 19:53
 */

namespace app\src\wallet\logic;


use app\src\base\helper\ExceptionHelper;
use app\src\base\helper\ResultHelper;
use app\src\base\logic\BaseLogic;
use app\src\extend\Page;
use app\src\wallet\model\WalletApply;
use think\Db;
use think\exception\DbException;

class WalletApplyLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new WalletApply());
    }

    private function getQuery(){

        $query = Db::table("itboye_wallet_apply")->alias("apply")
                    ->field("op_user.nickname as op_nickname,dt.name as account_type,user.nickname,user.mobile,apply.create_time,apply.id,apply.reason,apply.valid_status,apply.reply_msg,apply.uid,apply.op_uid,apply.money,wa.account,wa.valid_status as wa_valid_status,wa.extra as wa_extra")

                    ->join(["v_user_info"=>"user"],"user.id = apply.uid","LEFT")
                    ->join(["common_member"=>"op_user"],"op_user.uid = apply.op_uid","LEFT")
                    ->join(["itboye_withdraw_account"=>"wa"],"wa.id = apply.account_id","LEFT")
            ->join(["common_datatree"=>"dt"],"dt.id = wa.account_type","LEFT");

        return $query;
    }

    public function queryWithPagingHtml($map = null, $page = ['curpage' => 1, 'size' => 10], $order = false, $params = false, $fields = false)
    {

        try{
            $query = $this->getQuery();
            if(!is_null($map)) $query = $query->where($map);
            if(false !== $order) $query = $query->order($order);
            $start = max(intval($page['curpage']) - 1,0)*intval($page['size']);
            $list = $query -> limit($start,$page['size']) -> select();
            $query = $this->getQuery();
            $count = $query -> where($map) -> count();

            // 查询满足要求的总记录数
            $Page = new Page($count, $page['size']);

            //分页跳转的时候保证查询条件
            if ($params !== false) {
                foreach ($params as $key => $val) {
                    $Page -> parameter[$key] = urlencode($val);
                }
            }

            // 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page -> show();

            return $this -> apiReturnSuc(["show" => $show, "list" => $list]);
        }catch (DbException $ex){
            return $this -> apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }

    public function pass($id,$op_uid){
        Db::startTrans();
        $result = (new WalletApplyLogic())->saveByID($id,['valid_status'=>1,'op_uid'=>$op_uid ]);
        $flag = false;
        $error = "";
        if($result['status']) {
            $result = (new WalletApplyLogic())->getInfo(['id' => $id]);
            if ($result['status']) {
                $money = $result['info']['money'];
                $uid = $result['info']['uid'];
                $result = (new WalletLogic())->minusMoney($uid, $money, '提现成功，扣除余额');
                if($result['status']){
                    $flag = true;
                }
            }
        }
        $error = $result['info'];
        if($flag){
            Db::commit();
            return ResultHelper::success('操作成功');
        }else{
            Db::rollback();
            return ResultHelper::error('操作失败('+$error+')');
        }
    }

}