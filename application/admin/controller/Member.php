<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-19
 * Time: 18:39
 */

namespace app\admin\controller;


use app\src\admin\api\UserApi;
use app\src\admin\helper\AdminFunctionHelper;
use app\src\base\enum\StatusEnum;
use app\src\base\facade\AccountFacade;
use app\src\powersystem\logic\AuthGroupAccessLogic;
use app\src\powersystem\logic\AuthGroupLogic;
use app\src\repairerApply\logic\RepairerApplyLogicV2;
use app\src\session\logic\LoginSessionLogic;
use app\src\user\action\UserLogoutAction;
use app\src\user\facade\DefaultUserFacade;
use app\src\user\logic\MemberConfigLogic;
use app\src\user\logic\MemberLogic;
use app\src\user\logic\UcenterMemberLogic;
use app\src\user\logic\VUserInfoLogic;

use app\src\user\logic\DriverLogicV2;
use app\src\user\logic\WorkerLogicV2;
use app\src\wallet\logic\WalletHisLogicV2;
use app\src\wallet\logic\WalletLogic;
use app\src\wallet\model\WalletHis;
use think\exception\DbException;
use think\Request;

class Member extends Admin
{

    public function index() {

        $mobile   = $this->_param('mobile', '');
        $nickname   = $this->_param('nickname', '');
        $u_group   = $this->_param('u_group', '');
        $params     = [];
        $map        = null;
        if(!empty($mobile)){
            $map['mobile'] = array('like', "%" . $mobile  . "%");
            $params['mobile'] = $mobile;
        }
        if(!empty($nickname)){
            $map['nickname'] = array('like', "%" . $nickname  . "%");
            $params['nickname'] = $nickname;
        }

        if(!empty($u_group)){
            $map['u_group'] = $u_group;
            $params['u_group'] = $u_group;
        }
        $p = $this->_param('p',0);
        $page = array('curpage' => $p, 'size' => 10);
        $order = " reg_time desc ";
        $result = (new VUserInfoLogic())->queryWithPagingHtml($map, $page, $order,$params);

        if ($result['status']) {

            $params['p'] = $p;
            $this -> assign('params',$params);
            $this -> assign('mobile',$mobile);
            $this -> assign("u_group",$u_group);
            $this -> assign("nickname",$nickname);
            $this -> assign("show", $result['info']['show']);
            $this -> assign("list", $result['info']['list']);
            $result = (new AuthGroupLogic())->queryNoPaging();
            if($result['status']){
                $this->assign('user_group',$result['info']);
            }

            return $this -> boye_display();
        } else {
            $this -> error($result['info']);
        }
    }

    /**
     * 实名审核
     */
    public function realname(){
        $uid = $this->_param('uid',0);
        $this->assign('uid',$uid);
        if($uid >0 ){
            $r = (new MemberLogic())->getInfo(['uid'=>$uid]);

            if(!$r['status']) $this->error($r['info']);
            if(empty($r['info'])) $this->error('uid错误');
            $nickname = $r['info']['nickname'];
        }else{
            $nickname = '';
        }
        $this->assign('nickname',$nickname);
        $page = array('curpage' => $this->_param('p'), 'size' => 15);
        $map  = array('identity_validate'=>2);
        $result = (new MemberConfigLogic())->queryWithPagingHtml($map,$page);
        $this->assign('member',$result['info']['list']);
        $this->assign('show',$result['info']['show']);

        return $this->boye_display();
    }

    /**
     * 审核通过
     */
    public function pass(){

        $map = array('uid' => $this->_param('id',0));
        $entity = array('identity_validate'=>1);
        $result = (new MemberConfigLogic())->save($map,$entity);
        if($result['status']){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }



    /**
     * 审核失败
     */
    public function fail(){
        $map = array('uid'=>$this->_param('id',0));
        $entity = array('identity_validate'=>0);
        $result = (new MemberConfigLogic())->save($map,$entity);

        if($result['status']){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
    /**
     * 删除用户
     * 假删除
     */
    public function delete(){
        $uid = $this->_param('uid',0);
        if(AdminFunctionHelper::isRoot($uid)){
            $this->error("禁止对超级管理员进行删除操作！");
        }
        if($uid > 0){
            $result = (new UcenterMemberLogic())->saveByID($uid,['status'=>StatusEnum::DELETE]);
            if(!$result['status']){
                $this->error($result['info']);
            }
        }

        $this->success("删除成功!");
    }

    /**
     * 启用
     */
    public function enable(){
        $id = $this->_param('id',0);
        if(AdminFunctionHelper::isRoot($id)){
            $this->error("禁止对超级管理员进行禁用操作！");
        }

        $result = (new UcenterMemberLogic())->saveByID($id,['status'=>StatusEnum::NORMAL]);
        if($result['status']){
            $this->success('启用成功',url('Admin/Member/index'));
        }else{
            $this->error('启用失败',url('Admin/Member/index'));
        }
    }

    /**
     * 禁用
     */
    public function disable(){

        $id = $this->_param('id',0);
        if(AdminFunctionHelper::isRoot($id)){
            $this->error("禁止对超级管理员进行禁用操作！");
        }

        $result = (new UcenterMemberLogic())->saveByID($id,['status'=>StatusEnum::DISABLED]);
        if($result['status']){
            $this->success('禁用成功',url('Admin/Member/index'));
        }else{
            $this->error('禁用失败',url('Admin/Member/index'));
        }
    }

    /**
     * add
     * @param string $username
     * @param string $password
     * @param string $repassword
     * @return mixed
     */
    public function add($username = '', $password = '', $repassword = ''){
        if(IS_POST){

            if($password != $repassword){
                $this->error("密码和重复密码不一致！");
            }

            $nickname = $this->_param('nickname','');

            /* 调用注册接口注册用户 */
            $country = "+86";
            $result = UserApi::register($nickname,$username,$password,$country,"itboye");

            if($result['status']){
                //注册成功
                $this->success('用户添加成功！',url('Member/index'));
            } else { //注册失败，显示错误信息
                $this->error($result['info']);
            }

        }else{

            return $this->boye_display();
        }
    }

    /**
     * 检测用户名是否已存在
     */
    public function check_username($username){
        $result = (new UcenterMemberLogic())->checkUsername($username);
        if($result['status']){
            echo "false";
        }else{
            echo "true";
        }
    }

    /**
     * ajax - member-select
     */
    public function select(){

//        $map['nickname'] = array('like', "%" . $this->_param('q', '', 'trim') . "%");
        $map = [];
        $q = $this->_param('q',-1);
        if(!empty($q) && $q != -1){
            $map['uid'] = $q;
        }

        $page = array('curpage'=>0,'size'=>20);
        $order = "uid desc";
        $result = (new MemberLogic())->query($map,$page, $order,false,'uid,nickname,head');

        if($result['status']){
            $list = $result['info']['list'];
            foreach($list as $key=>$g){
                $list[$key]['id']=$list[$key]['uid'];
                $list[$key]['head'] = getImgUrl($list[$key]['head'],60);
            }

            $this->success("读取成功","",$list);
        }

    }

    public function view(){
        $id = $this->_param('id',0);

        $result = (new VUserInfoLogic())->getInfo(array("id"=>$id));
        if(!$result['status']){
            $this->error($result['info']);
        }

        $this->assign("userinfo",$result['info']);

        $result = (new AuthGroupAccessLogic())->queryGroupInfo($id);
        if(!$result['status']){
            $this->error($result['info']);
        }

        $this->assign("userroles",$result['info']);

        $result = (new LoginSessionLogic())->queryNoPaging(['uid'=>$id]);
        if(!$result['status']){
            $this->error($result['info']);
        }
        $this->assign("login_device_list",$result['info']);
        return $this->boye_display();
    }

    /**
     * 单个用户角色管理
     */
    public function user_role(){

        $id = $this->_param('id','','缺失id');
        if(IS_GET){

            $result = (new AuthGroupAccessLogic())->queryGroupInfo($id);
            $role_ids = "";
            foreach ($result['info'] as $vo){
                $role_ids .= $vo['id'].',';
            }
            $this->assign('role_ids',$role_ids);
            $result = (new AuthGroupLogic())->queryNoPaging();
            $this->assign('roles',$result['info']);
            $this->assign('id',$id);
            return $this->boye_display();
        }else{
            $roles = $this->_param('roles/a',[]);

            $result = (new AuthGroupAccessLogic())->delete(['uid'=>$id]);
            $data = [];
            foreach ($roles as $vo){
                $data[] = ['uid'=>$id,'group_id'=>$vo,'is_auth'=>0];
            }

            $result = (new AuthGroupAccessLogic())->saveAll($data);
            if($result['status']){
                $this->success('保存成功',url("Member/user_role",array('id'=>$id)));
            }else{
                $this->error('保存失败',url("Member/user_role",array('id'=>$id)));
            }
        }

    }

    /**
     * 强制踢下线
     *
     */
    public function force_logout(){
        $id = $this->_param('id','');
        $result = (new UserLogoutAction())->logout($id,"force_logout");

        if($result['status']){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }


    /**
     * 用户编辑
     * @return mixed
     */
    public function edit(){
        $id = $this->_param('id',0);
        if(IS_GET){
            $result = (new VUserInfoLogic())->getInfo(['id'=>$id]);
            $this->assign('user',$result['info']);
            return $this->boye_display();
        }
        $params = Request::instance()->param();
        $params['s_id'] = "itboye";
        $params['uid'] = $id;

        $result = (new UserApi())->update($params);

        if($result['status']){
            $this->success('操作成功',url('Member/edit',array('id'=>$id)));
        }else{
            $this->error('操作失败-'.$result['info'],url('Member/edit',array('id'=>$id)));
        }
    }

    /**
     * 余额充值
     */
    public function addMoney(){
        $id = $this->_param('id',0);
        if(IS_GET){

            $result = (new WalletLogic())->getInfoIfNotExistThenAdd($id);
            $this->assign('wallet',$result['info']);
            return $this->boye_display();
        }

        $money = $this->_param('money',0);
        $money = floatval($money) * 100.0;

        if($money > 0) {
            $result = (new WalletLogic())->plusMoney($id, $money, "[后台人工充值" . $money / 100.0 . " 金额]");
            if($result['status']){
                $this->success('操作成功');
            }

            $this->error($result['info']);
        }

        $this->error('操作失败');
    }

}