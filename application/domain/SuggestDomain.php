<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-03
 * Time: 15:32
 */

namespace app\domain;
use app\src\suggest\action\SuggestAction;


/**
 * Class SuggestDomain
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\domain
 */
class SuggestDomain extends BaseDomain
{
    /**
     * 意见反馈添加接口
     * @author hebidu <email:346551990@qq.com>
     */
    public function add(){
        $uid        = $this->_post('uid',0,lang('uid_need'));

        $device_type   = $this->_post('device_type','SXX');
        $text   = $this->_post('text','');
        $email = $this->_post('email',0);
        $tel  = $this->_post('tel',0);
        $ip = get_client_ip();

        $name = $this->_post('name','');
        $time=time();
        $entity=array(
            'uid'=>$uid,
            'text'=>$text,
            'email'=>$email,
            'tel'=>$tel,
            'IP'=>$ip,
            'name'=>$name,
            'status'=>1,
            'process_status'=>0,
            'create_time'=>$time,
            'update_time'=>$time,
            'device_type'=>$device_type
        );
        $suggest = new SuggestAction();
        $result = $suggest->add($entity);

        $this->exitWhenError($result,true);
    }


}