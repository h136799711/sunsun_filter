<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

namespace  app\admin\controller;


use app\src\system\logic\DatatreeLogicV2;
use app\src\tool\helper\RadixHelper;
use think\Db;

class Datatree extends Admin {

	private $parent;
	private $preparent;

	protected function _initialize(){
		parent::_initialize();
		$this->parent = $this->_param('parent',0);
		$result = (new DatatreeLogicV2())->getInfo(['id'=>$this->parent]);

		if(is_array($result)){
			$this->preparent = $result['parentid'];
		}

		$this->assign('parent',$this->parent);
		$this->assign('preparent',$this->preparent);
	}

	public function index(){
		$name = $this->_param('name','');
		$map = array('parentid'=>$this->parent);

		$params = array('parent'=>$this->parent);

		if(!empty($name)){
			$map['name'] = array('like',"%$name%");
			$params['name'] = $name;
		}

		$page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));

		$order = " sort desc,code desc ";

        $result = (new DatatreeLogicV2())->queryWithPagingHtml($map,$page,$order,$params);

        $this->assign('name',$name);
        $this->assign('show',$result ['show']);
        $this->assign('list',$result ['list']);
        return $this->boye_display();

	}


	public function search(){

		$name = $this->_param('name','');
		$map = array();
        $params = array('parent'=>$this->parent);

		if(!empty($name)){
			$map['name'] = array('like',"%$name%");
			$params['name'] = $name;
		}

		$page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));

		$order = " sort desc ";

		$result = (new DatatreeLogicV2())->queryWithPagingHtml($map,$page,$order,$params);


        $this->assign('name',$name);
        $this->assign('show',$result['show']);
        $this->assign('list',$result['list']);
        return $this->boye_display();

	}

	private function getDatatreeNextCode(){
        $parent = (new DatatreeLogicV2())->getInfo(['id'=>$this->parent]);

        $result = (new DatatreeLogicV2())->getInfo(['code'=>['like',$parent['code'].'___']],'code desc');

        $code = $parent['code'].'001';
        if(empty($result)){
            return $code;
        }

        $parent_code = $result['code'];
        if(strlen($parent_code) < 3){ $this->error('父级编码错误('.$parent_code.'0');}
        $hex36 = substr($parent_code,strlen($parent_code) - 3,3);
        $num   = RadixHelper::hex36ToNum($hex36) + 1;
//        $num   = 35*36*36 +  35*36 + 35;//46655 同一层级最多数据

        $hex36 = RadixHelper::numTo36Hex($num);


        if(strlen($hex36) < 3){
            $hex36 = str_pad($hex36,3,"0",STR_PAD_LEFT);
        }

        $code = substr($parent_code,0,strlen($parent_code) - 3).$hex36;
        return $code;
    }

	public function add(){
        $result = (new DatatreeLogicV2())->getInfo(['id'=>$this->parent]);
		if(IS_GET){
            $code = $this->getDatatreeNextCode();
            $this->assign('code',$code);
			return $this->boye_display();
		}else{
			$level = 0;
			$parents = $this->parent.',';
			$level = intval($result['level'])+1;
            $parents = $result['parents'].$parents;
            $entity = array(
                'code'     =>$this->_param('code',''),
                'alias'     =>$this->_param('alias',''),
				'name'     =>$this->_param('name',''),
				'notes'    =>$this->_param('notes',''),
				'sort'     =>$this->_param('sort',''),
				'level'    =>$level,
				'parents'  =>$parents,
				'parentid' =>$this->parent,
				'iconurl'  =>$this->_param('iconurl',''),
                'data_level'=>$this->_param('data_level',0)
			);

			$result = (new DatatreeLogicV2())->add($entity);

			$this->success("操作成功！",url('Admin/Datatree/index',array('parent'=>$this->parent)));
		}
	}

	public function delete(){
		$id = $this->_param('id',0);

		$result = (new DatatreeLogicV2())->queryNoPaging(['parentid'=>$id]);

		if(is_array($result ) && count($result ) > 0){
			$this->error("有子级，请先删除所有子级！");
		}
        $result = (new DatatreeLogicV2())->getInfo(['id'=>$id]);

        if(isset($result['data_level'])){
            $data_level = $result['data_level'];
            if($data_level == 1){
                $this->error("系统级数据无法删除！");
            }

            $result = (new DatatreeLogicV2())->delete(['id'=>$id]);
            $this->success("操作成功！");
        }

		$this->error("操作失败！");
	}

	public function edit(){
		$id = $this->_param('id',0);
		if(IS_GET){

			$result = (new DatatreeLogicV2())->getInfo(['id'=>$id]);
            $len = ($result['level']) * 3;
			if(empty($result['code']) || $len != strlen($result['code'])){
                $code = $this->getDatatreeNextCode();
                $result['code'] = $code;
            }

			$this->assign("vo",$result);
			return $this->boye_display();
		}else{

			$entity = array(
                'code'=>$this->_param('code',''),
                'alias' =>$this->_param('alias',''),
				'name'=>$this->_param('name',''),
				'notes'=>$this->_param('notes',''),
				'sort'=>$this->_param('sort',''),
				'iconurl'=>$this->_param('iconurl',''),
                'data_level'=>$this->_param('data_level',0)
			);
			$result = (new DatatreeLogicV2())->saveByID($id,$entity);
			$this->success("操作成功！",url('Admin/Datatree/index',array('parent'=>$this->parent)));
		}
	}

	public function deleteChildren(){

        $id = $this->_param('id',0);
        $result = (new DatatreeLogicV2())->delete(['parentid'=>$id]);
        $this->success("操作成功！",url('Admin/Datatree/index',array('parent'=>$this->parent)));
    }
}

