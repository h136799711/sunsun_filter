<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-02
 * Time: 16:08
 */

namespace app\index\controller;


use app\src\alibaichuan\service\OpenIMUserService;
use app\src\encrypt\algorithm\Md5V3Alg;
use app\src\rfpay\po\RfNoCardBalanceReq;
use app\src\rfpay\po\RfNoCardOrderCreateReq;
use app\src\rfpay\service\RfNoCardOrderService;
use app\src\rfpay\utils\RsaUtils;
use app\src\sunsun\common\action\HeatingRodTempHisAction;
use app\src\sunsun\filterVat\action\FilterVatDeviceEventAction;
use app\src\sunsun\filterVat\action\FilterVatTcpLogAction;
use app\src\sunsun\heatingRod\action\HeatingRodDeviceEventAction;
use app\src\sunsun\heatingRod\action\HeatingRodTcpLogAction;
use app\src\system\action\ApiCallHisClearOldAction;
use app\src\tool_email\helper\EmailHelper;
use app\src\user\logic\MemberConfigLogic;
use think\Controller;

class Test extends Controller
{
    public function time(){
        echo time();
        echo '<br/>';
        echo date('Y-m-d H:i:s',time());
        echo '<br/>';
        echo gmdate('Y-m-d H:i:s',time());
        echo '<br/>';
    }

    public function test_decrypt(){
        $data = "4cc5yaooO48SByU7J0mc/XC9R5JTFTOpL5p0PCc+rki4mZsUKS1NIEvp/BP/DtZSrFfTgA0NB7JX
0/MWhMnGmCUqFefKKzi+esKWsAJKmVQfRbc20YyJY/XXVi0DufiDrs/d0veL92aZYgxYhawhQItZ
FtlR2JC/OvyfCm7uyaOOdIr0OVdglUyuy6X33Je1KGSZkZcTXokXd9mDmJ8mj8UbxYcMB/v9ubej
YxpCKL0C142dtThPWeOWZH0yLEpTPCX0EPPInE6EvNRzvq8AmOnHlhRATA2YAJnpaujEC5vriefO
F+s2D9hi1JKi3pCgU1yEQqEEt177JpxLRzKCcyLfTY6m1rerD0PSt8Rj93wT555ym3mggNwUArTs
XC8BmrglMRQ/BuUic4CkLV1UDr9vW46+THAcUIjmxyt+O2xXmIjaMwpVEyMJoIF9k6YU8gi5zMeF
Wx5/DcUjJ5Dw8AnUcCl4kFl1WOGN3UbAzpXGNC2T1JmEbGdEjah2u83bOx4rxAj/kfTMeCuY5XnF
aXUNWXiQUnT7XUVbhlkFxGFj/YNae0oqnQRReLdwkUGNYsAMEve/uefj0e/huq5VdLB6XElxIQFh
BpRe12Bywi+G84ZYGX72g516+Vlo/tzY5Mbe7qTE3hXMz3Vofzt/E2nY5Pjvx0Vs7zq7uqLLeTjL
BNf77jz/C4cjPtvAlx1KzBX/qWL2YunGhDE6/oq+ehq3J5DF+GiGW5/O9T0INm6SpIXYUE1PfzEU
Vryp2PAxmYYtU0QVGyc=";

        $key = "b6b27d3182d589b92424cac0f2876fcd";
        $alg = new Md5V3Alg();
//        bms1OXZ5SEVGQ0dNSEd4SXpDM2V1NldLREswUy9Sbnl3b0NoNm1rQURHemZpZitVUExLR1QwS2dTQlJSb0x3RU52anN2VEZDTFRid3hVWEplSitLMmhZMHRrMjFkQ3VVLzc5bHZmNFJObTFNcUExQTBrV1VzamJlS3QzSSswK0FscERmdno4aEpOUXk4NjNGajlieVJiazZieUdIc1d4bDc4aWxMZjFaTGlSY3ZFQWlydGtpbVZhKzJPWjZnY2oyM0p2ZGlwNUdzS0ZTcjJxRXExd0tSWVNCNGFVb0tiT2gyakk3N2JjQ0ZUT1BtelZEcS9ITUhoUTl1cTgxOFJiZmhZUVpMdFZCeUZSQisvRnhwbEhqcWhKQ21mYmw0Y2ZZNHhhNGdiQmh6VExvU0hTUnBPWnFtbXMvQlZmMDNadVVNTzVXWkttQkc2akxkZlZpYmhBWDZyNlUrMEw2ektMWUpnUUlpN2N1QlJjMHpRSlJTMGpoQ21WVEtTOTFwQXZxZXozbmZ3cnA0b0lYaE1PMjhReUN2cWx4SnZlSEdObVVQcEtod3Nkdmg5OThYZEtJa1Jtak9QOCtKL2FMSHBpYw
        var_dump($alg->decryptTransmissionData(($data),$key));
    }

    /**
     *
     */
    public function testOpenIMUser(){
        $id = $_GET['id'];
        $result = (new MemberConfigLogic())->getInfo(['uid'=>$id]);

        if(!$result['status'] || empty($result['info']) ){
            dump("用户id错误");
            exit;
        }
        $openid = $result['info']['alibaichuan_id'];
        var_dump($openid);
        $service = new OpenIMUserService();
        $result = $service->get($openid);
        dump($result);
    }

    /**
     * @author hebidu <email:346551990@qq.com>
     *
     */
    public function balance(){
        $req = new RfNoCardBalanceReq();
        $req->setLinkId("T".time());
        $service = new RfNoCardOrderService();
        $resp = $service->balance($req);
        if($resp->isSuccess()){
            dump("success");
        }

        dump($resp);
    }

    public function sendSms(){
        dump(gmdate('Y-m-dTH:i:s',time()));
//        $req = new RfNoCardOrderSmsReq();
//        $req->setOrderNo("100000");
//        $service = new RfNoCardOrderSmsService();
//        $result = $service->send($req);
//        dump($result);
    }

    public function request(){

        $req = new RfNoCardOrderCreateReq();
        $req->setTestData();
        $req->setLinkId($req->getRandomLinkId());
        $req->setAmount("1000");
        $service = new RfNoCardOrderService();
        $result = $service->create($req);
        dump($result);
    }

    public function index(){
        RsaUtils::test();
        
//        $key = "12456ds98d123456";
//        $data = "123456";
//
//
//        $encrypt = AesUtils::encrypt($data,$key);
//
//        echo $data." <br/>";
//        $hexEncrypt = AesUtils::toHexStr($encrypt);
//        echo "==encrypt=<br/>";
//        echo $hexEncrypt."<br/>";
//
//        $decrypt = AesUtils::decrypt(AesUtils::toBinStr($hexEncrypt),$key);
//        echo "<br/>==decrypt=<br/>";
//        echo $decrypt;

    }

    public function sendEmail(){
        $to_email = "346551990@qq.com";
        $title = "html邮件测试html";
        $content =  "<h1> 【签名】相关规则:</h1>";
        dump( (new EmailHelper())->send($to_email,$title,$content));
    }

    public function clear_db(){
        $now = time();
        //1. 设备事件日志30天内保留
        $dataTimestamp = $now  - 30*24*3600;
        $result = (new FilterVatDeviceEventAction())->clearExpiredData($dataTimestamp);

        dump($result);
        //1. 设备事件日志30天内保留
        $dataTimestamp = $now  - 30*24*3600;
        $result = (new HeatingRodDeviceEventAction())->clearExpiredData($dataTimestamp);

        dump($result);
        //1. tcp接口日志7天内保留
        $dataTimestamp = $now - 7*24*3600;
        $result = (new FilterVatTcpLogAction())->clearExpiredData($dataTimestamp);

        dump($result);
        //2. tcp接口日志7天内保留
        $dataTimestamp = $now - 7*24*3600;
        $result = (new HeatingRodTcpLogAction())->clearExpiredData($dataTimestamp);

        dump($result);
        //3. tcp接口日志7天内保留
//            $dataTimestamp = $now - 24*3600;
//            $result = (new TcpLogAction())->clearExpiredData($dataTimestamp);

        //1. 接口日志7天内保留
        $dataTimestamp = $now - 3*24*3600;
        $result = (new ApiCallHisClearOldAction())->clearExpiredData($dataTimestamp);

        dump($result);


        // 历史温度数据
        $dataTimestamp = $now - 30*24*3600;
        $result = (new HeatingRodTempHisAction())->clearExpiredData($dataTimestamp);

        dump($result);
    }
}