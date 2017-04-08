<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-28 17:33:30
 * Description : [Description]
 */

namespace app\src\wallet\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\wallet\model\WithdrawAccount;
use app\src\system\logic\DatatreeLogicV2;
// use think\Db;
// use think\Exception;

/**
 * [simple_description]
 *
 * [detail]
 *
 * @author  rainbow <hzboye010@163>
 * @package app\
 * @example
 */
class WithdrawAccountLogicV2 extends BaseLogicV2 {
    //初始化
    protected function _init(){
        $this->setModel(new WithdrawAccount());
    }

    /**
     * 业务 - 提现账号添加
     * @Author
     * @DateTime 2016-12-29T09:17:51+0800
     * @param    array                    $params [description]
     * @return   [apiReturn]                      [description]
     */
    public function bind(array $params){
      extract($params);
      //check account_type
      if(!(new DatatreeLogicV2())->isParent($account_type,getDtreeId('account_type'))){
        return returnErr(Linvalid('account_type'));
      }
      //bind
      $map = [
      'uid'=>$uid,
      'account_type'  =>$account_type,
      'account'       =>$account,
      'valid_status'  =>0,
      'extra'         =>'',
      ];
      $this->add($map);
      return returnSuc(L('success'));
    }

    //提现账户列表
    public function bindList($uid=0,$field='a.id,a.account,a.account_type,d.name'){
      $model = $this->getModel();
      $r = $model->alias('a')->join(['common_datatree d',''],'d.id = a.account_type','left')->where(['uid'=>$uid])->field($field)->select();
      return $r;
    }
}