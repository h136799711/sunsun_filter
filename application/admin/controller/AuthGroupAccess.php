<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/3
 * Time: 14:05
 */

namespace app\admin\controller;


use app\src\admin\helper\AdminFunctionHelper;
use app\src\powersystem\logic\AuthGroupAccessLogic;

class AuthGroupAccess extends Admin
{
    /**
     * 将指定用户添加到指定用户组
     */
    public function addToGroup()
    {

        $uid = $this->_param('uid', '');
        $groupid = $this->_param('groupid', '');

        if (empty($uid) || empty($groupid)) {
            $this->error("参数错误");
        }

        if (AdminFunctionHelper::isRoot($uid)) {
            $this->error("不能对超级管理员进行操作");
        }

        if ($groupid) {
            $groupid = intval($groupid);
        }

        $result = (new AuthGroupAccessLogic())->addToGroup($uid, $groupid);

        if ($result['status']) {
            $this->success("操作成功~", url('Admin/AuthManage/user', array('groupid' => $groupid)));
        } else {
            $this->error($result['info']);
        }
    }

    /**
     * 将指定用户从指定用户组移除
     */
    public function delFromGroup()
    {
        $groupid =  $this->_param('groupid', -1);
        $uid = $this->_param('uid', -1);
        if ($groupid === -1 || $uid === -1) {
            $this->error("参数错误！");
        }
        $map = array('uid' => $uid, "group_id" => $groupid);

        $result = (new AuthGroupAccessLogic())->delete($map);

        if ($result['status']) {
            $this->success("操作成功~", url('Admin/AuthManage/user'));
        } else {
            $this->error($result['info']);
        }

    }


}