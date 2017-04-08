<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 15:51
 */

namespace app\index\controller;


use app\src\base\helper\ConfigHelper;
use app\src\base\helper\ExceptionHelper;
use app\src\file\logic\UserFileLogic;
use app\src\file\logic\UserPictureLogic;
use app\src\user\logic\MemberLogic;
use app\src\user\logic\UcenterMemberLogic;
use think\Controller;
use think\Exception;

class UserFile extends Controller
{
    protected $notify_id = NOW_TIME;
    protected $client_id = "";

    public function download(){
        $id = $this->request->get('id', 0);
        $file = new UserFileLogic();
        $result = $file->getInfo(['id'=>$id]);

        if($result['status']){

            $info = $result['info'];
            $file->setInc(['id'=>$id],'dnt',1);
//            $location = $info['location'];
//            if($location == 0){
//
//            }
            $path = '.'.$info['path'];
            Header( "Content-type:  application/octet-stream ");
            Header( "Accept-Length: " .$info['size']);
            header('Content-Length: ' .$info['size']);
            header( "Content-Disposition:  attachment;  filename= ".$info['savename']);

            $image = @readfile($path);
            echo $image;
            exit();
        }else{

            redirect(config('site_url').'/html/404.html');
        }

    }

    /**
     * curl upload
     * @author hebidu <email:346551990@qq.com>
     */
    public function curl_upload()
    {
        try{
            $this->client_id = $this->_param('client_id','');
            addLog("File/curl_upload",$_GET,'',$this->client_id."调用文件上传接口!");

            $uid   = $this->_param('uid',0);
            $type  = $this->_param('type','');
            $fdata = $this->_param('fdata','');
            $fname = $this->_param('fname','');
            $ftype = $this->_param('ftype','');

            if($uid <= 0) $this->apiReturnErr(lang('invalid_parameter',['param'=>$uid]));

            $userLogic = new UcenterMemberLogic();

            $result = $userLogic->getInfo(['id'=>$uid]);

            if(!$result['status']) $this->apiReturnErr( lang('err_file_user_id_not_exist') );

            /* 调用文件上传组件上传文件 */
            $file = new UserFileLogic();
            $extInfo = array(
                'uid' => $uid,
                'imgurl' => ConfigHelper::upload_path(),
                'type'=>$type);

            $info = $file->curl_upload(
                ['data'=>$fdata,'name'=>$fname,'type'=>$ftype],
                config('user_file_upload'),
                $extInfo
            );

            /* 记录图片信息 */
            if(is_array($info)){

                $this -> apiReturnSuc($info);
            } else {
                $this -> apiReturnErr($file->getError());
            }

        }catch (Exception $ex){
            $this->apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }

    public function _param($key, $default = '', $emptyErrMsg = '')
    {
        $value = $this->request->post($key, $default);

        if ($value == $default) {
            $value = $this->request->get($key, $default);
        }

        if ($default == $value && !empty($emptyErrMsg)) {
            $this->apiReturnErr($emptyErrMsg);
        }
        return $value;
    }

    /**
     * ajax返回，并自动写入token返回
     * @param $data
     * @param int $code
     * @internal param $i
     */
    protected function apiReturnErr($data, $code = -1)
    {
        header('Content-Type:application/json; charset=utf-8');
        json(['code' => $code, 'data' => $data, 'notify_id' => $this->notify_id])->send();
        exit(0);
    }

    /**
     * ajax返回
     * @param $data
     * @internal param $i
     */
    protected function apiReturnSuc($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        json(['code' => 0, 'data' => $data, 'notify_id' => $this->notify_id])->send();
        exit(0);
    }


}