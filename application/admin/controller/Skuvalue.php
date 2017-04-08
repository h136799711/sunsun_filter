<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\admin\controller;

use app\src\goods\logic\SkuvalueLogic;

class Skuvalue extends Admin {

	public function index(){

		$sku_id = $this->_param('sku_id',-1);
		$map = array(
			'sku_id'=>$sku_id
		);
		$params = array(
			'sku_id'=>$sku_id
		);
        $p = $this->_param('p', 0);
		$page = array('curpage' => $p, 'size' => 20);
        $order = " id desc";
		//
		$result = (new SkuvalueLogic())->queryWithPagingHtml($map,$page,$order,$params);

		$this->assign('p',$p);
        $this->assign('sku_id',$sku_id);
        $this->assign('show',$result['info']['show']);
        $this->assign('list',$result['info']['list']);
        return $this->boye_display();
	}
	
	public function add(){
		$sku_id = $this->_param('sku_id',-1);
		
		if(IS_GET){

			$this->assign('sku_id',$sku_id);
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

            $result = (new SkuvalueLogic())->add($entity);
			
			
			if($result['status']){
			    if($dnredirect){
			        $this->success("添加成功");
                }
                $this->success("添加成功！",url('Admin/Skuvalue/index',array('sku_id'=>$sku_id)));
			}
			else{
				$this->error($result['info']);
			}
			
		}
	}

	public function edit(){
		$sku_id = $this->_param('sku_id',-1);
		$id = $this->_param('id','');
		
		if(IS_GET){
			$result = (new SkuvalueLogic())->getInfo(array('id'=>$id));
			if($result['status']){
				$this->assign("vo",$result['info']);
			}
			$this->assign('sku_id',$sku_id);
            return $this->boye_display();
		}else{
			$name = $this->_param('name','');
			
			if(empty($name)){
				$this->error("属性不能为空！");
			}
			
			$entity = array(
				'name'=>$name,
			);
            $result = (new SkuvalueLogic())->saveByID($id,$entity);
			
			
			if($result['status']){
				$this->success("保存成功！",url('Admin/Skuvalue/index',array('sku_id'=>$sku_id)));
			}
			else{
				$this->error($result['info']);
			}
			
		}
	}

	public function delete(){
        $id = $this->_param('id',0);
        $sku_id = $this->_param('sku_id',0);

		$result = (new SkuvalueLogic())->delete(['id'=>$id]);
		if($result['status']){
			$this->success("删除成功！",url('Skuvalue/index',['sku_id'=>$sku_id]));
		}else{
			$this->error($result['info']);
		}
			
	}

	
}
