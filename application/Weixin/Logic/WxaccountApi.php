<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------



namespace app\weixin\Api;
use app\weixin\Api\Api;
use app\weixin\Model\WxaccountModel;

class WxaccountApi extends Api{

    const QUERY = "Weixin/Wxaccount/query";

    const GET_INFO = "Weixin/Wxaccount/getInfo";

    const ADD = "Weixin/Wxaccount/add";

    const SAVE_BY_ID = "Weixin/Wxaccount/saveByID";
	
	protected function _init(){
		$this->model = new WxaccountModel();
	}
	
}
