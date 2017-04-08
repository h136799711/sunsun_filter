<?php
/**
 * Author      : rainbow <hzboye010@163>
 * DateTime    : 2016-12-28 17:33:30
 * Description : [Description]
 */

namespace app\src\wallet\logic;

use app\src\base\logic\BaseLogicV2;
use app\src\wallet\model\WalletOrder;
// use app\src\system\logic\DatatreeLogicV2;
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
class WalletOrderLogicV2 extends BaseLogicV2 {
    const TOBE_PAY = 0;
    const PAYED    = 1;
    //初始化
    protected function _init(){
        $this->setModel(new WalletOrder());
    }

    /**
     * 业务 - 提现账号添加
     * @Author
     * @DateTime 2016-12-29T09:17:51+0800
     * @param    array                    $params [description]
     * @return   [apiReturn]                      [description]
     */
}