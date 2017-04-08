<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-12
 * Time: 11:47
 */

namespace app\src\oauth2\logic;


use app\src\base\helper\ExceptionHelper;
use app\src\base\helper\ValidateHelper;
use app\src\base\logic\BaseLogic;
use app\src\oauth2\model\OauthClients;
use think\exception\DbException;

class OauthClientsLogic extends BaseLogic
{

    /**
     * @return mixed
     */
    protected function _init()
    {
        $this->setModel(new OauthClients());
    }

    /**
     * 获取密钥信息
     * @param $client_id
     * @return array
     */
    public function getClientSecret($client_id){
        $result = $this->getInfo(['client_id' => $client_id]);

        $info = $result['info'];

        if (is_array($info) && isset($info['client_secret'])) {
            return $this->apiReturnSuc($info['client_secret']);
        }

        if (empty($info)){
            return $this->apiReturnErr(lang('invalid_parameter',['param'=>'client_id']));
        }

        return $this->apiReturnErr($info);
    }
}