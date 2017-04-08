<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-17
 * Time: 17:19
 */

namespace app\admin\controller;

use app\src\admin\helper\AdminConfigHelper;
use app\src\base\helper\ConfigHelper;
use app\src\file\logic\UserPictureLogic;
use think\Response;

class File extends Admin
{

    protected function _initialize(){

        //TODO: 导致session，修改不启作用，沿用上次，导致一级菜单未能存入session，使得当前激活菜单不正确
        //FIXME:考虑，将图片上传放到另外一个类中
        //解决uploadify上传session问题

        session('[pause]');
        $session_id = $this->_param('session_id','');
        if (!empty($session_id)) {
            session_id($session_id);
            session('[start]');
        }

        parent::_initialize();
    }

    public function uploadPicture(){

        if(IS_POST){

            /* 返回标准数据 */
            $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

            /* 上传到远程服务器 */
            $result = $this->uploadPictureRemote($_FILES['image']);
            $result = json_decode($result,true);
            if(isset($result['code']) && $result['code'] == 0){
                $return = $result['data']['image'];
                $return['status'] = 1;
            }else{
                $return['status'] = 0;
                $return['info'] = $result['data'];
            }


            /* 返回JSON数据 */
            $this->ajaxReturn($return);
        }

    }

    //上传到远程服务器
    private function uploadPictureRemote($file,$type="other"){

        $tmp_path = $file['tmp_name'];
        if(!is_uploaded_file($tmp_path)) exit('invalid tmp file');
        $data = file_get_contents($tmp_path);
        $apiConfig = AdminConfigHelper::getByApiConfig();
        $clientId = $apiConfig['client_id'];
        $url = config('file_curl_upload_url').'?client_id='.$clientId;
        //只支持单文件
        $file  = ['fdata'=>$data,'ftype'=>$file['type'],'fname'=>$file['name']];
        $param = ['type'=>$type,'uid'=>UID];

        $op    = $this->upload_file($url,$file,$param);
        return $op;
    }
    private function upload_file($url,$file,$param){
        $post_data = array_merge($file,$param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT,5);            //定义超时5秒钟
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

        $return_data = curl_exec($ch);

        curl_close($ch);
        return $return_data;
    }



    public function picturelist(){
        if(IS_AJAX){
            $q = $this->_param('q','');
            $time = $this->_param('time','');
            $cur = $this->_param('p',0);
            if( $cur==0){
                $cur=$this->_param('p',0);
            }
            $size = $this->_param('size',10);
            $map = array('uid'=>UID,'status'=>1);
            $page = array('curpage'=>$cur,'size'=>$size);
            $order = 'create_time desc';
            $params = array(
                'p'=>$cur,
                'size'=>$size,
            );
            if(!empty($q)){
                $params['q'] = $q;
                $map['ori_name'] = array("like",'%'.$q.'%');
            }

            if(!empty($time)){
                $time = toUnixTimestamp($time);
                $map['create_time'] = array(array('lt',$time+24*3600),array('gt',$time-1),'and');
            }

            $fields = 'id,create_time,status,path,url,md5,imgurl,ori_name,savename,size';
            $r = (new UserPictureLogic())->queryWithPagingHtml($map,$page,$order,$params,$fields);

            $r['status'] = (int) $r['status'];
            $this->ajaxReturn($r);

        }
    }

    public function avatarlist(){
        if(IS_AJAX){
            $q = $this->_param('q','');
            $cur = $this->_param('p',0);
            if( $cur==0){
                $cur=$this->_param('p',0);
            }
            $size = $this->_param('size',10);
            $map = array('status'=>1);
            $page = array('curpage'=>$cur,'size'=>$size);
            $order = 'create_time desc';
            $params = array(
                'p'=>$cur,
                'size'=>$size,
            );
            if(!empty($q)){
                $params['q'] = $q;
                $map['ori_name'] = array("like",'%'.$q.'%');
            }

            $map['type']=array("like",'00Q%');
            $fields = 'id,create_time,status,path,url,md5,imgurl,ori_name,savename,size';
            $r = (new UserPictureLogic())->queryWithPagingHtml($map,$page,$order,$params,$fields);
            $r['status'] = (int) $r['status'];
            $this->ajaxReturn($r);

        }
    }
    /**
     * 上传图片接口
     */
    public function uploadUserPicture(){

        if(IS_POST){

            if(!isset($_FILES['wxshop'])){
                $this->error("文件对象必须为wxshop");
            }

            $result['info'] = "";
            //2.再上传到自己的服务器，
            //TODO:也可以上传到QINIU上
            /* 返回标准数据 */
            $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

            if(config('UPLOAD_PICTURE_REMOTE_URL')==NULL) {
                $type = 'other';//todo: 上传时设置

                /* 调用文件上传组件上传文件 */
                $Picture = new UserPictureLogic();
                $extInfo = array('uid' => UID, 'show_url' => ConfigHelper::upload_path(),'type'=>$type);
                $file = request()->file('wxshop');
                $info = $Picture->upload(
                    $file,
                    config('user_picture_upload')
                    , $extInfo
                );

                /* 记录图片信息 */
                if ($info) {
                    $return['status'] = 1;
                    $return['info'] = $info[0];
                    // $return = array_merge($info, $return);
                } else {
                    $return['status'] = 0;
                    $return['info'] = $Picture->getError();
                }
            }else{
                /* 上传到远程服务器 */
                $result = $this->uploadPictureRemote($_FILES['wxshop']);

                $result = json_decode($result,true);

                if(isset($result['code']) &&  $result['code']==0){
                    $return = $result['data']['image'];
                    $return['status'] = 1;
                }else{
                    $return['status'] = 0;
                    $return['info'] = $result['data'];
                }
            }

            /* 返回JSON数据 */
            $this->ajaxReturn($return);
        }

    }

    private function ajaxReturn($data){
        $response =  Response::create($data, "json")->code(200);
        $response->header("X-Powered-By","WWW.ITBOYE.COM")->send();
        exit;
    }

    /**
     * 图片删除
     */
    public function del(){
        $imgIds = $this->_param("imgIds/a",-1);
        if($imgIds!=-1){
            $map=array(
                'id'=>array(
                    'in',$imgIds
                )
            );

            $result = (new UserPictureLogic())->save($map,array('status'=>-1));

            if ($result['status']) {
                $this->success("删除成功!");
            }else{
                $this->error("删除失败!");
            }

        }else{
            $this->error("请先选中要删除的图片!");
        }
    }


    /**
     * 上传文件接口
     */
    public function uploadUserFile(){

        if(IS_POST){

            if(!isset($_FILES['user_file'])){
                $this->error("文件对象必须为user_file");
            }

            $result['info'] = "";
            //2.再上传到自己的服务器，
            //TODO:也可以上传到QINIU上
            /* 返回标准数据 */
            $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

            /* 上传到远程服务器 */
            $result = $this->uploadFileRemote($_FILES['user_file']);

            $result = json_decode($result,true);

            if(isset($result['code']) &&  $result['code']==0){
                $return = $result['data']['image'];
                $return['status'] = 1;
            }else{
                $return['status'] = 0;
                $return['info'] = $result['data'];
            }

            /* 返回JSON数据 */
            $this->ajaxReturn($return);
        }

    }


    //上传到远程服务器
    private function uploadFileRemote($file,$type="other"){

        $tmp_path = $file['tmp_name'];
        if(!is_uploaded_file($tmp_path)) exit('invalid tmp file');
        $data = file_get_contents($tmp_path);
        $apiConfig = AdminConfigHelper::getByApiConfig();
        $clientId = $apiConfig['client_id'];
        $url = config('user_file_curl_upload_url').'?client_id='.$clientId;
        //只支持单文件
        $file  = ['fdata'=>$data,'ftype'=>$file['type'],'fname'=>$file['name']];
        $param = ['type'=>$type,'uid'=>UID];

        $op    = $this->upload_file($url,$file,$param);
        return $op;
    }
}