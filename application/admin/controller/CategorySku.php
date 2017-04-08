<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\admin\controller;

use app\src\category\logic\CategoryLogic;
use app\src\category\logic\CategorySkuLogic;
use app\src\goods\logic\SkuLogic;
use app\src\goods\logic\SkuvalueLogic;
use think\Exception;
use think\Log;

class CategorySku extends Admin{

    protected $cate_id;
    protected $parent;

    protected function _initialize(){
        parent::_initialize();
        $this->cate_id = $this->_param('cate_id',0);
        $this->parent = $this->_param('parent',0);
        $this->assign("cate_id",$this->cate_id );
        $this->assign("parent",$this->parent );
    }

    public function index(){

        $map = array(
            'cs.cate_id'=>$this->cate_id
        );

        $params = array(
            'cate_id'=>$this->cate_id
        );
        $p = $this->_param("p",0);
        $page = array('curpage' => $p, 'size' => config('LIST_ROWS'));

        $result = (new CategoryLogic())->getInfo(array("id"=>$this->cate_id));

        if(!$result['status']){
            $this->error($result['info']);
        }

        $cate_vo = $result['info'];
        //
        $result = (new CategorySkuLogic())->queryJoin($this->cate_id,$page,$params);

        $this->assign('cate_id',$this->cate_id);
        $this->assign('cate_vo',$cate_vo);
        $this->assign('show',$result['info']['show']);
        $this->assign('list',$result['info']['list']);
        return $this->boye_display();
    }

    public function bulkDelete(){
        $ids = $this->_post('ids/a',[]);
        if(empty($ids)) $this->error("请先选择要操作的项");

        $result = (new CategorySkuLogic())->delete(['id'=>['in',$ids]]);

        $this->success("操作成功");
    }

    public function bulkAdd(){
        $ids = $this->_post('ids/a',[]);
        if(empty($ids)) $this->error("请先选择要操作的项");
        if(count($ids) >= 10){
            $this->error("由于系统限制一个类目只能关联10个规格");
        }
        $result = (new CategorySkuLogic())->count(['cate_id'=>$this->cate_id]);
        if($result['status']){
            $count = intval($result['info']);
            if($count >= 10){
                $this->error("由于系统限制一个类目只能关联10个规格");
            }
        }

        $allEntity = [];
        foreach ($ids as $id){
            array_push($allEntity,['cate_id'=>$this->cate_id,'sku_id'=>$id]);
        }
        try{
            $result = (new CategorySkuLogic())->addAll($allEntity);
            if(!$result['status']){
                $this->error($result['info']);
            }
            $this->success("操作成功");
        }catch (Exception $exception){
            $this->error($exception->getMessage());
        }
    }

    public function add(){
        $result = (new CategoryLogic())->getInfo(["id"=>$this->cate_id]);

        if(!$result['status']){
            $this->error($result['info']);
        }

        $cate_vo = $result['info'];
        $params = array(
            'cate_id'=>$this->cate_id
        );
        $page = array('curpage' => $this->_param('p', 0), 'size' => 30);
        //
        $result = (new CategorySkuLogic())->queryRightJoin($this->cate_id,$page,$params);

        $this->assign('cate_vo',$cate_vo);
        $this->assign('list',$result['info']['list']);
        $this->assign('show',$result['info']['show']);
        return $this->boye_display();
    }


    public function delete(){
        $id = $this->_param('id',0);
        if($id <= 0) $this->error("缺失id");
        $result = (new CategorySkuLogic())->delete(['id'=>$id]);
        if($result['status']){
            $this->success('删除成功');
        }
    }
}
