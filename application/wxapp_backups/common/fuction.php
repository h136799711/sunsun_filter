<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

/**
 * 插件调用
 *
 */
function pluginCall($pluginname,$vars){
    return apiCall('Weixin/'.$pluginname.'/process', $vars,"Plugin");
}

//===============================================================



/**
 * 获取当前完整url
 */
function getCurrentURL(){
    $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    return $url;
}
