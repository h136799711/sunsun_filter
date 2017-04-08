<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-16
 * Time: 14:22
 */

namespace app\admin\controller;


use app\src\admin\helper\AdminConfigHelper;
use app\src\admin\helper\MenuHelper;
use app\src\menu\logic\MenuLogic;
use think\Request;

class Menu extends Admin
{


    protected function _initialize() {
        parent::_initialize();
        //
        $pid = $this->_param('pid',0);
        if ($pid != 0) {
            $result = (new MenuLogic())->getInfo(['id' => $pid]);
            if ($result['status']) {
                $this -> assign('parentMenu', $result['info']);
            } else {
                $this -> error(L('UNKNOWN_ERR'));
            }
        }
    }

    /**
     * 菜单
     */
    public function index() {
        $map = array();
        $pid = $this->_param('pid',0);
        $map['pid'] = $pid;

        $page = array('curpage' => $this->_param('p',0), 'size' => AdminConfigHelper::getValue('LIST_ROWS'));
        $order = "sort desc";
        $result = (new MenuLogic())->queryWithPagingHtml($map, $page, $order);

        //2. 不能删除的菜单id
        $cant_delete_menus = '284,164';
        $this->assign("cant_delete",$cant_delete_menus);
        return parent::queryResult($result);
    }

    /**
     * 保存
     */
    public function save($logic=null,$primarykey = 'id', $entity = NULL, $redirect_url = false) {

        $entity = Request::instance()->param();
        $pid = $this->_param('pid',0);
        $id = $this->_param('id',0);

        $entity['pid'] = $pid;

        $redirect_url = url('Admin/Menu/index', ['pid' => $pid]);

        //TODO: 保存到权限规则表中
        $result = (new MenuLogic())->getInfo(['id'=>$id]);
        if($result['status'] && is_array($result['info'])){
            $newEntity = array(
                'title'=>$entity['title'],
                'name'=>$entity['url'],
                'type'=>$entity['pid'],
            );
        }else{
            $this->error("获取数据错误，请重试！");
        }
        $hide = $this->_post('hide','');

        if($hide === ''){
            $entity['hide']=0;
        }


        $result = (new MenuLogic())->saveByID($id, $entity);
        if ($result['status'] === false) {
            $this -> error($result['info']);
        } else {
            $this -> success(L('RESULT_SUCCESS'), $redirect_url);
        }

    }

    public function edit() {
        if (IS_GET) {
            $id = $this->_param('id',0);
            $map = array('id' => $id);
            $result = (new MenuLogic())->getInfo($map);

            if ($result['status'] === false) {
                $this -> error(L('C_GET_NULLDATA'));
            } else {
                $this -> assign("entity", $result['info']);
            }

            $map['id'] = array('neq' , $id);
            $result = (new MenuLogic())->queryNoPaging($map);
            if ($result['status']) {

                $menus = MenuHelper::toFormatTree($result['info']);

                $this -> assign("pid", $this->_param('pid',0));
                $this -> assign("menus", $menus);
                return $this->boye_display();
            } else {
                $this -> error($result['info']);
            }
        }
    }

    /**
     * 增加菜单
     */
    public function add() {
        if (IS_GET) {
            $result = (new MenuLogic())->queryNoPaging();
            if ($result['status']) {
                $menus = MenuHelper::toFormatTree($result['info']);
                $this -> assign("pid", $this->_param('pid',0));
                $this -> assign("menus", $menus);
                return $this->boye_display();
            } else {
                $this -> error($result['info']);
            }
        } else {
            $menu = Request::instance()->param();

            $menu['pid'] = $this->_param('pid','');
            $success_url = url('Admin/Menu/index', array('pid' => $menu['pid'] ));

            $result =  (new MenuLogic())->add($menu);
            if ($result['status'] === false) {
                $this -> error($result['info']);
            } else {
                $this -> success(L('RESULT_SUCCESS'), $success_url);
            }
        }
    }

    /**
     * 删除菜单
     */
    public function delete($success_url = false)
    {
        $pid = $this->_param('id', -1);
        $map = array('pid' => $pid);
        $result = (new MenuLogic())->query($map);
        if ($result['status'] && !is_null($result['info'])) {

            if (count($result['info']['list']) > 0) {
                $this->error(L('ERR_CANT_DEL_HAS_CHILDREN'));
            } else {

                $map = array('id' => $pid);
                //获取菜单信息
                $result = (new MenuLogic())->getInfo($map);

                if ($result['status']) {
                    $entity = $result['info'];
                } else {
                    $this->error($result['info']);
                }

                $result = (new MenuLogic())->delete($map);

                if ($result['status'] === false) {
                    $this->error($result['info']);
                } else {

                    if(empty($success_url)){
                        $success_url = url('Admin/Menu/index');
                    }

                    $this->success(L('RESULT_SUCCESS'), $success_url );
                }

            }
        }
    }
}