<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\admin\controller;

use app\src\goods\logic\SkuvalueLogic;
use app\src\goods\model\Skuvalue;
use think\Log;
class CategorySkuvalue extends Admin{
	
	protected $level;
	protected $parent;
	protected $preparent;
	
	protected function _initialize(){
		parent::_initialize();
		$this->level = $this->_param('level',0);
		$this->parent = $this->_param('parent',0);
		$this->preparent = $this->_param('preparent',0);
		
		$this->assign("level",$this->level );
		$this->assign("parent",$this->parent );
		$this->assign("preparent",$this->preparent );
	}
	
	public function index(){
		
		$cate_id = $this->_param('cate_id',-1);
		$sku_id = $this->_param('sku_id',-1);
		$map = array(
//			'cate_id'=>$cate_id,
			'sku_id'=>$sku_id
		);
		$name = $this->_param('name','');
		$params = array(
//			'cate_id'=>$cate_id,
			'sku_id'=>$sku_id
		);

		$page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
		
		$order = "id asc ";
		//
		$result = (new SkuvalueLogic())->queryWithPagingHtml($map,$page,$order,$params);
		//
		if($result['status']){
			$this->assign('sku_id',$sku_id);
			$this->assign('cate_id',$cate_id);
			$this->assign('show',$result['info']['show']);
			$this->assign('list',$result['info']['list']);
			return $this->boye_display();
		}else{
			Log::record('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
			$this->error(L('UNKNOWN_ERR'));
		}
	}
	
	public function add(){
		$cate_id = $this->_param('cate_id',-1);
		$sku_id = $this->_param('sku_id',-1);
		
		if(IS_GET){
			
			$this->assign('sku_id',$sku_id);
			$this->assign('cate_id',$cate_id);
            return $this->boye_display();
		}else{
			
			$name = $this->_param('name','');
			$dnredirect = $this->_param('dnredirect',false);
			
			if(empty($name)){
				$this->error("属性不能为空！");
			}
			
			$entity = array(
				'name'=>$name,
				'sku_id'=>$sku_id
			);
			
//			$result = apiCall(SkuvalueApi::ADD,array($entity));
			$result = (new SkuvalueLogic())->add($entity);

			if($result['status']){
				if($dnredirect){
					$this->success("添加成功！");					
				}else{
					$this->success("添加成功！",url('Admin/CategorySkuvalue/index',array('sku_id'=>$sku_id,'cate_id'=>$cate_id,'preparent'=>$this->preparent,'parent'=>$this->parent,'level'=>$this->level)));
				}
			}
			else{
				$this->error($result['info']);
			}
			
		}
	}

	public function edit(){
		$cate_id = $this->_param('cate_id',-1);
		$sku_id = $this->_param('sku_id',-1);
		$id = $this->_param('id','');
		
		if(IS_GET){
//			$result = apiCall(SkuvalueApi::GET_INFO,array(array('id'=>$id)));
			$result = (new SkuvalueLogic())->getInfo(['id'=>$id]);
			if($result['status']){
				$this->assign("vo",$result['info']);
			}
			$this->assign('sku_id',$sku_id);
			$this->assign('cate_id',$cate_id);
            return $this->boye_display();
		}else{
			$name = $this->_param('name','');
			
			if(empty($name)){
				$this->error("属性不能为空！");
			}
			
			$entity = array(
				'name'=>$name,
			);
			
//			$result = apiCall(SkuvalueApi::SAVE_BY_ID,array($id,$entity));
			$result = (new SkuvalueLogic())->saveByID($id,$entity);

			
			if($result['status']){
				$this->success("保存成功！",url('Admin/CategorySkuvalue/index',array('sku_id'=>$sku_id,'cate_id'=>$cate_id,'preparent'=>$this->preparent,'parent'=>$this->parent,'level'=>$this->level)));
			}
			else{
				$this->error($result['info']);
			}
			
		}
	}

	public function delete(){
		
		$id = $this->_param('id',0);
		
//		$result = apiCall(SkuvalueApi::DELETE,array(array('id'=>$id)));
		$result = (new SkuvalueLogic())->delete(['id'=>$id]);
		if($result['status']){
			$this->success("删除成功！");
		}else{
			$this->error($result['info']);
		}
			
	}

	
}
