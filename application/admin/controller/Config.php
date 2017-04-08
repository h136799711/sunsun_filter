<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

namespace app\admin\controller;

use app\src\admin\helper\AdminConfigHelper;
use app\src\system\logic\ConfigLogic;
use think\Cache;
use think\Request;
use think\View;

class Config  extends Admin {

    public function _initialize() {
        parent::_initialize();
        $this -> assignTitle(L('C_CONFIG'));
    }

    /**
     * 配置
     */
    public function index() {

        $map = array();
        $param = [];
        $name = $this->_param('name', '');
        if(!empty($name)){
            $map['name'] = array('like', '%' . $name . '%');
            $param['name'] = $name;
        }
        $group = $this->_param('group',-1);
        if($group !== -1){
            $map['group'] = $group;
            $param['group'] = $group;
        }

        $page = array('curpage' => $this->_param('p', 0), 'size' => AdminConfigHelper::getValue('LIST_ROWS'));
        $order = 'update_time desc';
        $result =  (new ConfigLogic())->queryWithPagingHtml($map, $page, $order,$param);
        if ($result['status']) {
            $this -> assign("config_groups", AdminConfigHelper::getValue('CONFIG_GROUP_LIST'));
            $this -> assign("show", $result['info']['show']);
            $this -> assign("list", $result['info']['list']);
            return $this -> boye_display();
        } else {
            $this -> error($result['info']);
        }
    }

    private function setConfigView($group){
        $map = array('group'=>$group);
        $result = (new ConfigLogic())->queryNoPaging($map);
        if($result['status']){
            return View::instance()->fetch("default/Widget/config_set",['group'=>$group,'list'=>$result['info']]);
        }
        return "-0-";

    }

    /**
     * 设置
     */
    public function set(){
        if(IS_GET){
            $this->configVars();



            return $this->boye_display();
        }else{
            $config = $this->_post('config/a',[]);
            $order = 'sort desc';
            $result = (new ConfigLogic())->set($config);

            if($result['status']){
                //清除缓存
                Cache::set("config_" . session_id() . '_' . UID ,null);
                $this->success(L('RESULT_SUCCESS'),url('Admin/Config/set'));
            }else{
                $this -> error($result['info']);
            }
        }
    }

    /**
     * 更新保存，根据主键默认id
     * 示列url:
     * /Admin/Menu/save/id/33
     * id必须以get方式传入
     * @param string $primarykey
     * @param null $entity
     * @param bool $redirect_url
     */
    public function save($logic=null, $primarykey = 'id', $entity = NULL, $redirect_url = false){
        parent::save(new ConfigLogic(),$primarykey , $entity, $redirect_url );
    }

    /**
     * 添加
     */
    public function add() {
        if (IS_GET) {
            $this->configVars();
            return $this -> boye_display();
        } else {
            $menu = Request::instance()->param();
            $result = (new ConfigLogic())->add($menu);

            if($result['status']){
                $this->success("操作成功",url('Admin/Config/index'));
            }

            $this->error('操作失败',url('Admin/Config/index'));
        }
    }

    public function edit() {
        $this->configVars();
        $map = array('id' => $this->_param('id',0));
        $result = (new ConfigLogic())->getInfo($map);

        if ($result['status'] === false) {
            $this -> error(L('C_GET_NULLDATA'));
        } else {
            $this -> assign("entity", $result['info']);
            return $this->boye_display();
        }
    }

    public function delete(){
        $this->_delete(new ConfigLogic());
    }



    /**
     * 配置分组与类型参数
     */
    protected function configVars() {
        //配置分组
        $config_groups = AdminConfigHelper::getValue('CONFIG_GROUP_LIST');
        //配置类型
        $config_types = AdminConfigHelper::getValue('CONFIG_TYPE_LIST');
        $config_view = [];
        foreach ($config_groups as $key=>&$item) {
            $config_view[$key]  = $this->setConfigView($key);
        }

        $this -> assign('config_view', $config_view);
        $this -> assign('config_groups', $config_groups);
        $this -> assign('config_types', $config_types);
    }

}
