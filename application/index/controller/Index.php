<?php
namespace app\index\controller;

use app\src\base\enum\ErrorCode;
use app\src\base\exception\BusinessException;
use app\src\base\helper\ConfigHelper;
use app\src\base\helper\ExceptionHelper;
use app\src\base\utils\CacheUtils;
use app\src\base\utils\CryptUtils;
use app\src\encrypt\algorithm\AlgParams;
use app\src\encrypt\algorithm\IAlgorithm;
use app\src\hook\DomainAuthHook;
use app\src\hook\LoginAuthHook;
use think\Exception;

class Index extends Base
{

    private $app_version;  //当前软件的版本
    private $app_type;     //当前软件的类型 ，ios，android，pc ,by_test

    private $decrypt_data; //解密过的数据


    /**
     * 接口入口
     */
    public function index()
    { 
        try{

            //1. 公共参数初始化
            $this->_initParameter();
            //2. 公共参数校验
            $this->_check();
            //3. 配置信息读取
            CacheUtils::initAppConfig();

            //已登录会话ID
            $login_s_id = $this->getLoginSId();
            $module = $this->getModuleName();

            $api_type = preg_replace("/_/","/",substr(ltrim($this->alParams->getType()),3),1);
            $api_type = preg_split("/\//",$api_type);

            if(count($api_type) < 2){
                $this->apiReturnErr("type参数不正确!",ErrorCode::Invalid_Parameter);
            }

            $action_name = $api_type[1];
            $controller_name = $api_type[0];

            $auth_value = $module.'_'.$controller_name.'_'.$action_name;

            $domainClass = $controller_name.'Domain/'.$action_name;

            $this->decrypt_data['domain_class'] = $domainClass;

            if($module == 'default'){
                $module = "domain";
            }else{
                $module = $module . "_domain";
            }

            $cls_name = "app\\$module\\".$controller_name.'Domain';
            if(!class_exists($cls_name,true)){
                $this->apiReturnErr(lang('err_404'),ErrorCode::Not_Found_Resource);
            }

            $uid = $this->getUid();

            //1. 登录判定
            $LoginAuthHook = new LoginAuthHook();
            $result = $LoginAuthHook->check($uid,$login_s_id,$auth_value,"",config('login_session_expire_time'));
            if(!$result['status']){
                $this->apiReturnErr($result['info'],ErrorCode::Api_Need_Login);
            }

            //2. 授权判定
            $domainAuthHook = new DomainAuthHook();
            $result = $domainAuthHook->auth($auth_value,$uid);
            if(!$result['status']){
                $this->apiReturnErr($result['info'],ErrorCode::Api_No_Auth);
            }

            //3. 初始化业务类
            $class = new  $cls_name($this->algInstance,$this->decrypt_data);
            if(!method_exists($class,$action_name)){
                $this->apiReturnErr('api-'.lang('err_404'),ErrorCode::Not_Found_Resource);
            }

            //4. 调用方法
            $class->$action_name();

            //1. 这一步不会走到,如果走到，说明前面没有exit
            throw  new BusinessException("no return data");

        }catch (Exception $ex) {
            $this->apiReturnErr(ExceptionHelper::getErrorString($ex), ErrorCode::Business_Error);
        }
    }

    /**
     * 初始化公共参数
     * time:必须 | 请求时间戳
     * sign:必须 | 签名
     * data:必须 | 数据
     * type:必须 | 调用接口
     * notify_id:必须 | 通知id
     * api_ver:必须   |
     *
     * app_version: 否 | APP版本
     * app_type: 否    | ios or android
     * lang: 否 | 默认en ，语言参数
     *
     */
    private function _initParameter(){


        $this->app_version = isset($_POST['app_version']) ? $_POST['app_version'] : $this->app_version;
        $this->app_type = isset($_POST['app_type']) ? $_POST['app_type'] : $this->app_type;
        
        //检查语言是否支持
        $lang_support = ConfigHelper::getLangSupport();
        $is_support = false;

        foreach ($lang_support as $lang){
            if($lang['value'] == $this->lang){
                $is_support = true;
            }
        }

        if(!$is_support){
            //对于不支持的语言都使用zh-cn
            $this->lang = "zh-cn";
        }
    }
    
    /**
     * 解密验证
     * 1. 校验请求时间戳是否合法（发起请求 - 服务器处理请求 <  60秒）
     * 2. 对签名sign 进行验证
     * 3. 对数据进行解密，存入decrypt_data
     */
    private function _check(){

        if(!($this->algInstance instanceof IAlgorithm)){
            throw  new Exception("invalid algInstance param");
        }
        //modify by hebidu 去除时间验证
        //1. 请求时间戳校验
//        $now = microtime(true);//time();
//        //时间误差 +- 1分钟
//        if($now - 60 > $this->alParams->getTime() || $this->alParams->getTime() > $now + 60){
//            $this->apiReturnErr(lang('invalid_request'),ErrorCode::Invalid_Parameter);
//        }

        try{

            //2. 验签
            if(!$this->algInstance->verify_sign($this->alParams->getSign(),$this->alParams)){
                $this->apiReturnErr(lang('err_sign'));
            }
            
            //3. 数据解密
            $this->decrypt_data                 = [];
            $this->decrypt_data                 = $this->alParams->getResponseParams();
            $this->decrypt_data['api_ver']      = $this->alParams->getApiVer();
            $this->decrypt_data['client_id']    = $this->alParams->getClientId();
            $this->decrypt_data['client_secret']= $this->alParams->getClientSecret();
            $this->decrypt_data['lang']         = $this->lang;
            $this->decrypt_data['app_version']  = $this->app_version;
            $this->decrypt_data['app_type']     = $this->app_type;
            $data = $this->algInstance->decryptData($this->alParams->getData());
            if(is_array($data)){
                foreach($data as $key=>$vo){
                    $this->decrypt_data['_data_'.$key] = $vo;
                }
            }
        }catch (Exception $e){

            $this->apiReturnErr(ExceptionHelper::getErrorString($e));

        }

    }

    /**
     * 获取登录会话id
     * @return string
     */
    private function getLoginSId(){

        $login_s_id = isset($_GET['s_id']) ? $_GET['s_id'] : "" ;

        if(empty($login_s_id)){
            $login_s_id = isset($this->decrypt_data['_data_s_id'])?($this->decrypt_data['_data_s_id']):"";
        }

        return $login_s_id;
    }

    /**
     * 获取接口模块名称
     * 1. 用于未来对接口进行业务拆分、按使用场景进行拆分  比如针对第三方的接口、针对PC的接口
     * @return string
     */
    private function getModuleName(){

        $module_name = isset($_GET['m']) ? $_GET['m'] : "" ;

        if(empty($module_name)){
            $module_name = isset($this->decrypt_data['_data_m'])?($this->decrypt_data['_data_m']):"";
        }

        if(empty($module_name)) $module_name = "default";
        return $module_name;
    }

    /**
     *  获取用户UID
     *
     */
    private function getUid(){

        $uid = isset($this->decrypt_data['uid']) ? $this->decrypt_data['uid']:0;

        if(empty($uid)){
            $uid = isset($this->decrypt_data['_data_uid'])?$this->decrypt_data['_data_uid']:0;
        }
        $uid = intval($uid);

        return $uid;
    }
}
