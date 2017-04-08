<?php
/**
 * Created by PhpStorm.
 * User: Gooraye
 * Date: 2017-02-23
 * Time: 11:39
 */
namespace app\admin\controller;

use app\src\file\logic\UserPictureLogic;
use app\src\system\logic\DatatreeLogicV2;
class Material extends Admin
{

    public $pcode='00Q%';
    public function avatar(){


        $map['code'] = array('like',$this->pcode);
        $result = (new DatatreeLogicV2())->queryNoPaging($map,"","id,name,code");

        $this -> assign('avatar_type',$result);

        $type = $this->_param('type',0);

        $type=empty($type)?$this->pcode:$type;

        $this -> assign('type',$type);
        if($type){
            $map['code']= array('like',$type.'%');
            $params['type'] = $type;
            $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
            $order = " create_time desc ";
            $result = (new UserPictureLogic())->queryWithType($map, $page, $order,$params);

        }else{
            $result = array('status'=>true,'info'=>array('show'=>'','list'=>''));
        }


        $this -> assign('show', $result['info']['show']);
        $this -> assign('list', $result['info']['list']);
        return $this -> boye_display();
    }

    public function add(){
        if(IS_GET){
            $map['code'] = array('like',$this->pcode);
            $type = (new DatatreeLogicV2())->queryNoPaging($map,"","id,name,code");
            unset($type[0]);
            $this->assign('avatar_type',$type);
            return $this -> boye_display();
        }else{
            $type = $this->_param('type','');
            $ori_name = $this->_param('ori_name','');
            $porn_prop  = $this->_param('porn_prop',0);
            $type=empty($type)?$this->pcode:$type;
            $porn_prop=empty($porn_prop)?$this->pcode:$porn_prop;
            //$url =

            $id = $this->_param('id',0);
            $entity = array(
                'type'=>$type,
                'porn_prop'=>$porn_prop,
                'ori_name'=>$ori_name
            );
            $result = (new UserPictureLogic())->saveByID($id,$entity);

            if(!$result['status']){
                $this->error($result['info']);
            }
            $this->success("保存成功！",url('Admin/Material/avatar'));

        }
    }


    public function edit(){
        $id = $this->_param('id',0);
        if(IS_GET){
            $map['code'] = array('like',$this->pcode);
            $type = (new DatatreeLogicV2())->queryNoPaging($map,"","id,name,code");
            unset($type[0]);
            $this->assign('avatar_type',$type);
            $result = (new UserPictureLogic())->getInfo(['id'=>$id]);
            if(!$result['status']){
                $this->error($result['info']);
            }

            $this->assign("info",$result['info']);
           // dump($result['info']);
            return $this -> boye_display();
        }else{
            $type = $this->_param('type','');
            $ori_name = $this->_param('ori_name','');
            $porn_prop  = $this->_param('porn_prop',0);
            $type=empty($type)?$this->pcode:$type;
            //$url =


            $entity = array(
                'type'=>$type,
                'porn_prop'=>$porn_prop,
                'ori_name'=>$ori_name
            );
            $result = (new UserPictureLogic())->saveByID($id,$entity);

            if(!$result['status']){
                $this->error($result['info']);
            }
            $this->success("保存成功！",url('Admin/Material/avatar'));
        }
    }

    public function delete(){
        $id = $this->_param('id',0);
        $result = (new UserPictureLogic())->delete(array('id'=>$id));

        if(!$result['status']){
            $this->error($result['info']);
        }

        $this->success("删除成功！",url('Admin/Material/avatar'));

    }

    //批量删除
    public function delete_all(){
        $tids   = $this->_param('ids','');
        if($tids == '' || count($tids) == 0){
            $this -> success('操作成功');
        }
        $result = (new UserPictureLogic())->delete(['id'=>array('in',$tids)]);

        if($result['status']){
            $this -> success("操作成功");
        }else{
            $this -> error($result['info']);
        }

    }
}