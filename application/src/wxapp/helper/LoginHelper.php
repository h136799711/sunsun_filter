<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-06
 * Time: 11:23
 */

namespace app\src\wxapp\helper;


class LoginHelper
{
    public static function getSessionKey($appId,$appSecret,$jsCode){
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appId&secret=$appSecret&js_code=$jsCode&grant_type=authorization_code";
        $result = self::curl($url,[]);
        return $result;
    }

    static  function curl($url,$data){
        $ch     = curl_init();
        $header = ['Accept-Charset'=>"utf-8"];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.64 Safari/537.36'); //chrome46/mac
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $error = curl_errno($ch);

        if($error){
            return ['status' => false, 'info' => $error];
        }else{

            $js = json_decode($tmpInfo,true);
            if(is_null($js)){
                $js = "$tmpInfo";
            }
            return ['status' => true, 'info' => $js];
        }
    }
}