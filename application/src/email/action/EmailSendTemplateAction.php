<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-01
 * Time: 14:52
 */

namespace app\src\email\action;



/**
 * Class EmailSendAction
 * 发送邮件:邮件发送模板
 * @package app\src\email\action
 */
class EmailSendTemplateAction extends EmailSendAction
{
    public function regSend($to_email,$code)
    {
        $title='注册激活邮件';//待修改 获取模板
        $content='激活码:'.$code ;//待修改 获取模板

       $result = parent::send($to_email,$title,$content);
        return $result;
    }

}