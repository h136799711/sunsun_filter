<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/4/22
 * Time: 14:08
 */

namespace app\src\encrypt\des;

/**
 * DES
 * Class DesCrypt
 * @package app\vendor\Crypt
 */
class Des{


    public  static function encryptDesEcbPKCS5($input, $key)
    {
        $encode = openssl_encrypt($input,"des-ecb",$key);
        return $encode;
    }

    /**
     * 对明文信息进行加密
     * @param 内容|$content
     * @param 密钥|$key
     * @return 内容
     * @internal param string $salt
     */
    static public function encode($content,$key) {
        $encode = openssl_encrypt($content,"des-ecb",$key);
        return $encode;
    }

    /**
     * 对密文进行解密
     * @param $encode_content
     * @param $key string 密钥
     * @return string
     * @internal param $content
     */
    static public function decode($encode_content,$key) {
        if(empty($encode_content)){
            return "";
        }
        return trim(openssl_decrypt($encode_content,"des-ecb",$key));
    }


}