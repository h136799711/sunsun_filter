<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-01
 * Time: 14:52
 */

namespace app\src\email\action;


use app\src\base\action\BaseAction;
use app\src\system\logic\ConfigLogic;
use app\src\base\helper\ResultHelper;
/**
 * Class EmailSendAction
 * 发送邮件
 * @package app\src\email\action
 */
class EmailSendAction extends BaseAction
{
    public function send($to_email,$title,$content){

        if(empty($to_email)){
            return ResultHelper::error('无效的收件邮箱');
        }

        vendor('phpmailer/phpmailer/PHPMailerAutoload');
        $result = (new ConfigLogic())->queryNoPaging(array('group' => 8));

        if(is_array($result['info'])){
            foreach ($result['info'] as $v) {
               $emailinfo[$v['name']] =$v['value'];
            }
        }else{
            $emailinfo = array(
                "smtp_username"=>"postmaster@itboye.com",
                "smtp_port"=>"25",
                "smtp_send_email"=>"postmaster@itboye.com",
                "smtp_sender_name" => "杭州博也网络科技有限公司",
                "smtp_password"=>"hbdHBD136799711",
                "smtp_host"=>"smtp.itboye.com"
            );
        }
        //Create a new PHPMailer instance
        $mail = new \PHPMailer();
        //SMTP 配置
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->SMTPDebug = 0;
        $mail->Host = $emailinfo['smtp_host'];  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $emailinfo['smtp_username'];                 // SMTP username
        $mail->Password = $emailinfo['smtp_password'];;                           // SMTP password
//        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = $emailinfo['smtp_port'];                                    // TCP port to connect to

        $mail->CharSet = "UTF-8";
        //发件人
        $mail->setFrom($emailinfo['smtp_username'], $emailinfo['smtp_sender_name']);
        //Set an alternative reply-to address
        $mail->addReplyTo($emailinfo['smtp_username'], $emailinfo['smtp_sender_name']);
        //收件人
        $mail->addAddress($to_email, $to_email);
        //Set the subject line
        $mail->Subject = $title;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
//        $mail->msgHTML("html=>'<b>best</b>'");
        //Replace the plain text body with one created manually
        $mail->AltBody = '邮件内容(可选)';
        $mail->Body = $content;// "邮件内容";
        $mail->isHTML();
        //Attach an image file
//        $mail->addAttachment('images/phpmailer_mini.png');
        //send the message, check for errors
        try{
            if (!$mail->send()) {
                return ResultHelper::error("Mailer Error: ");
            } else {
                return ResultHelper::success("Message sent!");
            }
        }catch (\Exception $exception){
            return ResultHelper::error("Mailer exception: " . $exception->getMessage());
        }
    }

}