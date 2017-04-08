<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 16:49
 */

namespace app\admin\controller;

use app\src\suggest\logic\SuggestLogic;
/**
 * Class SunsunFilterVat
 * 过滤桶设备
 * @package app\admin\controller
 */
class Suggest extends Admin
{
    public function index(){


        $map = array();
        $params =false;
        $uid   = $this->_param('uid', 0);
        $startdatetime = $this->_param('startdatetime');
        $startdatetime = toUnixTimestamp($startdatetime);
        $enddatetime   = $this->_param('enddatetime');
        $enddatetime   = toUnixTimestamp($enddatetime);

        if(!empty($startdatetime) && !empty($enddatetime)){
            if($startdatetime === FALSE || $enddatetime === FALSE){
                $params = array('startdatetime' =>$startdatetime, 'enddatetime' =>$enddatetime);
                $map['create_time'] = array( array('EGT', $startdatetime), array('ELT', $enddatetime), 'and');
                $startdatetime = toDatetime( $startdatetime);
                $enddatetime   = toDatetime( $enddatetime);
            }else{
                $params = array('startdatetime' => $startdatetime, 'enddatetime' =>$enddatetime);
                $map['create_time'] = array( array('EGT', $startdatetime), array('ELT', $enddatetime), 'and');
                $startdatetime = toDatetime( $startdatetime);
                $enddatetime   = toDatetime( $enddatetime);
            }
        }

        if (!empty($did)){
            $map['uid'] = $uid;
            $params['uid'] = $uid;

        }
        $p=$this->_param('p', 0);
        $page = array('curpage' =>$p , 'size' => config('LIST_ROWS'));
        $order = " create_time desc ";
        $result = (new SuggestLogic())->queryWithPagingHtml($map,$page,$order,$params);
        if($result['status']){
            $params['p'] = $p;
            $this -> assign('params',$params);
            $this -> assign('uid', $uid);
            $this -> assign('startdatetime', $startdatetime);
            $this -> assign('enddatetime', $enddatetime);
            $this->assign('show',$result['info']['show']);
            $this->assign('list',$result['info']['list']);
            return $this->boye_display();
        }else {
            $this -> error($result['info']);
        }
    }
}