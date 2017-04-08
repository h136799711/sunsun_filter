<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-29
 * Time: 11:44
 */

namespace app\mobile\controller;


use app\mobile\helper\MobileConfigHelper;
use app\src\admin\controller\BaseController;
use think\Request;

/**
 * mobile端基类控制器
 * Class MobileController
 * @package app\mobile\controller
 */
abstract class MobileController  extends BaseController
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->assign('nav', '');
        $this->initConfig();
    }

    public function _param($key,$default='',$emptyErrMsg=''){

        $value = Request::instance()->param($key,$default);

        if($default == $value && !empty($emptyErrMsg)){
            $this->error($emptyErrMsg);
        }

        return $value;
    }

    protected function assignNav($nav = '首页'){
        $this->assign('nav', $nav);
    }

    private function initConfig(){
        MobileConfigHelper::init();
    }


}