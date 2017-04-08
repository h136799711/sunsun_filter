<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace app\admin\controller;

use app\src\category\logic\CategoryPropLogic;
use app\src\category\logic\CategoryPropvalueLogic;
use think\Log;
class CategoryProp extends Admin{
		
	public function index(){
		$cate_id = $this->_param('cate_id',-1);
        $map = array(
            'cate_id'=>$cate_id,
        );
        $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));
        $order = "id asc";
        $result = (new CategoryPropLogic())->queryWithPagingHtml($map,$page,$order,false);

        if($result['status']){
            $this->assign('cate_id',$cate_id);
            $this->assign('list',$result['info']['list']);
            $this->assign('show',$result['info']['show']);
        }else{
            Log::record('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
            $this->error('查询失败！');
        }
        return $this->boye_display();
	}

	public function add(){
		$cate_id = $this->_param('cate_id',-1);
		if(IS_GET){
			$this->assign('cate_id',$cate_id);
			return $this->boye_display();
		}else{
            $cate_id = $this->_param('cate_id',-1);
            $pname = $this->_param('propname');
            $entity = [
                'cate_id'=>$cate_id,
                'propname'=>$pname,
            ];
			$result = (new CategoryPropLogic())->add($entity);

			if($result['status']){
				$this->success("添加成功！",url('Admin/CategoryProp/index',array('cate_id'=>$cate_id)));
			}
			else{
				$this->error($result['info']);
			}
		}
	}
	
	public function edit(){
		if(IS_GET){
            $cate_id = $this->_param('cate_id',-1);
            $id = $this->_param('prop_id','');
			$result = (new CategoryPropLogic())->queryNoPaging(['id'=>$id,'cate_id'=>$cate_id]);
            $this->assign('cate_id',$cate_id);
			$this->assign('id',$id);
			$this->assign('info',$result['info'][0]);
			return $this->boye_display();
		}else{
            $cate_id = $this->_param('cate_id',-1);
            $id = $this->_param('id','');
            $propname = $this->_param('propname','');
		    $map=[
                'id'=>$id,
                'cate_id'=>$cate_id
            ];
            $entity['propname']=$propname;
			$result = (new CategoryPropLogic())->save($map,$entity);
			if($result['status']){
				$this->success("保存成功！",url('Admin/CategoryProp/index',array('cate_id'=>$cate_id)));
			}
			else{
				$this->error($result['info']);
			}
		}
	}
	public function delete(){
		$id = $this->_param('prop_id','');
		$map = array('id'=>$id);
		$result = (new CategoryPropvalueLogic())->queryNoPaging(['prop_id'=>$id]);
		if($result['status']){
			if(count($result['info']) > 0){
				$this->error("存在属性值，请先删除属性值！");				
			}
			$result = (new CategoryPropLogic())->delete($map);
			if($result['status']){
				$this->success("删除成功！");
			}else{
				$this->error($result['info']);
			}
		}else{
            $this->error('属性值查询失败！');
		}
	}
}

