<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */
namespace app\src\admin\controller;

use think\Controller;
use think\Exception;
use think\Request;
use think\Session;

class BaseController extends Controller {

    protected $session_id;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        session("?session_id");
        $this->session_id = session_id();
        if(empty($this->session_id)){
            throw  new Exception("Session 未初始化");
        }
    }

    protected $seo = array(
			'title'=>'',
			'keywords'=>'',
			'description'=>'',
	);
	
	protected $cfg = array(
			'owner'=>'',
			'theme'=>'simplex'
	);

    public function assignVars($seo=array('title'=>'标题','keywords'=>'关键词','description'=>'描述',),	$cfg=array('owner'=>'杭州博也网络科技有限公司'))
    {
        $this->_assignVars($seo);
    }
        /*
         * Seo 配置
         * */
	public function _assignVars($seo=array('title'=>'标题','keywords'=>'关键词','description'=>'描述',),	$cfg=array('owner'=>'杭州博也网络科技有限公司')){
		$this->seo = array_merge($this->seo,$seo);
		$this->cfg = array_merge($this->cfg,$cfg);
		
		$this->assign("seo",$this->seo);
		$this->assign("cfg",$this->cfg);
	}
	/**
	 * 赋值页面标题值
	 */
	public function assignTitle($title){
		$this->seo = array_merge($this->seo,array('title'=>$title));
		$this->assign("seo",$this->seo);
	}
	
	//初始化
	protected function _initialize(){
		//设置程序版本
		$this->_assignVars();
		$this->_defined();
	}

	protected function _defined(){
        if(!defined("CONTROLLER_NAME")){
            define("CONTROLLER_NAME", Request::instance()->controller());
        }

        if(!defined("ACTION_NAME")){
            define("ACTION_NAME", Request::instance()->action());
        }

        if(!defined("IS_POST")){
            define("IS_POST", Request::instance()->isPost());
        }

        if(!defined("IS_GET")){
            define("IS_GET", Request::instance()->isGet());
        }

        if(!defined("IS_AJAX")){
            define("IS_AJAX", Request::instance()->isAjax());
        }
    }

	/* 空操作，用于输出404页面 */
    protected function _empty() {
    	header('HTTP/1.1 404 Not Found'); 
		header("status: 404 Not Found");     	
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		if(!defined('DEBUG')){
			header('Location: '.__ROOT__. '/public/404.html');
		}else{
			echo '{"status": "404","msg": "resource not found!"}';
			exit();
		}
    }



}
