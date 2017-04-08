<?php

/**
 * warn: 往这个配置文件增加东西都需要通知到所有参与开发的人员
 * @author hebidu <email:346551990@qq.com>
 */

define("ITBOYE_CDN", "http://devsunsun.cdn.8raw.com");

return [

    // 默认时区
    'default_timezone'       => 'Etc/GMT',

    /* 文件上传相关配置 */
    'user_file_upload' => [
//上传公用配置
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 500*1024, //上传的文件大小限制 (不大于0或者不填-不做限制)
        'exts'     => 'bin,doc,txt', //必填,允许上传的文件
        // 'autoSub'  => true, //自动子目录保存文件
        'subName'  => ['date', 'Ymd'], //必填,子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => '../upload/user_file/', //必填,保存根路径
        'replace'  => true,//存在同名是否覆盖
        'hash'     => true,//是否生成hash编码
    ],
    //图片上传相关配置（文件上传类配置）
    'picture_upload_driver'=>'local',

    'by_api_config'=>[
        'client_id'=>'by571846d03009e1',
        'client_secret'=>'964561983083ac622f03389051f112e5',
        'api_url'=>'http://devsunsun.8raw.com/index.php',
        'debug'=> false
    ],
    // 应用模式状态
    //地址配置
    'file_download_url'=>'http://devsunsun.8raw.com/index.php/user_file/download?id=',
    'site_url'=>'http://devsunsun.8raw.com',
    'api_url'=>'http://devsunsun.8raw.com/index.php',
    'avatar_url'=>'http://devsunsun.8raw.com/index.php/picture/avatar',
    'picture_url'=>'http://devsunsun.8raw.com/index.php/picture/index?id=',
    'file_curl_upload_url'=>'http://devsunsun.8raw.com/index.php/file/curl_upload',
    'user_file_curl_upload_url'=>'http://devsunsun.8raw.com/index.php/user_file/curl_upload',
    'upload_path'=>'http://devsunsun.8raw.com/',

    //百度地图ak
    'baidu_map_ak' =>'NB4fAMqntPrs1RSGkTXBzjK9FVCMx9ix',//300w/d
    //数据字典
    'datatree'=>[
        'repair_type' =>6167, //维修类型
        'vehicle_type'=>6170, //车辆类型
        'worker_skill' =>6166, //技工技能
        "account_type" =>6178, //提现账号类型
        'STORE_TYPE' => 2,
        'GOODSUNIT' =>37,           //计算单位
        'COUNTRY' =>35,             //国家
        'PRODUCT_MAIN_IMG' => 6015, //商品主图
        'PRODUCT_SHOW_IMG' => 6016, //商品轮播图
        'WXPRODUCTGROUP' => 13,     //商品分组
        'BANNERS_TYPE' => 17,       //Banners类别

        'SHOP_INDEX_BANNERS'=>18, //商城首页轮播图片

        'RED_ENVOLOPE_TYPE'=>6018,//红包类型
        'RED_TEN_PERCENT'=>6021,//支付返10%红包

        'COUPON_TYPE'=>6132,//优惠券类型

        'POST_CATEGORY' => 21,//文章分类
        'HELP_POST_CATEGORY' => 6081,//帮助中心分类
        'HELP_APP_POST_CATEGORY' => 6165, //APP帮助中心文章分类

        'BBS_BANNERS_TYPE' => 6056,//论坛Banners类别
        'BANNERS_URL_TYPE'=>6069,//banners跳转链接类型
        'BANNER_SCORESHOP_INDEX'=>6137,//积分商城首页轮播

        'QUANTITY_SALE_OUT'=>6076, //库存变动类型，卖出
        'QUANTITY_TYPE'=>6186,//库存变动类型

        'WALLET_PAY_GOODS'  => 6148, //余额支付商品
        'WALLET_PAY_SGOODS' => 6149, //余额支付积分商品
        'WALLET_WITHDRAW'   =>32,//余额提现
        'WALLET_AFTERSALE'  =>6157,//余额提现
    ],
    //验证码配置
    'code_cfg'=>[
        'type'=>'local', //local:本地弹窗 qcloud: 腾讯云 juhe: 聚合
        'extra'=>[
            //腾讯云配置
//            'sdk_app_id'=>"1400018532",
//            "app_key"=>"18d087393ef7df76214d5f6ec087a5ba"
            //聚合配置
            "key"=>"b771aa8f615679f52990ce44ad2d9042"
        ]
    ],

    //阿里百川
    'alibaichuan_cfg'=>[
        'is_debug'   => true,//是否测试
        'app_key'    => '23500185',
        'app_secret' => 'b7f5a4c77e7e91f5266d1f9ea7468874',
    ],
    //支持的支付方式
    'app_support_payways'=>[
        ['name'=>'支付宝','type'=>1,'desc'=>'需要手机安装支付宝'],
        ['name'=>'Paypal','type'=>2,'desc'=>'支持paypal'],
        ['name'=>'微信支付','type'=>3,'desc'=>'需要手机安装微信'],
        ['name'=>'余额支付','type'=>4,'desc'=>'钱包余额支付'],
    ],

    //多语言支持
    'lang_support'=>[
        ['name'=>'简体中文','value'=>'zh-cn'],
        ['name'=>'한국','value'=>'ko'],
        ['name'=>'English','value'=>'en'],
        ['name'=>'Tiếng Việt','value'=>'vi'],
    ],

    //融丰支付配置
//    'rf_pay_config'=>[
//        //接口地址
//        'api_url'=> "http://api.ktb.wujieapp.net",
//        //
//        'org_no'=> "99999999",
//        'mer_no'=> "101607256868749",
//        //
//        'key'=> "bea91d7d61ecd36fcabfd4303c75a06f",
//        //rsa私钥 base64形式
//        'pem_path'=> "/www/wwwroot/api.guannan.8raw.com/application/src/rfpay/pem/base64.pem",
//        //订单创建成功后的回调地址
//        "no_card_order_backUrl"=>"http://api.ihomebank.com/index.php/rfpay"
//    ],

    // 加密salt定义
    'security_salt'=> [
        'password'=>'itboyep;[230',
    ],

    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用调试模式
    'app_debug'              => true,

    // 应用命名空间
    'app_namespace'          => 'app',
    // 应用Trace
    'app_trace'              => false,
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'json',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 是否开启多语言
    'lang_switch_on'         => true,
    // 支持的语言列表
    'lang_list'     => ['zh-cn','en'],
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common','src','domain'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'test',
        // 日志保存目录
        //'path'  => LOG_PATH,
        // 日志记录级别
        //'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => 'global_',
        // 缓存有效期 0表示永久缓存
        'expire' => 24*3600,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    //mysql session配置
    'session'                => [
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => 'itboye_sid',
        // SESSION 前缀
        'prefix'         => 'itboye',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
//        'type'              => 'mysql', // 驱动方式 支持redis memcache memcached
//        'auto_start'        => true,        // 是否自动开启 SESSION
//        // Session驱动设置
//        'session_expire'    =>  3600,        // Session有效期 单位：秒
//        'session_prefix'    => 'itboye_',    // Session前缀
//        'table_name'        => 'common_session',   // 表名（包含表前缀）
//        'var_session_id'    => 'itboye_sid', //会话id
//        'database'          =>  [
//            'hostname'  => '121.40.52.122',     // 服务器地址
//            'database'  => 'itboye_hutou',         // 数据库名
//            'username'  => 'itboye_te',         // 用户名
//            'password'  => 'itboye456',    // 密码
//            'hostport'  => '3306',          // 端口
//            'prefix'    => '',            // 表前缀（默认为空）
//            'charset'   => 'utf8',          // 数据库编码
//        ]
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],

    //队列
    'queue'=>[
        'type'=>'database', //驱动类型，可选择 sync(默认):同步执行，database:数据库驱动,redis:Redis驱动,topthink:Topthink驱动
        //或其他自定义的完整的类名
        'table' => 'queue_jobs'
    ],

    /* 图片上传相关配置 */
    'user_picture_upload' => [
//上传公用配置
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 500*1024, //上传的文件大小限制 (不大于0或者不填-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //必填,允许上传的文件
        // 'autoSub'  => true, //自动子目录保存文件
        'subName'  => ['date', 'Ymd'], //必填,子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './upload/user_picture/', //必填,保存根路径

//新版上传配置
        // 'house_rate' => [4,3],//房源图片比例4:3

//一下为兼容curl_upload的老版配置
        // 'savePath' => '',
        //curl使用 - 保存路径 eg: '1/'
        // 'saveName' => ['uniqid', ''], //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        // 'saveExt'  => '',//文件保存后缀，空则使用原后缀
        'replace'  => true,//存在同名是否覆盖
        'hash'     => true,//是否生成hash编码
    ],
    //图片上传相关配置（文件上传类配置）
    'picture_upload_driver'=>'local',

    //阿里百川
    // 'ALBAICHUAN_CFG'=>[
    //     'is_debug'   => true,//是否测试
    //     'app_key'    => '23456139',
    //     'app_secret' => '4647cb9e09046b8ef8e56c5aa5f95a61',
    // ]
];
