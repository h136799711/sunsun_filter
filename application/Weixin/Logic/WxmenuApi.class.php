<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Weixin\Api;

use Weixin\Model\WxmenuModel;

class WxmenuApi extends \Common\Api\Api{

    const QUERY_NO_PAGING = "Weixin/Wxmenu/queryNoPaging";

    const QUERY = "Weixin/Wxmenu/query";

    const ADD = "Weixin/Wxmenu/add";

    const GET_INFO = "Weixin/Wxmenu/getInfo";

    const SAVE_BY_ID = "Weixin/Wxmenu/saveByID";

	protected function _init(){
		$this->model = new WxmenuModel();
	}
	
	/**
	 * 获取家族关系
	 */
	public function getInfoWithFamily($id){
		$result = $this->model->alias(" wu ")->field("wu.nickname,wu.referrer,wu.id as wxuserid,wu.openid,wu.wxaccount_id,wf.parent_1,wf.parent_2,wf.parent_3,wf.parent_4,wf.parent_5")->join("LEFT JOIN __WXUSER_FAMILY__ as wf on wu.openid = wf.openid and wu.wxaccount_id = wf.wxaccount_id")->where(array('wu.id'=>$id))->find();
	
		if($result === false){
			$error = $this->model->getDbError();
			return $this -> apiReturnErr($error);
		}else{
			return $this->apiReturnSuc($result);
		}
	}
}
