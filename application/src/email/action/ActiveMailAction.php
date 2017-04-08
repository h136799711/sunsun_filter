<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-01
 * Time: 14:52
 */

namespace app\src\email\action;


use app\src\base\action\BaseAction;
use app\src\email\logic\ActiveMailLogic;
use app\src\user\logic\MemberConfigLogic;
/**
 * Class EmailSendAction
 * 发送邮件
 * @package app\src\email\action
 */
class ActiveMailAction extends BaseAction
{
    public function produceKey($email,$key)
    {

        $entity = array(
            'key' => $key,
            'email' => $email
        );
        $result = (new ActiveMailLogic())->add($entity);
        return $result;
    }
    public function checkActive($uid,$key){

        $map=array(
            'key'=>$key,
            'uid'=>$uid
        );
        $result = (new ActiveMailLogic())->getInfo($map);

        if($result['status']){
            $entity=array(
                'email_validate'=>1
            );
            $map=array(
                'uid'=>$uid
            );
            $result= (new MemberConfigLogic())->save($map,$entity);
           return $result;
        }
    }
}