<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-22
 * Time: 14:05
 */

namespace app\callback\controller;


use think\controller\Rest;

class Sunsun extends Rest
{
    public function index(){

        return $this->response(['status'=>1,'info'=>'操作成功'],"json");
    }
}