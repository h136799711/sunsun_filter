<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-10
 * Time: 19:42
 */

namespace app\admin\controller;


use app\src\category\action\CategorySkuDelRelationAction;
use app\src\goods\logic\SkuLogic;
use app\src\sku\action\SkuDeleteAction;

class Sku extends Admin
{
    public function index(){

        $name = $this->_param('name','');
        $p = $this->_param('p',0);

        $page = array('curpage' => $p, 'size' => config('LIST_ROWS'));
        $params = [];
        if(!empty($name)){
            $params['name'] = $name;
        }

        $order = " id asc ";
        $map = ['name'=>['like','%'.$name.'%']];
        $result = (new SkuLogic())->queryWithPagingHtml($map,$page,$order,$params);

        if($result['status']){
            $this->assign('p',$p);
            $this->assign('name',$name);
            $this->assign('show',$result['info']['show']);
            $this->assign('list',$result['info']['list']);
            return $this->boye_display();
        }else{
            $this->error(L('UNKNOWN_ERR'));
        }
    }

    public function add(){
        if(IS_GET){
            return $this->boye_display();
        }else{
            $name = $this->_param('name','');

            if(empty($name)){
                $this->error("名称不能为空！");
            }

            $entity = array(
                'name'=>$name,
            );

            $result = (new SkuLogic())->add($entity);

            if($result['status']){
                $this->success("添加成功！",url('Admin/Sku/index'));
            }
            else{
                $this->error($result['info']);
            }

        }
    }

    public function edit(){

        $id = $this->_param('id','');

        if(IS_GET){
            $result = (new SkuLogic())->getInfo(['id'=>$id]);

            if($result['status']){
                $this->assign("vo",$result['info']);
            }
            return $this->boye_display();
        }else{
            $name = $this->_param('name','');

            if(empty($name)){
                $this->error("规格名称不能为空！");
            }

            $entity = array(
                'name'=>$name,
            );

            $result = (new SkuLogic())->saveByID($id,$entity);


            if($result['status']){
                $this->success("保存成功！",url('Admin/Sku/index'));
            }
            else{
                $this->error($result['info']);
            }

        }
    }

    public function delete(){

        $id = $this->_param('id',0);

        $result = (new SkuDeleteAction())->delete($id);

        if($result['status']){
            $this->success("删除成功！");
        }else{
            $this->error($result['info']);
        }
    }

    public function deleteCateRelation(){

        $id = $this->_param('id',0);
        $result = (new CategorySkuDelRelationAction())->delRelation($id);
        if($result['status']){
            $this->success("清理成功");
        }
        $this->error('清理失败');
    }



}