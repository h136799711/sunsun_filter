<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace app\src\system\logic;

use app\src\base\logic\BaseLogic;
use app\src\system\model\Config;

class ConfigLogic extends BaseLogic{

	protected function _init(){
		$this->setModel(new Config());
	}

	/**
	 * 设置
	 * @param $config
	 * @return array
	 */
	public function set($config){
        $effects = 0;
        $result = false;
        if ($config && is_array($config)) {
            $flag = true;
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $result = $this ->getModel() -> where($map) -> setField('value', $value);
                if($result !== false){
                    $effects = $effects + $result;
                }else{
                    $flag = false;
                }
            }
            if($flag !== false){
                $result = $effects;
            }
        }

		if($result === false){
			return $this->apiReturnErr($this->getModel()->getError());
		}else{
			return $this->apiReturnSuc($result);
		}
	}

}
