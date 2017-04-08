<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-13
 * Time: 15:17
 */

namespace app\admin\controller;

use app\src\admin\controller\CheckLoginController;
use app\src\admin\helper\AdminConfigHelper;
use app\src\admin\helper\AdminFunctionHelper;
use app\src\admin\helper\AdminSessionHelper;
use app\src\admin\helper\MenuHelper;
use app\src\base\helper\ResultHelper;
use app\src\base\logic\BaseLogic;
use app\src\powersystem\logic\AuthGroupAccessLogic;
use app\src\powersystem\logic\AuthGroupLogic;
use think\Request;

class Admin extends CheckLoginController
{

    protected $isMobile;

    protected function _initialize()
    {
        parent::_initialize();
        $this->initConfig();
        $this->setActiveMenu();
        $this->initUserMenu();
        $this->initGlobalPageValue();
    }

    //==后台初始化开始

    private function initConfig(){
        AdminConfigHelper::init();
    }

    /**
     * 初始化页面全局变量
     */
    private function initGlobalPageValue(){
//        vendor("mobiledetect.mobiledetectlib.namespaced.Detection.MobileDetect");
        $this->isMobile = Request::instance()->isMobile();//(new \Detection\MobileDetect())->isMobile();

//        if(!IS_AJAX){

//            $url = $this->_param('ret_url','');
//
//            if(empty($url) && isset($_SERVER['HTTP_REFERER'])){
//                $url = $_SERVER['HTTP_REFERER'];
//            }
//
//            if(isset($_SERVER['PHP_SELF']) && $url != $_SERVER['PHP_SELF']){
//                if(strpos($url,$_SERVER['PHP_SELF']) === false){
//                    $url = "javascript:parent.window.itboye.history.back();";
//                }
//            }
            $url = "javascript:itboye.top_back();";
            $this->assign('_g_ret_url',$url);
//        }




        $menu = MenuHelper::getFMenu(UID);
        if($this->isMobile){
            $topbarMenu = MenuHelper::getTopMenu(UID);
            $leftMenu = MenuHelper::getLeftMenu(UID);
            $breadcrumb = MenuHelper::getBreadcrumb();
            $this->assign('topbar_menu',$topbarMenu);
            $this->assign('left_menu',$leftMenu);
            $this->assign("breadcrumb",$breadcrumb);
        }
        $this->assign("is_mobile",$this->isMobile);
        $this->assign('_menuList',$menu);
        $skinCode = AdminConfigHelper::getValue('BY_SKIN');
        $this->assign('by_skin',AdminFunctionHelper::getBySkin($skinCode));
        $this->assign('user',AdminSessionHelper::getUserInfo());
    }

    /**
     * 设置当前激活状态的菜单
     */
    private function setActiveMenu(){
        $active_menu_id = request()->param('active_menu_id',0);
        if ($active_menu_id != 0) {
            AdminSessionHelper::setCurrentActiveTopMenuId($active_menu_id);
        }
        $active_sub_menu_id = Request::instance()->param('active_sub_menu_id',0);
        // 当前三级导航
        if ($active_sub_menu_id != 0) {
            AdminSessionHelper::setCurrentActiveLeftMenuId($active_sub_menu_id);
        }
    }

    /**
     * 初始化当前用户可见菜单
     */
    private function initUserMenu(){
        $uid = AdminSessionHelper::getUserId();
        if($uid > 0){
            $current_user_menu = AdminSessionHelper::getCurrentUserMenu();
            if (!empty($current_user_menu)) {
                return 0;
            }

            $map = array('uid' => $uid);

            $result = (new AuthGroupAccessLogic())->queryNoPaging($map);

            $menuList = "";
            if ($result['status']) {
                $group_ids = '';
                foreach ($result['info'] as $groupAccess) {
                    $group_ids .= $groupAccess['group_id'] . ',';
                }

                unset($map['uid']);
                if (!empty($group_ids)) {
                    $map = array('id' => array('in', rtrim($group_ids, ",")));
                    $result = (new AuthGroupLogic())->queryNoPaging($map);

                    if ($result['status'] && is_array($result['info'])) {
                        foreach ($result['info'] as $group) {
                            //合并多角色
                            $menuList .= $group['menulist'];
                        }

                    }
                } else {

                }
            }
            AdminSessionHelper::setCurrentUserMenu($menuList);
        }
        return 0;
    }

    //==后台初始化代码结束

    public function _param($key,$default='',$emptyErrMsg=''){

        $value = Request::instance()->param($key,$default);

        if($default == $value && !empty($emptyErrMsg)){
            $this->error($emptyErrMsg);
        }

        return $value;
    }

    /**
     * @param $key
     * @param string $default
     * @param string $emptyErrMsg  为空时的报错
     * @return mixed
     */
    public function _post($key,$default='',$emptyErrMsg=''){

        $value = Request::instance()->post($key,$default);

        if($default == $value && !empty($emptyErrMsg)){
            $this->error($emptyErrMsg);
        }

        return $value;
    }

    /**
     * @param $key
     * @param string $default
     * @param string $emptyErrMsg  为空时的报错
     * @return mixed
     */
    public function _get($key,$default='',$emptyErrMsg=''){
        $value = Request::instance()->get($key,$default);

        if($default == $value && !empty($emptyErrMsg)){
            $this->error($emptyErrMsg);
        }
        return $value;
    }

    public function boye_display($theme='default',$file=false){
        if(!empty($file)){
            return $this->fetch($theme.'/'.$file);
        }else{
            return $this->fetch($theme."/". request()->controller().'/'.request()->action());
//        return $this->fetch("default/". request()->controller().'/'.request()->action());
        }
    }


    //== 其它方法
    /**
     * 分页查询结果处理
     */
    public function queryResult($result) {
        if ($result['status']) {
            $this -> assign("show", $result['info']['show']);
            $this -> assign("list", $result['info']['list']);
            return $this->boye_display();
        } else {
            LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this -> error($result['info']);
        }
    }

    //===========================通用CRUD操作方法
    /**
     * 增加菜单
     * GET:显示
     * @param $entity
     * @param 添加成功后跳转url|bool $success_url 添加成功后跳转url
     */
    protected function addTo($entity, $success_url = false) {
        if (IS_GET) {
            $this -> display();
        } else {
            if ($success_url === false) {
                $success_url = url('Admin/' . CONTROLLER_NAME . '/index');
            }
            $result = apiCall('Admin/' . CONTROLLER_NAME . '/add', array($entity));
            if ($result['status'] === false) {
                 $this -> error($result['info']);
            } else {
                $this -> success(L('RESULT_SUCCESS'), $success_url);
            }

        }
    }




    /**
     * 更新保存，根据主键默认id
     * 示列url:
     * /Admin/Menu/save/id/33
     * id必须以get方式传入
     * @param $logic
     * @param string $primarykey
     * @param null $entity
     * @param bool $redirect_url
     */
    protected function save($logic=null,$primarykey = 'id', $entity = null, $redirect_url = false) {
        if (IS_POST) {
            if ($redirect_url === false) {
                $redirect_url = url('Admin/' . CONTROLLER_NAME . '/index');
            }
            if (is_null($entity)) {
                $entity = Request::instance()->param();
            }

            $id = $this->_param($primarykey, 0);
            if(method_exists($logic,"saveByID")){

                if(isset($entity[$primarykey])){
                    unset($entity[$primarykey]);
                }

                $result = $logic->saveByID($id, $entity);

            }else{
                $result = ResultHelper::error('Admin.php error logic param');
            }

            if ($result['status'] === false) {
                LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
                $this -> error($result['info']);
            } else {
                $this -> success(L('RESULT_SUCCESS'), $redirect_url);
            }
        } else {
            $this -> error("不支持get方式save");
        }
    }

    /**
     * 删除
     * @param 删除成功后跳转|bool $success_url 删除成功后跳转
     */
    public function _delete($logic,$success_url = false) {
        if ($success_url === false) {
            $success_url = url('Admin/' . CONTROLLER_NAME . '/index');
        }

        $map = array('id' => $this->_param('id', -1));

        $result = ResultHelper::error('logic 缺失delete方法');
        if(method_exists($logic,"delete")){
            $result = $logic->delete($map);
        }

        if ($result['status'] === false) {
            $this -> error($result['info']);
        } else {
            $this -> success(L('RESULT_SUCCESS'), $success_url);
        }
    }

    /**
     * 启用
     * @param BaseLogic $logic
     * @param string|boolean $success_url 删除成功后跳转
     * @param string $pk
     */
    public function _enable($logic,$success_url = false,$pk="id") {
        if ($success_url === false) {
            $success_url = url('Admin/' . CONTROLLER_NAME . '/index');
        }

        $map = array($pk => $this->_param($pk, -1));

        $result = ResultHelper::error('logic 缺失enable方法');
        if(method_exists($logic,"enable")){
            $result = $logic->enable($map);
        }

        if ($result['status'] === false) {
            $this -> error($result['info']);
        } else {
            $this -> success(L('RESULT_SUCCESS'), $success_url);
        }
    }

    /**
     * 禁用
     * @param BaseLogic $logic
     * @param string|boolean $success_url 删除成功后跳转
     * @param string $pk
     */
    public function _disable($logic,$success_url = false,$pk="id") {
        if ($success_url === false) {
            $success_url = url('Admin/' . CONTROLLER_NAME . '/index');
        }

        $map = array($pk => $this->_param($pk, -1));

        $result = ResultHelper::error('logic 缺失disable方法');
        if(method_exists($logic,"disable")){
            $result = $logic->disable($map);
        }

        if ($result['status'] === false) {
            $this -> error($result['info']);
        } else {
            $this -> success(L('RESULT_SUCCESS'), $success_url);
        }
    }
}