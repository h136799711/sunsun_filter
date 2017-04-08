<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-10
 * Time: 10:14
 */

namespace app\src\kuaidiniao\helper;

//快递鸟申请的商户ID
defined('EBusinessID') or define('EBusinessID', '1275465');
//电商加密私钥，快递鸟提供，注意保管，不要泄漏
defined('AppKey') or define('AppKey', 'd6190524-c108-484c-97ff-47198aaba4cd');
//请求url
defined('ReqURL') or define('ReqURL', 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx');


class KdniaoHelper
{


    /**
     * Json方式 查询订单物流轨迹
     */
    public static  function getOrderTracesByJson($shipperCode, $logisticCode){
        $requestData= "{\"OrderCode\":\"\",\"ShipperCode\":\"".$shipperCode."\",\"LogisticCode\":\"".$logisticCode."\",\"SiteUrl\":\"".$_SERVER['HTTP_HOST']."\",\"App\":\"php52demo\"}";
        $datas = array(
            'EBusinessID' => EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );

        $datas['DataSign'] =  self::encrypt($requestData, AppKey);
        $result = self::sendPost(ReqURL, $datas);
        $jsonObj = json_decode($result,JSON_OBJECT_AS_ARRAY);

        //根据公司业务处理返回的信息......
        if(isset($jsonObj['Traces'])){
            $traces = $jsonObj['Traces'];
            $jsonObj['Traces'] = array_reverse($traces);
        }
        return json_encode($jsonObj);
    }

    /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return url响应返回的html
     */
    static function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], 80);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param 内容 $data
     * @param appkey Appkey
     * @return DataSign签名
     * @internal param 内容 $data
     */
    static function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }

}