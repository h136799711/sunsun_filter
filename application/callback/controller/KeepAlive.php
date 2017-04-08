<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-23
 * Time: 17:02
 */

namespace app\callback\controller;


use think\controller\Rest;

class KeepAlive extends Rest
{
    public function index(){
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 2008 05:00:00 GMT");
        flush();
        set_time_limit(0);
//        header("Connection: Keep-Alive");
//        header("Proxy-Connection: Keep-Alive");

        $cnt = 1;
        while($cnt < 8){
            echo "当前次数 ".$cnt.'<br/>';
            $cnt++;
            ob_flush();
            flush();
            sleep(1);
        }
        exit;
    }
}