<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 17:57
 */

namespace app\admin\controller;


use app\src\category\logic\CategoryLogic;
use app\src\goods\logic\ProductGroupLogic;
use app\src\goods\logic\ProductLogic;
use app\src\system\logic\DatatreeLogicV2;

class ProductGroup extends Admin
{

    private $cate;

    public function index(){
        $id = $this->_param('id',0);
        if(IS_GET){

            $select_cate_parent = $this->_param('select_cate_parent','');

            if($select_cate_parent == ''){
                $select_cate_parent = 0;
            }
            $this -> assign('select_cate_parent',$select_cate_parent);

            $this -> cate = array();
            if($select_cate_parent != 0){
                $this->queryChild($select_cate_parent);
            }

            $store_id = $this->_param('store_id',0);
            $page_no = $this->_param('p',1);
            $this->assign("store_id",$store_id);

            $this -> assign('id',$id);

            $parent = getDatatree('WXPRODUCTGROUP');

            $result = (new DatatreeLogicV2())->getInfo(['id' => $id]);

            if(!empty($result)){
                if($result['parentid'] != $parent){
                    $this -> error("分类id参数错误！");
                }
                $this -> assign('name',$result['name']);
            }else{
                $this -> error($result);
            }
            //查询八个馆
            $result = (new CategoryLogic())->queryNoPaging(['level'=>1,'parent'=>0]);
            if($result['status']){
                $this -> assign('cate_parent',$result['info']);
            }else{
                $this -> error($result['info']);
            }

            //查询该分类商品信息
            $map = array(
                'g.g_id' => $id,
                'p.status' => 1,
                'p.onshelf' => 1,
            );
            if($select_cate_parent != 0){
                $map['p.cate_id'] = array('in',$this->cate);
            }
            $page = array('curpage'=>$page_no,'size'=>10);
            $field = "g.id,g.g_id,g.start_time,g.end_time,g.display_order";
            $field = $field.",p.id as p_id,p.name";
            $order = "display_order desc";
            $result = (new ProductGroupLogic())->queryWithCount($map,$page,$order,false,$field);
            if(!$result['status']){
                $this->error($result['info']);
            }

            $this->assign("group",$result['info']['list']);

            $this -> assign("group_show",$result['info']['show']);

            return $this->boye_display();
        }
    }

    public function getProduct(){

        $map = array();
        $page_size = 10;

        $select_cate_parent = $this->_param('select_cate_parent',"");
        $p = $this->_param('p',1);
        if($p <= 0){
            $p = 1;
        }
        if($select_cate_parent == ""){
            $select_cate_parent = 0;
        }
        $this -> cate = array();
        if($select_cate_parent !== 0){
            $this -> queryChild($select_cate_parent);
            $map['cate_id'] = array('in',$this -> cate);
        }

        $page = array('curpage'=>$p,'size'=>$page_size);
        $result = (new ProductLogic())->queryOnShelf($map,$page);
        if($result['status']){
            foreach($result['info']['list'] as &$vo){
                $map = array(
                    'p_id' => $vo['id'],
                    'start_time' => array('elt',time()),
                    'end_time' => array('egt',time())
                );
                $res = (new ProductGroupLogic())->getInfo($map);
                if($res['status']){
                    if($res['info'] == ""){
                        $vo['able'] = 1;
                    }else{
                        $vo['able'] = 0;
                    }
                }else{
                    $this -> error($res['info']);
                }
            }
            $result['info']['p'] = $p;
            $result['info']['page'] = ceil($result['info']['count']/$page_size);
            echo json_encode($result);
        }else{
            $this -> error($result['info']);
        }

    }

    /**
     * 废弃
     * @param $group
     * @return mixed
     */
    private function getProductName($group){
        $arr = $group;
        foreach($group as $k=>$vo){
            $id = $vo['p_id'];
            $result = (new ProductLogic())->getInfo(['id' => $id]);
            if(!$result['status']){
                $this -> error($result['info']);
            }

            $arr[$k]['p_name'] = $result['info']['name'];
        }
        return $arr;
    }

    public function edit(){
        if(IS_POST){
            $p_id = $this->_param('p_id',0);
            $g_id = $this->_param('g_id',0);
            $price = $this->_param('price_'.$p_id,0,'floatval');
            $start_time = $this->_param('start_time_'.$p_id,'');
            $start_time = toUnixTimestamp($start_time);
            $end_time = $this->_param('end_time_'.$p_id,'');
            $end_time = toUnixTimestamp($end_time);
            $display_order = $this->_param('display_order_'.$p_id,0,'int');
            $entity = array(
                'price' => $price,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'display_order' => $display_order
            );

            $map = array(
                'p_id' => $p_id,
                'g_id' => $g_id,
            );
            $result = (new ProductGroupLogic())->save($map,$entity);

            if($result['status']){
                $this -> success('修改成功！',url('Admin/ProductGroup/index',array('id'=>$g_id)));
            }else{
                $this -> error($result['info']);
            }

        }
    }

    public function delete(){
        $id = $this->_param('id',0);
        $result = (new ProductGroupLogic())->delete(['id'=>$id]);
        if($result['status']){
            $this -> success('删除成功！');
        }else{
            $this -> error($result['info']);
        }

    }

    public function add(){
        if(IS_POST){
            $g_id = $this->_param('g_id',0);
            $p_id = $this->_param('p_id',0);
            $entity = array(
                'p_id' => $p_id,
                'g_id' => $g_id,
            );
            $start_time = $this->_param('new_start_time','');
            $end_time = $this->_param('new_end_time','');
            $entity['start_time']=toUnixTimestamp($start_time);
            $entity['end_time']=toUnixTimestamp($end_time);
            $entity['price']= $this->_param('new_price',0,'floatval');
            $entity['display_order'] =  $this->_param('new_display_order',0,'int');
            $result = (new ProductGroupLogic())->add($entity);
            if(!$result['status']){
                $this->error($result['info']);
            }

            $this->success('操作成功！',url('Admin/ProductGroup/index',array('id'=>$g_id)));
        }
    }

    /**
     * 循环版本
     * @param $id
     * @return array
     */
    private function queryChild($id){
        $que = array();
        array_push($que,$id);
        while(count($que)!=0){
            $tmp = array_shift($que);
            $map = array(
                'parent' => $tmp,
            );
            $result = (new CategoryLogic())->queryNoPaging($map);

            if($result['status']){
                if(is_array($result['info'])){
                    foreach($result['info'] as $val){
                        array_push($que,$val['id']);
                    }
                }else{
                    array_push($this -> cate , $tmp );
                }
            }else{
                return array('status' => false,'info' => $result['info']);
            }
        }
        return array('status' => true,'info' => $this -> cate);
    }

}