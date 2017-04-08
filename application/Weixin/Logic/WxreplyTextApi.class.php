<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Weixin\Api;

use Weixin\Model\WxreplyTextModel;

class WxreplyTextApi extends \Common\Api\Api{

    const QUERY = "Weixin/WxreplyText/query";

    const ADD = "Weixin/WxreplyText/add";

    const GET_INFO = "Weixin/WxreplyText/getInfo";

    const SAVE_BY_ID = "Weixin/WxreplyText/saveByID";

    const GET_KEYWORDS = "Weixin/WxreplyText/getKeywords";


	protected function _init(){
		$this->model = new WxreplyTextModel();	
	}
	
}
