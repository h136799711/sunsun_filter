<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------



namespace app\weixin\Logic;

use Weixin\Model\WxaccountModel;
use app\weixin\Logic\ApiLogic;
class WxaccountLogic extends ApiLogic{

    const QUERY = "Weixin/WxaccountLogic/query";

    const GET_INFO = "Weixin/WxaccountLogic/getInfo";

    const ADD = "Weixin/WxaccountLogci/add";

    const SAVE_BY_ID = "Weixin/WxaccountLogic/saveByID";
	
	protected function _init(){
		$this->model = new WxaccountModel();
	}
	
}
