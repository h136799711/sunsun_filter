<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\weixin\controller;
use Admin\Api\AngelRoleApi;
use Admin\Api\ConfigApi;
use Admin\Api\PartnerRoleApi;
use Admin\Api\RoleApi;

use app\src\system\logic\ConfigLogic;
use app\weixin\Api\WeixinLogic;
use Think\Controller;
use app\weixin\Api\WxaccountApi;
use Weixin\Api\WxuserApi;
use app\wxapp\controller\BaseController;
//use Admin\Api\NewmemberApi;
use app\src\user\action\LoginAction;
use app\src\user\logic\UcenterMemberLogic;
use app\src\user\logic\MemberLogic;
use app\src\user\logic\MemberConfigLogic;
use app\src\user\action\RegisterAction;
class Home extends  Base {

    protected $userinfo;
    protected $address;
    protected $newmember;
    protected $role;
    protected $wxaccount;
    protected $wxapi;
    protected $openid;
    protected $themeType;

    protected function _initialize() {

        parent::_initialize();

        header("X-AUTHOR:ITBOYE.COM");
        // 获取配置
        $this->getConfig();
        header('content-type:text/html;charset=utf-8;');
        if (!defined('APP_VERSION')) {
            //定义版本
            if (defined("APP_DEBUG") && APP_DEBUG) {
                define("APP_VERSION", time());
            } else {
                define("APP_VERSION", config('APP_VERSION'));
            }
        }
        //		config('SHOW_PAGE_TRACE', false);//设置不显示trace
        $this->refreshWxaccount();//获取相应公众账号的信息从数据库当中
        //		$debug = true;
        $debug = false;

        if ($debug) {
            $this->getDebugUser();
        } else {
            //$url = getCurrentURL();
            $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $this->getWxuser($url);
        }

        if (empty($this->userinfo) || $this->userinfo['subscribed'] == 0) {
            $this->display("Error:please_subscribe");
            exit();
        }


        $this->assign('avatar', $this->userinfo['avatar']);
        $this->assign('nickname', $this->userinfo['nickname']);
        $this->assign('money', $this->userinfo['money']);
        $this->assign('uuid', $this->userinfo['id']);
        $map['wx_id'] = $this->userinfo['id'];
        //获得默认地址防止缓存
        //暂时关闭从用户表中取数据
//        $addressres = apiCall(WxuserApi::GET_INFO,array(array('id'=>$this->userinfo['id'])));
//        if($addressres['status']){
//            $this->address = $addressres['info']['default_address'];
//        }else{
//            $this->error('获取默认地址');
//        }
//        $new_member = apiCall(NewmemberApi::GET_INFO,array($map));
//        if($new_member['status']){
//            $this->newmember = $new_member['info'];
//            $this->assign('points',$new_member['info']['points']);//积分
//            $this->assign('store_money',$new_member['info']['store_money']);
//            if($new_member['info']['vip_type'] == 1){
//                $rolemap['id'] = $new_member['info']['role_grade'];
//                $roleres = apiCall(RoleApi::GET_INFO,array($rolemap));
//                if($roleres['status']){
//                    $this->role = $roleres['info'];
//                }
//                //黄金会员有累计消费控制这里会与其他会员有点区别
//                if($new_member['info']['get_stock_money'] >= $roleres['info']['cumulative']){
//                    slog($new_member['info']['sham_share'] <= ($roleres['info']['cumulative_all_number']-$roleres['info']['cumulative_number']));
//                    if($new_member['info']['sham_share'] <= ($roleres['info']['cumulative_all_number']-$roleres['info']['cumulative_number'])){
//                        $num = floor(($new_member['info']['get_stock_money'] - $roleres['info']['cumulative'])/$roleres['info']['cumulative_money']);
//                        $stock_add = $num*$roleres['info']['cumulative_number']+$new_member['info']['sham_share'];
//                        if($stock_add > $roleres['info']['cumulative_all_number']){
//                            $stock_add = $roleres['info']['cumulative_all_number'];
//                        }
//                        $gsave = array(
//                            'get_stock_money'=>$new_member['info']['get_stock_money']-$num*$roleres['info']['cumulative_money'],
//                            'sham_share'=>$stock_add
//                        );
//                        $gres = apiCall(NewmemberApi::SAVE,array($map,$gsave));
//                    }
//                }
//            }
//
//            if($new_member['info']['vip_type'] == 2){
//                $rolemap['id'] = $new_member['info']['role_grade'];
//                $roleres = apiCall(AngelRoleApi::GET_INFO,array($rolemap));
//                if($roleres['status']){
//                    $this->role = $roleres['info'];
//                }
//            }
//            if($new_member['info']['vip_type'] == 3){
//                $rolemap['id'] = $new_member['info']['role_grade'];
//                $roleres = apiCall(PartnerRoleApi::GET_INFO,array($rolemap));
//                if($roleres['status']){
//                    $this->role = $roleres['info'];
//                }
//            }
//        }

    }

    //获取测试用户信息，用于PC端测试使用
    private function getDebugUser() {
        $this->userinfo = array(
            'id'           => 10,
            'uid'          => 236,
            'openid'       => 'oJz-Ks7mwh0CadiADLlAWfjE7vvw',
            'nickname'     => '老胖子何必都',
            'avatar'       => 'http://wx.qlogo.cn/mmopen/An6TFzHNImPecEhl1R3UWd26LlC1mvVgyhdh2KGCOb0yjQ4JNQnOicG2ysaKojzusSO9R3RE55Exq0lYKpVr3RRArU0u7kgjR/0',
            'score'        => 0,
            'wxaccount_id' => 5,
            'exp'          => 100,
            'groupid'      => 11,
            'subscribed'   => 1,
        );

        $this->openid = "oJz-Ks7mwh0CadiADLlAWfjE7vvw";
    }

    public function getWxuser($url) {
        $this->userinfo = null;
        //判断用户是否已经登录

        if (session("?global_user")) {
            $this->userinfo = session("global_user");//这里读入的是数组？
            $this->openid   = $this->userinfo['openid'];
        }
        if (!is_array($this->userinfo)) {
            $code  = input('get.code', '');
            $state = input('get.state', '');
            if (empty($code) && empty($state)) {
                $redirect = $this->wxapi->getOAuth2BaseURL($url, 'HomeIndexOpenid');
                $this->redirect($redirect);
            }
            if ($state == 'HomeIndexOpenid') {
                $accessToken  = $this->wxapi->getOAuth2AccessToken($code);
                $this->openid = $accessToken['openid'];
                $result       = $this->wxapi->webGetUserInfo($accessToken['openid'], $accessToken['access_token']);
                if ($result['status']) {
                    $this->refreshWxuser($result['info']);
                } else {
                    $this->error($result['info']);
                }
            }
        } else {
            //如果已经登录的情况下，是否登录后台系统
            $user_Info=session('user_Info');
            if(empty($user_Info)){
            $user_info=$this->getmember($this->userinfo);
            }
        }
    }

    /**
     * 刷新粉丝信息
     */
    private function refreshWxuser($userinfo) {
        $wxuser = array();
        $uid    = $this->wxaccount['uid'];
//		$wxuser['wxaccount_id'] = intval($this -> wxaccount['id']);
        $wxuser['nickname'] = $userinfo['nickname'];
        $wxuser['province'] = $userinfo['province'];
        $wxuser['country']  = $userinfo['country'];
        $wxuser['city']     = $userinfo['city'];
        $wxuser['sex']      = $userinfo['sex'];
        $wxuser['avatar']   = $userinfo['headimgurl'];
        if (!empty($userinfo['subscribe_time'])) {
            $wxuser['subscribe_time'] = $userinfo['subscribe_time'];
        }
        if (!empty($this->openid) && is_array($this->wxaccount)) {
            //传入的用户信息正确
            //刷新数据库信息
            $map = array('openid' => $this->openid, 'wxaccount_id' => $this->wxaccount['id']);
            //暂时关闭微信平台添加用户信息到数据库
            //$result = apiCall(WxuserApi::SAVE, array($map, $wxuser));
            $result = ['status' => true, 'info' => '暂时假添加用户'];
            if (!$result['status']) {
                LogRecord($result['info'], "商城控制器基类_刷新wxuser信息" . __LINE__);
            } else {
                //暂时
                //关闭从数据库中取出用户数据
                //$result = apiCall(WxuserApi::GET_INFO , array($map));
                $result = ['status' => true, 'info' => ['uid' => 1,
                                                        'id' => 1,
                                                        'money' => '1111',
                                                        'nickname' => '张健',
                                                        'avatar' => 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5iby7AxYzFwFIBI4q51MibdZPqZlYSggo7aJYY1FCYL2Uev4s4dw0ic89CA3hJTAn4gic5Nlic3OLicbrg/0',
                                                        'subscribed' => 1,
                                                        'openid' => '123123']];
                if ($result['status']) {
                    $result['info']['uid'] = $result['info']['id'];
                    $this->userinfo        = $result['info'];
                    session("global_user", $result['info']);

                    $this->getmember($result['info']);

                } else {
                    $this->error("个人用户信息获取失败！");
                }
            }

        } else {
            $this->error("系统参数错误！");
        }

    }



    /**
     * 判断是否在数据库中有此用户信息
     * 刷新或新增用户到数据库
     */

    private function getmember($userinfo){
        $wxopenid=$userinfo['openid'];
        $map['wxopenid']=$wxopenid;

        $memberconfig=(new MemberConfigLogic())->getInfo($map);

        if(!$memberconfig['status']) $this->apiReturnErr('数据查询有误');
        if(empty($memberconfig['info'])){

            //数据库中没有信息，则新增三个表
            $username = 'wx_'.md5($wxopenid);
            $password = substr($wxopenid,0,8);
            $entity = ['wxopenid' => $wxopenid, 'username' => $username, 'password' => $password];
            $entity['country'] = "+86";
            $entity['mobile'] = 'wx'.md5($username);
            $entity['reg_type'] = "";
            $result = (new RegisterAction())->register($entity);
            if(!$result['status']){
                return $this->apiReturnErr($result['info']);
            }
            //新增用户到数据库，并把信息加入数据库
            $uid=$result['info'];
            $m_member['nickname'] = $userinfo['nickname'];
            $refresh = (new MemberLogic())->save(['uid' => $uid], $m_member);
            $m_config['subscribed'] = $userinfo['subscribed'];
            $refresh              = (new MemberConfigLogic())->save(['uid' => $uid], $m_config);

            $result = (new LoginAction())->login($username,$password,"+86","","wx","");
            if($result['status'] && is_array($result['info'])){
                $user_Info = $result['info'];
                //$userInfo['wxapp_session_key'] = $session_key;
                //登录成功
                session('user_Info',$user_Info);
            }
        }else {


            $uid        = $memberconfig['info']['uid'];
            $memberinfo = (new MemberLogic())->getInfo(['uid' => $uid]);
            $result     = (new UcenterMemberLogic())->getInfo(['id' => $uid]);
            if (!$memberinfo['status']) $this->apiReturnErr('数据查询有误');
            if (!$result['status']) $this->apiReturnErr('用户信息读取错误');
            //数据库中有信息，则判断是否需要更新
            $nickname = $memberinfo['info']['nickname'];
            if ($nickname !== $userinfo['nickname']) {
                //若昵称与微信昵称不同，则更新
                $entity['nickname'] = $userinfo['nickname'];
            }
            if ($userinfo['avatar']) {
                $entity['head'] = $userinfo['avatar'];
            }
            if (!empty($entity)) {
                $refresh = (new MemberLogic())->save(['uid' => $uid], $entity);
            }
            //关注状态更新
            $entity     = [];
            $subscribed = $memberconfig['info']['subscribed'];
            if ($subscribed !== $userinfo['subscribed']) {
                $entity['subscribed'] = $userinfo['subscribed'];
                $refresh              = (new MemberConfigLogic())->save(['uid' => $uid], $entity);
            }

            $username = 'wx_'.md5($wxopenid);
            $password = substr($wxopenid,0,8);
            $result = (new LoginAction())->login($username,$password,"+86","","wx","");

            if ($result['status'] && is_array($result['info'])) {
                $user_Info = $result['info'];
                //$userInfo['wxapp_session_key'] = $session_key;
                //登录成功

                session('user_Info',$user_Info);

            }else{
                $this->apiReturnErr('后台登录失败');
            }
        }

    }





    /**
     * 刷新
     */
    private function refreshWxaccount() {
        $id = input('get.storeid', '');
        if (!empty($id)) {//这里的变量是否有问题
            session("storeid", $id);
        } elseif (session("?storeid")) {
            $id = session("storeid");
        }else{
            $id = input('post.storeid', '');
        }

        if(empty($id)){
            $id = '6';
        }
        //暂时关闭数据库获取微信信息，用模拟的方式
//        //$result = apiCall(WxaccountApi::GET_INFO, array( array('id' => $id)));
//        $result=(new WxaccountApi())->getInfo(['id'=>$id]);
        $account=['id'=>1,
                  'appid'=>'wx58fe237b1746d7b0',
                  'appsecret'=>'5da0ee40096800c6dab7339fa300ff64',
                  'encodingAESKey'=>'nh11jeo9ddcnx8w8opdm5ht2a8at8o8qn25ygx71zgj',
                  'encodingaeskey'=>'nh11jeo9ddcnx8w8opdm5ht2a8at8o8qn25ygx71zgj',
                  'uid'=>4,
                  'token'=>'pvifkmrw1476152475'];
        $result=['status'=>true,'info'=>$account];

        if ($result['status'] && is_array($result['info'])) {
            $this -> wxaccount = $result['info'];
            $this -> wxapi = new WeixinLogic($this -> wxaccount['appid'], $this -> wxaccount['appsecret']);
        } else {
            exit("公众号信息获取失败，请重试！");
        }




    }

    /**
     * 从数据库中取得配置信息
     */
    protected function getConfig() {
        $config = cache('config_' . session_id() . '_' . session("uid"));

        if ($config === false) {
            $map = array();
            $fields = 'type,name,value';
            //$result = apiCall(ConfigApi::QUERY_NO_PAGING, array($map, false, $fields));
            $result=(new ConfigLogic())->queryNoPaging($map, false, $fields);
            if ($result['status']) {
                $config = array();
                if (is_array($result['info'])) {
                    foreach ($result['info'] as $value) {
                        $config[$value['name']] = $this -> parse($value['type'], $value['value']);
                    }
                }
                //缓存配置300秒
                cache("config_" . session_id() . '_' . session("uid"), $config, 300);
            } else {
                LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
                $this -> error($result['info']);
            }
        }
        config($config);
    }

    /**
     * 根据配置类型解析配置
     * @param  integer $type 配置类型
     * @param  string $value 配置值
     * @return array|string
     */
    private static function parse($type, $value) {
        switch ($type) {
            case 3 :
                //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }


}
