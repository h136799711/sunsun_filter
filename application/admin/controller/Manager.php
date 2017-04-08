<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-14
 * Time: 16:36
 */

namespace app\admin\controller;


use think\Request;

class Manager extends Admin
{

    //首页
    public function index(){
        if($this->isMobile){
            return $this->boye_display();
        }
        return $this->boye_display("learun");
    }

    //介绍
    public function about(){

        if($this->isMobile){
            return $this->boye_display();
        }
        return $this->boye_display("learun");
    }

    //
    public function well(){
        $url = request()->scheme().'://'.request()->host();
        $this->assign('admin_url',$url);
        if($this->isMobile){
            return $this->boye_display();
        }
        return $this->boye_display("learun");
    }
}