<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 14:49
 */

namespace app\src\file\logic;


use app\src\base\logic\BaseLogic;
use app\src\extend\upload\Upload;
use app\src\file\model\UserPicture;
use app\src\extend\Page;
use think\Db;

class UserFileLogic extends BaseLogic
{
    private $error;

    public function getError(){
        return $this->error;
    }
    /**
     * 检测上传目录
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    public function checkPath($path){
      /* 检测并创建目录 */
      if(!is_dir($path)){
        if(!mkdir($path)){
          $this->error = '上传目录 ' . $path . ' 创建失败！';
          return false;
        }else{
          chmod($path,0777);
          /* 检测目录是否可写 */
          if (!is_writable($path)) {
              $this->error = '上传目录 ' . $path . ' 不可写！';
              return false;
          } else {
              return true;
          }
        }
      }
      return true;
    }

    /**
     * 文件上传
     * todo: 文件真实目录是否存在
     * todo: 待删除重复的文件
     * @param  array  $files   要上传的文件列表（通常是$_FILES数组）
     * @param  array  $setting config.php中的文件上传配置
     * eg: ConfigHelper::user_picture_upload(),
     * @param  array  $extInfo 传递过来的额外信息
     * eg:['uid' => $uid,'show_url' => ConfigHelper::upload_path(),'type'=>$type];
     * @param  string $driver  上传驱动名称
     * @param  array  $config  上传驱动配置
     * @return array           文件上传成功后的信息
     */
    public function upload($files, $setting,$extInfo, $driver = 'local', $config = null){
        $now      = time();
        $model    = $this->getModel();
        $type     = $extInfo['type'];
        $uid      = $extInfo['uid'];
        $show_url = $extInfo['show_url'];

        $rule = 'date';//保存规则 : date(推荐,其他类型要改) md5 ...
        $path  = isset($setting['rootPath']) ? rtrim($setting['rootPath'],'/'):'./upload/user_picture';
        $path .= '/'.$type; //根据文件类型再分分文件夹
        // if(!$this->checkPath($path)) return $this->getError();
        //设置日期子文件夹
        $relate_path = ltrim($path,'.').'/';
        $sub_path    = isset($setting['subName']) ? $setting['subName']:['date','Ymd'];
        if(is_string($sub_path[1]))
          $sub_path = call_user_func($sub_path[0],$sub_path[1]);
        else
          $sub_path = call_user_func_array($sub_path[0],$sub_path[1]);
        // if(!$this->checkPath($path.'/'.$sub_path)) return $this->getError();

        //统一封装成多张上传
        if(is_object($files)) $files = [$files];

        //文件检查设置 - 后缀
        $check = ['ext' => $setting['exts']];
        //文件检查设置 - 大小
        if(isset($setting['maxSize'])){
          $size =  (int) $setting['maxSize'];
          if($size>0) $check['size'] = $size;
        }
        //文件检查设置 - 类型
        if(isset($setting['mimes']) && !empty($setting['mimes'])) $check['type'] = $setting['mimes'];
        $type = $extInfo['type'];

        $return  = [];//返回
        foreach($files as $file){
            //  上传根目录: /public/upload/user_picture
            //  详细地址  : 上传根目录+/{$type}/{date}/{md5}.{ext}
            // $temp = $file->getPathName();
            // $md5  = md5_file($temp);
            // $sha1 = sha1_file($temp);
            $info = $file->getInfo();
            $name = $info['name'];
            //? 文件检查 长宽比
            if($rate_check){
                $f_info = getimagesize($temp);
                if($f_info[0]*$rate_arr[1] != $f_info[1]*$rate_arr[0]){
                  return '该类型图片需要比例'.$rate_arr[0].':'.$rate_arr[1];
                }
            }

            //文件检查
            if(!$file->check($check)) return $file->getError();
            //上传图片
            $upload = $file->rule($rule)->move($path);
            if($upload->getError()){
                // 上传失败获取错误信息
                return $upload->getError();
            }
            // 成功上传后 获取上传信息
            $sha1 = $upload->hash('sha1');
            $md5  = $upload->hash('md5');

            // ? 图片上传过
            $field = 'path,uid,ori_name,savename,size,url,imgurl,md5,sha1,type,ext,id';
            $result = $this->getInfo(['md5'=>$md5,'sha1'=>$sha1,'status'=>1]);
            if($result['status'] && empty($result)){ //无需记录
                //todo : 真实图片是否存在
                // $path = '.'.$field['path'];
                // if($is_file($path)){
                //     unlink($path);
                // }
                $img_info = $result['info'];
                //该图片该类型该用户未上传过
                unset($img_info['id']);
                $img_info['uid']      = $uid;
                $img_info['type']     = $type;
                $img_info['ori_name'] = $name;
                $img_info['create_time'] = $now;
                $result = $this->add($img_info);
                if($result['status']){
                    $img_info['id'] = $result['info'];
                }
                $return[] = $img_info;
            }else{

                $ext      = $upload->getExtension();
                $savename = $upload->getFilename();
                $path     = $relate_path.$sub_path.'/'.$savename;
                $imgurl   = rtrim($show_url,'/').$path;
                $img_info = [
                  'path'        => $path,
                  'uid'         => $uid,
                  'ori_name'    => $name,
                  'savename'    => $savename,
                  'size'        => $info['size'],
                  'url'         => '',//图片链接
                  'imgurl'      => $imgurl,//完整显示地址
                  'md5'         => $md5,
                  'sha1'        => $sha1,
                  'type'        => $type,
                  'ext'         => $ext,
                  'create_time' => $now,
                ];
                $result =  $this->add($img_info);
                if(!$result['status']){
                    //TODO: 上传成功，插入失败,记录日志
                    return $result['info'];
                }
                $img_info['id']  = $result['info'];
                $return[] = $img_info;
            }
        }
        return $return;
    }

    /**
     * curl 文件上传 - 2016-12-05 rainbow
     * @param  array  $files
     *  $data : 文件流数据
     *  eg: $files  = ['data'=>$fdata,'name'=>$fname,'type'=>$ftype]
     * @param  array  $setting 文件上传配置 application/config.php
     *  eg: config('user_picture_upload')
     * @param  string $extInfo
     *  eg: ['uid' => $uid,'imgurl' => config('upload_url'),'type'=>$type];
     * @param  string $driver  上传驱动名称
     * @param  array  $config  上传驱动配置
     * @return array           文件上传成功后的信息
     */
    public function curl_upload($files, $setting,$extInfo, $driver = 'local', $config = null){
// shalt('here');die();
        /* 上传文件 */
        $setting['callback']    = [$this, 'isFile'];
        $setting['removeTrash'] = [$this, 'removeTrash'];
        $setting['savePath']    = (isset($extInfo['type']) ? $extInfo['type']: 'other').'/';
        $Upload = new Upload($setting, $driver, $config);
        $info   = $Upload->curl_upload($files,(new UserFileLogic()));

        if($info){ //文件上传成功，记录文件信息
            $uid = $extInfo['uid'];
            $type = $extInfo['type'];

            $infos = ['image'=>$info];
            $record = [];
            foreach ($infos as $key => &$value) {
                /* 已经存在文件记录 */
                if(isset($value['id']) && is_numeric($value['id'])){
                    unset($value['id']);
                    /* 记录文件信息 */
                    $entity  = $value;
                    $entity['create_time'] = time();
                }else {
                    $value = array_merge($value, $extInfo);
                    $value['path'] = substr($value['savepath'],1).$value['savename'];
                    $value['ori_name'] = $value['name'];
                    $value['imgurl'] = $value['imgurl'].$value['path'];
                    $entity = [
                        'driver'      => $driver,
                        'path'        => $value['path'],
                        'ori_name'    => $value['ori_name'],
                        'savename'    => $value['savename'],
                        'size'        => $value['size'],
                        'imgurl'      => $value['imgurl'],
                        'md5'         => $value['md5'],
                        'sha1'        => $value['sha1'],
                        'create_time' => time(),
                        'ext'         => $value['ext'],
                        'uid'         => $uid,
                        'type'        => $type,
                    ];
                }

                $result = $this->add($entity);
                if($result['status']){
                  $value['id'] = $result['info'];
                } else {

                }
            }
            return $infos; //文件上传成功
        } else {
            $this->error = $Upload->getError();
            return false;
        }
    }


    /**
     * 下载指定文件
     * @param  number  $root 文件存储根目录
     * @param  integer $id   文件ID
     * @param  string   $args     回调函数参数
     * @return boolean       false-下载失败，否则输出下载文件
     */
    public function download($root, $id, $callback = null, $args = null){
        /* 获取下载文件信息 */
        $result = $this->getInfo(['id'=>$id]);
        if(!$result['status']){
            $this->error = '不存在该文件！';
            return false;
        }
        $file = $result['info'];
        /* 下载文件 */
        switch ($file['location']) {
            case 0: //下载本地文件
                $file['rootpath'] = $root;
                return $this->downLocalFile($file, $callback, $args);
            case 1: //TODO: 下载远程FTP文件
                break;
            default:
                $this->error = '不支持的文件存储类型！';
                return false;

        }

    }

    /**
     * 检测当前上传的文件是否已经存在
     * @param  array $file 文件上传数组
     * @return bool 文件信息， false - 不存在该文件
     * @throws \Exception
     */
    public function isFile($file){
        if(empty($file['md5'])){
            throw new \Exception('缺少参数:md5');
        }
        /* 查找文件 */
        $map = array('md5' => $file['md5'],'sha1'=>$file['sha1'],);
        return $this->getModel()->where($map)->find();
    }

    /**
     * 下载本地文件
     * @param  array    $file     文件信息数组
     * @param  callable $callback 下载回调函数，一般用于增加下载次数
     * @param  string   $args     回调函数参数
     * @return boolean            下载失败返回false
     */
    private function downLocalFile($file, $callback = null, $args = null){
        if(is_file($file['rootpath'].$file['savepath'].$file['savename'])){
            /* 调用回调函数新增下载数 */
            is_callable($callback) && call_user_func($callback, $args);

            /* 执行下载 */ //TODO: 大文件断点续传
            header("Content-Description: File Transfer");
            header('Content-type: ' . $file['type']);
            header('Content-Length:' . $file['size']);
            if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
                header('Content-Disposition: attachment; filename="' . rawurlencode($file['name']) . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
            }
            readfile($file['rootpath'].$file['savepath'].$file['savename']);
            exit;
        } else {
            $this->error = '文件已被删除！';
            return false;
        }
    }

    /**
     * 清除数据库存在但本地不存在的数据
     * @param $data
     */
    public function removeTrash($data){
        $this->getModel()->where(array('id'=>$data['id'],))->delete();
    }




    public function queryWithType($map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false){

        $field = 'pic.id as id,pic.path as path,pic.imgurl as imgurl,dt.name as type_name,pic.url,pic.ori_name as ori_name,pic.create_time as create_time,pic.uid as uid';
        $query = Db::table("itboye_user_picture")->alias("pic")
            ->field($field)
            ->join(["common_datatree"=>"dt"],'dt.code = pic.type',"LEFT");
        if(!is_null($map)){
            $query = $query->where($map);
        }

        $list = $query ->order('sort desc')-> page($page['curpage'] . ',' . $page['size']) -> select();
        $query = Db::table("itboye_user_picture")->alias("pic")
            ->field($field)
            ->join(["common_datatree"=>"dt"],'dt.code = pic.type',"LEFT");
        $count = $query -> where($map) -> count();
        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false && is_array($params)) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));
    }


}