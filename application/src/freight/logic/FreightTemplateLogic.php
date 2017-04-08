<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-10
 * Time: 14:22
 */

namespace app\src\freight\logic;


use app\src\base\logic\BaseLogic;
use app\src\extend\Page;
use app\src\freight\model\FreightAddress;
use app\src\freight\model\FreightTemplate;

class FreightTemplateLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new FreightTemplate());
    }
    /**
     * 根据用户ID查询运费模版
     */
    public function queryWidthUID($uid = -1, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false){

        $query = $this -> getModel();
        $map =array('uid' => $uid);
        $list=$this->queryNoPaging($map);
        if(!$list['status']) $this->error('错误');
        foreach($list['info'] as $k=>$v){
            $data=$query->FreightAddress()->where(['template_id'=>$v['id']])->select();
            $data=json_decode(json_encode($data),true);
            $list['info'][$k]['freightAddress']=$data;
        }
        $list=$list['info'];


        if($list === false){
            $error = $this -> getModel() -> getDbError();
            return $this -> apiReturnErr($error);
        }
        $count = $this -> getModel() -> where($map) -> count();
        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));

    }
    /**
     * 添加运费模版
     */
    public function addTemplate($entity){

        $data = [
            "name" => $entity["name"],
            "company" => $entity["company"],
            "uid" => $entity["uid"],
            "type" => $entity["type"]
        ];
        $this->getModel()->save($data);
        $template_add=$this->getModel()->id;

        $freightAddress = $entity["freightAddress"];
        foreach($freightAddress as $k=>$v){
            $freightAddress[$k]['template_id']=$template_add;
        }

        $result = (new FreightAddress())->saveAll($freightAddress);

        if ($result === false) {
            return $this -> apiReturnErr($this -> getModel() -> getDbError());
        }
        return $this -> apiReturnSuc($result);
    }
    /**
     * 更新运费模版
     */
    public function updateTemplate($map,$entity){

        //删除旧模版地址数据

        $m = new FreightAddress();
        $m->startTrans();


        if(isset($map['id'])){
            $result = $m->where(array('template_id'=>$map['id']))->delete();
            if ($result === false) {
                $m->rollback();
                return $this -> apiReturnErr($this -> getModel() -> getDbError());
            }
            $m->commit();
        }

        $model = $this->getModel();
        $model->startTrans();
        $tem_entity=array(
            'company'=>$entity['company'],
            'type'=>$entity['type'],
            'uid'=>$entity['uid'],
            'name'=>$entity['name'],
        );

        $result = $model->save($tem_entity,array('id'=>$map['id']));
        if ($result === false) {
            $model->rollback();
            return $this -> apiReturnErr($this -> getModel()-> getDbError());
        }
        $model->commit();



        $freightAddress = $entity["freightAddress"];
        foreach($freightAddress as $k=>$v){
            $freightAddress[$k]['template_id']=$map['id'];
        }

        $result = (new FreightAddress())->saveAll($freightAddress);



        return $this -> apiReturnSuc($result);

    }
    /**
     * 查询指定运费模版
     */
    public function findTemplate($map){

        $model = $this ->getModel();

        $result = $model->alias('ft')->where($map)->find();
        $result=json_decode(json_encode($result),true);

        $data=$model->FreightAddress()->where(['template_id'=>$result['id']])->select();
        $data=json_decode(json_encode($data),true);
        $result['freightAddress']=$data;
        if ($result === false) {
            return $this -> apiReturnErr($this -> getModel() -> getDbError());
        }
        return $this -> apiReturnSuc($result);
    }


    /**
     * 数据转换,对象转为数组
     */
    public function transform($data){
        $data=json_decode(json_encode($data),true);
        return $data;
    }



}