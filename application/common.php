<?php
/**
 * 公共函数库
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/4/17
 * Time: 16:12
 */

// 或者进行自动检测语言
\think\Lang::detect();

// 获取CDN图标 - country[60,107] service[35]
function getCdnIcon($type,$size=60,$name,$ext='png'){
    return ITBOYE_CDN.$type.DS.$size.DS.$name.'.'.$ext;
}

//return eg:
//{
// "status": 101,
// "message": "AK参数不存在"
// }
// {
// "status": 2,
// "msg": "Request Parameter Error: lack address or location",
// "results": []
// }
// {
// "status": 0,
// "result": {
//  "location": {
//      "lng": 120.29630620976407,
//      "lat": 30.35396811588703
//  },
//  "precise": 0,
//  "confidence": 30,
//  "level": "商圈"
// }
// }
function getAddressPos($address=''){
    $req = 'http://api.map.baidu.com/geocoder/v2/?address='.urlencode($address).'&output=json&ak='.config('baidu_map_ak');
    $r = json_decode(file_get_contents($req),true);
    if($r['status'] !== 0){
        return returnErr(isset($r['msg']) ? '非法地址:'.$r['msg'] : $r['message']);
    }
    return returnSuc($r['result']['location']);
}

//递归 - 改变null值
function fixNull($r,$replace=''){
    if(is_null($r)) {
        return $replace;
    }elseif(is_array($r)){
        foreach ($r as &$v) {
            // $v = is_null($v) ? $replace : $v;
            $v = fixNull($v,$replace);
        }
    }elseif(is_object($r) && method_exists($r,"toArray")){
        $r = $r->toArray();
        foreach ($r as &$v){
           // $v = is_null($v) ? $replace : $v;
           $v = fixNull($v,$replace);
        }
    // }elseif(is_string($r)){
    //     return is_null($r) ? $replace : $r;
    }
    return $r;
}

function sdb($table='',$pre=''){
    return \think\Db::table($table,$pre);
}
//db_object to array
function Obj2Arr($r,$key=false){
    $l = [];
    foreach ($r as $v) {
        $data = $v->getData();
        if($key) $l[$data[$key]] = $data;
        else $l[] = $data;
    }
    return $l;
}
//缓存键名
function getCacheKey($map=null,$pre='g'){
    $key = '_'.serialize($map);
    return $pre.$key;
}
//将数组的某个键作为索引key
function changeArrayKey($arr = null,$k='id'){
    $r = [];
    foreach ($arr as $v) {
        $r[$v[$k]] = $v;
    }
    return $r;
}
/**
 * 获取config中配置的数据字典的ID
 */
function getDtreeId($code) {
    return config("datatree." . $code);
}

function shalt($r,$msg='',$end=true){
    echo $msg  ? $msg.'<br/>':'';
    dump($r);
    $end && die();
}
/**
 * 生成假数据对象列表
 * eg:
 * $map = ['id'=>['numberBetween',[1,99]],'name'=>['firstName',[]]];
 * getFaker($map,rand(1,5));
 * return faker object list
 */
//require '../vendor/faker/autoload.php';
function getFaker(array $rules,$count=1){
    vendor('faker.autoload');
    $faker = \Faker\Factory::create('zh_CN');
    $r = [];
    for ($i=0; $i < $count; $i++) {
        $map = [];
        foreach ($rules as $k => $v) {
            $map[$k] = call_user_func_array([$faker,$v[0]], $v[1]);
        }
        $r[] = $map;
    }
    return $r;
}
/**
 * 记录日志，系统运行过程中可能产生的日志
 * Level取值如下：
 * EMERG 严重错误，导致系统崩溃无法使用
 * ALERT 警戒性错误， 必须被立即修改的错误
 * CRIT 临界值错误， 超过临界值的错误
 * WARN 警告性错误， 需要发出警告的错误
 * ERR 一般性错误
 * NOTICE 通知，程序可以运行但是还不够完美的错误
 * INFO 信息，程序输出信息
 * DEBUG 调试，用于调试信息
 * SQL SQL语句，该级别只在调试模式开启时有效
 */
function LogRecord($msg, $location, $level = 'ERR') {
    \think\Log::write($location . $msg, $level);
}

/**
 * 获取订单编号
 */
function getOrderid($uid){
    $rand = mt_rand(1000000, 9999999);
    $orderID = date("yzHis",time());
    return $orderID.$rand.get_36HEX($uid);
}

/**
 * 接口日志记录
 * @param $api_uri
 * @param $get
 * @param $post
 * @param $notes
 * @param bool $onlyDebug
 * @throws \think\Exception
 */
function addLog($api_uri,$get,$post,$notes,$onlyDebug=false,$from='',$content=''){

    if($onlyDebug && config('app_debug') == false){
        return ;
    }

    $model = db('ApiCallHis');

    if(is_array($get)){
        $get = json_encode($get);
    }
    if(is_array($post)){
        $post = json_encode($post);
    }

    $post    = is_null($post)?"null":$post;
    $get     = is_null($get)?"null":$get;
    $notes     = is_null($notes)?"null":$notes;
    $api_uri = empty($api_uri) ? "":$api_uri;


    $model->insert(array(
        'api_uri'=>$api_uri,
        'call_get_args'=>$get,
        'call_post_args'=>$post,
        "call_input"=>$content,
        'notes'=>$notes,
        'call_time'=>NOW_TIME,
        'call_from'=>$from,
    ));

}
/**
 * 接口日志记录
 * @param $api_uri
 * @param $get
 * @param $post
 * @param $notes
 * @param bool $onlyDebug
 * @throws \think\Exception
 */
function addHisLog($api_uri,$get,$post,$notes,$onlyDebug=false){

    if($onlyDebug && config('app_debug') == false){
        return ;
    }

    $model = db('ApiHistory');

    if(is_array($get)){
        $get = json_encode($get);
    }
    if(is_array($post)){
        $post = json_encode($post);
    }

    $post    = is_null($post)?"null":$post;
    $get     = is_null($get)?"null":$get;
    $api_uri = empty($api_uri)?"":$api_uri;

    $result = $model->create(array(
        'api_uri'=>$api_uri,
        'call_get_args'=>$get,
        'call_post_args'=>$post,
        'notes'=>$notes,
        'call_time'=>NOW_TIME,
    ));

    if($result){
        $model->add();
    }
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


if(!function_exists('think_ucenter_md5')){

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function think_ucenter_md5($str, $key = 'ThinkUCenter'){
    return '' === $str ? '' : md5(sha1($str) . $key);
}
}

/**
 * 密码加密
 * @param $str
 * @param string $key
 * @return string
 */
function itboye_ucenter_md5($str, $key = 'ITBOYE'){
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 (单位:秒)
 * @return string
 */
function think_ucenter_encrypt($data, $key, $expire = 0) {
    $key  = md5($key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char =  '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x=0;
        $char  .= substr($key, $x, 1);
        $x++;
    }
    $str = sprintf('%010d', $expire ? $expire + time() : 0);
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data,$i,1)) + (ord(substr($char,$i,1)))%256);
    }
    return str_replace('=', '', base64_encode($str));
}

/**
 * 系统解密方法
 * @param string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key  加密密钥
 * @return string
 */
function think_ucenter_decrypt($data, $key){
    $key    = md5($key);
    $x      = 0;
    $data   = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data   = substr($data, 10);
    if($expire > 0 && $expire < time()) {
        return '';
    }
    $len  = strlen($data);
    $l    = strlen($key);
    $char = $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char  .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}


/**
 * @desc  im:十进制数转换成三十六机制数
 * @param (int)$num 十进制数
 * @return bool|string
 */
function get_36HEX($num) {
    $num = intval($num);
    if ($num <= 0)
        return 0;
    $charArr = array("0","1","2","3","4","5","6","7","8","9",'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $char = '';
    do {
        $key = ($num - 1) % 36;
        $char= $charArr[$key] . $char;
        $num = floor(($num - $key) / 36);
    } while ($num > 0);
    return $char;
}

/**
 * 自定义语言变量
 * @param $str  字符串
 * @param $dif  分割符
 * @param $add  链接符
 * @return string is8n字符串
 * add by zhouhou
 */
function LL($str='',$dif=' ',$add = ''){
    return implode($add,array_map('lang',explode($dif, trim($str))));
}
/**
 * lang() alias 方法别名
 * @param [type] $name [description]
 * @param array  $vars [description]
 * @param string $lang [description]
 */
function L($name, $vars = [], $lang = '')
{
    return \think\Lang::get($name, $vars, $lang);
}
/**
 * 缺少参数函数别名
 * @Author
 * @DateTime 2016-12-13T10:20:27+0800
 * @param    [type]                   $name [description]
 */
function Llack($name){
    return lang('lack_parameter',["param"=>$name]);;
}
function Linvalid($name,$throw=false){
    $msg = lang('invalid_parameter',["param"=>$name]);
    if($throw){
        throw new \Exception(Linvalid("group_id"), \app\src\base\enum\ErrorCode::Invalid_Parameter);
    }
    return $msg;
}
function returnErr($msg,$trans=false){
    if($trans) \think\Db::rollback();
    return ['status'=>false,'info'=>$msg];
}
function returnSuc($data){
    return ['status'=>true,'info'=>$data];
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
//function get_nickname($uid = 0){
//    static $list;
//    if(!($uid && is_numeric($uid))){ //获取当前登录用户名
//        return session('global_user.username');
//    }
//
//    /* 获取缓存数据 */
//    if(empty($list)){
//        $list = cache('sys_user_nickname_list');
//    }
//
//    /* 查找用户信息 */
//    $key = "u{$uid}";
//    if(isset($list[$key])){ //已缓存，直接使用
//        $name = $list[$key];
//    } else { //调用接口获取用户信息
//        $result = apiCall(\app\system\api\MemberApi::GET_INFO,array(array("uid"=>$uid)));
//
//        if($result['status'] !== false && $result['info']['nickname'] ){
//            $nickname = $result['info']['nickname'];
//            $name = $list[$key] = $nickname;
//
//            /* 缓存用户 */
//            $count = count($list);
//            $max   = 1000;
//            while ($count-- > $max) {
//                array_shift($list);
//            }
//            cache('sys_user_nickname_list', $list);
//        } else {
//            $name = '';
//        }
//    }
//    return $name;
//}

/**
 * 时间戳格式化
 * @param int $time
 * @param string $format
 * @return string 完整的时间显示
 */
function time_format($time = NULL,$format='Y-m-d H:i'){
    $time = $time === NULL ? time() : intval($time);
    return date($format, $time);
}
/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 */
//function action_log($action = null, $model = null, $record_id = null, $user_id = null){
//
//    //参数检查
//    if(empty($action) || empty($model) || empty($record_id)){
//        return '参数不能为空';
//    }
//    if(empty($user_id)){
//        $user_id = is_login();
//    }
//
//    //查询行为,判断是否执行
//    $action_info = apiCall(\app\system\api\ActionApi::GET_INFO, [["name"=>$action]]);
//    if($action_info['status'] && is_array($action_info['info'])  && $action_info['info']['status'] != 1){
//
//        return '该行为被禁用或删除';
//    }
//    $action_info = $action_info['info'];
//    //插入行为日志
//    $data['action_id']      =   $action_info['id'];
//    $data['user_id']        =   $user_id;
//    $data['action_ip']      =   ip2long(get_client_ip());
//    $data['model']          =   $model;
//    $data['record_id']      =   $record_id;
//    $data['create_time']    =   NOW_TIME;
//
//    //解析日志规则,生成日志备注
//    if(!empty($action_info['log'])){
//        if(preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)){//匹配[]，获取[]里的字符串
//            $log['user']    =   $user_id;
//            $log['record']  =   $record_id;
//            $log['model']   =   $model;
//            $log['time']    =   NOW_TIME;
//            $log['data']    =   ['user'=>$user_id,'model'=>$model,'record'=>$record_id,'time'=>NOW_TIME];
//            $replace = [];
//            foreach ($match[1] as $value){
//                $param = explode('|', $value);//分割字符串通过|
//
//                if(isset($param[1])){
//                    $replace[] = call_user_func($param[1],$log[$param[0]]);//调用函数
//                }else{
//                    $replace[] = $log[$param[0]];
//                }
//            }
//            $data['remark'] =   str_replace($match[0], $replace, $action_info['log']);
//        }else{
//            $data['remark'] =   $action_info['log'];
//        }
//    }else{
//        //未定义日志规则，记录操作url
//        $data['remark']     =   '操作url：'.$_SERVER['REQUEST_URI'];
//    }
//    $result = apiCall(\app\system\api\ActionLogApi::ADD, [$data]);
//
//    if(!$result['status']){
//        LogRecord("记录操作日志失败!", $result['info']);
//    }
//
//    if(!empty($action_info['rule'])){
//        //解析行为
//        $rules = parse_action($action, $user_id);
//
//        //执行行为
//        $res = execute_action($rules, $action_info['id'], $user_id);
//    }
//}
/**
 * 解析行为规则
 * 示例：table:member|field:score|condition:uid={$self} AND status>-1|rule:9-2+3+score*1/1|cycle:24|max:1;
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 *              field->要操作的字段；
 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int $self 替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 */
//function parse_action($action = null, $self){
//    if(empty($action)) return false;
//
//    //参数支持id或者name
//    if(is_numeric($action)){
//        $map = ['id'=>$action];
//    }else{
//        $map = ['name'=>$action];
//    }
//
//    //查询行为信息
//    $result = apiCall(\app\system\api\ActionApi::GET_INFO, [$map]);
//    if(!$result['status']) return false;
//
//    $info = $result['info'];
//    if(is_null($info) || $info['status'] != 1) return false;

    //解析规则:prefix:common_|table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
//    $rules = $info['rule'];
//    $rules = str_replace('{$self}', $self, $rules);
//    $rules = explode(';', $rules);
//    $return = [];
//    foreach ($rules as $key=>&$rule){
//        $rule = explode('|', $rule);
//        foreach ($rule as $k=>$fields){
//            $field = empty($fields) ? [] : explode(':', $fields);
//            if(!empty($field)){
//                $return[$key][$field[0]] = $field[1];
//            }
//        }
//暂时注释掉 login error
        //cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
        // if(!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])){
        //     unset($return[$key]['cycle'],$return[$key]['max']);
        // }
//    }
//
//    return $return;
//}

/**
 * 执行行为
 * @param array|bool $rules 解析后的规则数组
 * @param int $action_id 行为id
 * @param int $user_id 执行的用户id
 * @return bool false 失败 ， true 成功
 *
 */
//function execute_action($rules = false, $action_id = null, $user_id = null){
//
//    if(!$rules || empty($action_id) || empty($user_id)) return false;
//    $return = true;
//
//    foreach ($rules as $rule){
//
//        //检查执行周期
//        if(isset($rule['max'])){
//            $map = ['action_id'=>$action_id, 'user_id'=>$user_id];
//            if(isset($rule['cycle'])){//设置了cycle时
//                $map['create_time'] = ['gt', NOW_TIME - intval($rule['cycle']) * 3600];
//            }
//            //统计执行次数
//            $exec_count = db('ActionLog','common_')->where($map)->count();
//            if($exec_count > $rule['max']) continue;
//        }
//
//        $prefix = $rule['prefix'];
//        //执行数据库操作
//        if(empty($prefix)){
//            $Model = model(ucfirst($rule['table']));
//        }else{
//            $Model = db(ucfirst($rule['table']),$prefix);
//        }
//        $field = $rule['field'];
//        $res = $Model->where($rule['condition'])->setField($field, ['exp', $rule['rule']]);
//
//        if(!$res) $return = false;
//    }
//    return $return;
//}



/**
 * 小周的socketLog
 * @param $log
 * @param string $type
 * @param string $user
 */
function slog($log, $type = '', $user = 'xiao'){
    require '../vendor/xsocketlog/socketlog.php';
    $socketlog = new \xsocketlog\socketlog($user);
    $socketlog->send($log, $type);
}
/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 获取数据字典的ID
 * TODO: 考虑从数据库中获取
 */
function getDatatree($code) {
    return config("datatree." . $code);
}

/**
 * 获取图片地址
 * @param $id
 * @param int $size
 * @return string
 */
function getImgUrl($id,$size=0){
    return config('picture_url').$id.($size  ? '&size='.$size:'');
}
 /**
  * 模板helper - 图片
 */
function imgTag($id,$size=120,$click=false,$tooltip=false){
    $size = $size ? $size : 120;
    $str = "<img src='".getImgUrl($id,$size)."' ";
    $style = " style ='width:".$size."px;";
    if($click){//新窗口打开
        $str .= " onclick=\"javascript:window.open('".getImgUrl($id)."');\" alt='点击新窗口查看原图' ";
        $style .= "cursor:pointer;";
    }
    if($tooltip){ //bootstrap-tooltip
        $str .= " title='点击查看原图' data-toggle='tooltip' ";
     }
     return $str.$style."' />";
 }
/**
 * 模板helper - 图片
 * @Author
 * @DateTime 2017-01-06T11:26:05+0800
 * @param    [type]  $id      [图片id]
 * @param    integer $size    [显示大小]
 * @param    boolean $click   [是否点击打开新窗口显示原图]
 * @param    boolean $tooltip [bootstrap-tooltip 或 自定义tooltip]
 * @return   [type]  [description]
 */
function imgTooltip($id,$size=120,$click=false,$tooltip=false){
    $size = $size ? $size : 120;
    $str = "<img src='".getImgUrl($id,$size)."' ";
    $style = " style ='width:".$size."px;";

    if($tooltip){//bootstrap-tooltip
        if($click){
            //自带js打开
            $style .= "cursor:pointer;";
            $str .= " onclick=\"javascript:window.open('".getImgUrl($id)."');\"  alt='" .(is_string($click) ? $click : "点击新窗口查看原图") ."' ";
        }
        $str .= " title='" .(is_string($tooltip) ? $tooltip : "点击查看原图") ."' data-toggle='tooltip' ";
    }else{ //自定义tooltip
        if($click){
            //自定义js打开 - 见模板
            $str .= " class='img-click'  alt='" .(is_string($click) ? $click : "点击新窗口查看原图") ."' ";
            $style .= "cursor:pointer;";
        }
        $str .= " data-src='".getImgUrl($id)."' ";

    }
    return $str.$style."' />";
}
function w_datatree($parent,$hasChildren,$checkedID=0,$showCode=0){

    if($hasChildren){
        $map['parents'] = array('like','%'.$parent.',%');
        $result = (new \app\src\system\logic\DatatreeLogicV2())->queryNoPaging($map);
        $list = \app\src\admin\helper\MenuHelper::toFormatTree($result,'name','id','parentid',$parent);
    }else{
        $map = array('parentid'=>$parent);
        $list = (new \app\src\system\logic\DatatreeLogicV2())->queryNoPaging($map);
    }
    echo \think\View::instance()->fetch("default/Widget/datatree",['list'=>$list,'checkedID'=>$checkedID,'showCode'=>$showCode]);
}
function qw_datatree($parent,$hasChildren,$checkedID=0,$showCode=0){

    if($hasChildren){
        $map['parents'] = array('like','%'.$parent.',%');
        $result = (new \app\src\system\logic\DatatreeLogicV2())->queryNoPaging($map);
        $list = \app\src\admin\helper\MenuHelper::toFormatTree($result,'name','id','parentid',$parent);
    }else{
        $map = array('parentid'=>$parent);
        $list = (new \app\src\system\logic\DatatreeLogicV2())->queryNoPaging($map);
    }
    echo \think\View::instance()->fetch("default/Widget/quantitydatatree",['list'=>$list,'checkedID'=>$checkedID,'showCode'=>$showCode]);
}
/**
 * 返回数据状态的含义
 * @status $status 一个数字 -1,0,1,2,3 其它值都是未知
 * @return 描述字符串
 */
function getStatus($status) {
    $desc = '未知状态';
    switch($status) {
        case -1 :
            $desc = "已删除";
            break;
        case 0 :
            $desc = "禁用";
            break;
        case 1 :
            $desc = "正常";
            break;
        case 2 :
            $desc = "待审核";
            break;
        case 3 :
            $desc = "通过";
            break;
        case 4 :
            $desc = "不通过";
            break;
        default :
            break;
    }
    return $desc;
}

/**
 * 订单内的支付方式的描述
 * @param $pay_type
 * @return string
 */
function getPayType($pay_type){
    switch($pay_type){
        case 1:
            return "支付宝支付";
        case 2:
            return "微信支付";
        case 3:
            return "余额支付";
        default:
            return "在线支付";
    }

}

/**
 * 获取支付状态的文字描述
 */
function getPayStatus($status) {
    switch($status) {
        case \app\src\order\model\Orders::ORDER_PAID :
            return "已支付";
        case \app\src\order\model\Orders::ORDER_TOBE_PAID :
            return "待支付";
        case \app\src\order\model\Orders::ORDER_REFUND :
            return "已退款";
        case \app\src\order\model\Orders::ORDER_CASH_ON_DELIVERY :
            return "货到付款";
        default :
            return "未知";
    }
}
/**
 * 获取订单状态的文字描述
 */
function getOrderStatus($status) {

    switch($status) {
        case \app\src\order\model\Orders::ORDER_COMPLETED :
            return "已完成";
        case \app\src\order\model\Orders::ORDER_RETURNED :
            return "已退货";
        case \app\src\order\model\Orders::ORDER_SHIPPED :
            return "已发货";
        case \app\src\order\model\Orders::ORDER_TOBE_CONFIRMED :
            return "待确认";
        case \app\src\order\model\Orders::ORDER_TOBE_SHIPPED :
            return "待发货";
        case \app\src\order\model\Orders::ORDER_CANCEL :
            return "订单已关闭";
        case \app\src\order\model\Orders::ORDER_RECEIPT_OF_GOODS :
            return "已收货";
        case \app\src\order\model\Orders::ORDER_BACK :
            return "卖家退回";
        case \app\src\order\model\Orders::ORDER_RESENDS :
            return "正在退款";
        default :
            return "未知";
    }
}
/**
 * 获取订单评价状态的文字描述
 */
function getCommentStatus($status) {

    switch($status) {
        case \app\src\order\model\Orders::ORDER_TOBE_EVALUATE :
            return "待评价";
        case \app\src\order\model\Orders::ORDER_HUMAN_EVALUATED :
            return "已评价";
        case \app\src\order\model\Orders::ORDER_SYSTEM_EVALUATED :
            return "自动评价";
        default :
            return "未知";
    }
}
/**
 * 从session中取WxAccountID
 */
function getWxAccountID(){
    if(session("?wxaccountid")){
        return session("wxaccountid");
    }
    return -1;
}
/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pk
 * @param string $pid parent标记字段
 * @param string $child
 * @param int $root
 * @return array
 * @internal param string $level level标记字段
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}
/**
 * 如果操作失败则记录日志
 * @return array 格式：array('status'=>boolean,'info'=>'错误信息')
 * @author hebiduhebi@163.com
 */
function ifFailedLogRecord($result, $location) {
    if ($result['status'] === false) {
        Think\Log::write($location . $result['info'], 'ERR');
    }
}
/**
 * 获取插件类的类名
 * @var string $name 插件名
 * @return string
 */
function get_addon_class($name){
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}

/**
 * 单个数字变汉字
 * @param $x
 * @return mixed
 */
function n2c($x)
{
    $arr_n = array("日","一","二","三","四","五","六");
    return $arr_n[$x];
}

/**
 * 封装tp的url函数并添加ret_url参数
 * @param $uri
 * @param string $vars
 * @return string
 */
function byUrl($uri,$vars=''){
    return url($uri,$vars).'?ret_url='.__SELF__;
}

/**
 *
 * @param $time
 * @param string $format
 * @return false|string
 */
function toDatetime($time,$format='Y-m-d H:i:s'){

    $timezone = intval(cookie('timezone'));
    if($timezone >= -12 && $timezone <= 12){
        $time = $timezone * 3600 + $time;
    }
    return date($format,$time);
}

/**
 *
 * @param $time
 * @param string $format
 * @return false|string
 */
function toUnixTimestamp($strtime,$format='Y-m-d H:i:s'){
    $unixTimestamp = strtotime($strtime);
    $timezone = intval(cookie('timezone'));

    if($timezone >= -12 && $timezone <= 12){
        $unixTimestamp = $unixTimestamp - $timezone * 3600;
    }

    return $unixTimestamp;
}

function toTemperate($temp,$unit='°C'){
    return number_format($temp/10,1,".","").$unit;
}