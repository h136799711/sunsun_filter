<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-17
 * Time: 16:49
 */

namespace app\admin\controller;

use app\src\admin\helper\AdminConfigHelper;
use app\src\base\helper\ValidateHelper;
use app\src\sunsun\common\action\UserDeviceAction;
use app\src\sunsun\filterVat\logic\FilterVatDeviceLogic;
use app\src\sunsun\common\logic\DeviceVersionLogic;

/**
 * Class SunsunFilterVat
 * 过滤桶设备
 * @package app\admin\controller
 */
class SunsunDeviceVersion extends Admin
{


    public function index()
    {
        $allDevice = AdminConfigHelper::getValue("sunsun_device_type");
        $result = (new DeviceVersionLogic())->queryNoPaging(['is_latest'=>1]);
        if ($result['status']) {

            $this->assign('all_device', $allDevice);
            $this->assign('show', $result['info']);
            $this->assign('list', $result['info']);
            return $this->boye_display();
        } else {
            $this->error($result['info']);
        }

    }

    public function morever()
    {
        $map = array();
        $params = false;
        $device_type = $this->_param('device_type', 0);
        if (!empty($device_type)) {
            $map['device_type'] = $device_type;
            $params['device_type'] = $device_type;
        }

        $version = $this->_param('version', '');
        if (!empty($version)) {
            $map['version'] = $version;
            $params['version'] = $version;
        }


        $page = array('curpage' => $this->_param('p', 1), 'size' => config('LIST_ROWS'));
        $order = "create_time desc";
        $result = (new DeviceVersionLogic())->queryWithPagingHtml($map, $page, $order, $params);
        if ($result['status']) {
            $this->assign('version',$version);
            $this->assign('device_type',$device_type);
            $this->assign('show', $result['info']['show']);
            $this->assign('list', $result['info']['list']);
            return $this->boye_display();
        } else {
            $this->error($result['info']);
        }
    }

    public function add()
    {
        $device_type = $this->_param('device_type', '');
        if (IS_GET) {

            $result = (new DeviceVersionLogic())->getInfo(['is_latest'=>1,'device_type'=>$device_type]);
            $latest = "";
            if(ValidateHelper::legalArrayResult($result)){
                $latest = $result['info']['version'];
            }
            $this->assign('latest',$latest);
            $this->assign('device_type',$device_type);
            return $this->boye_display();
        } else {
            $entity = [];
            $entity['device_type'] = $device_type;
            $entity['device_name'] = $this->_param('device_name', '');
            $entity['url'] = config('file_download_url').intval($this->_param('url', ''));
            $entity['bytes'] = $this->_param('bytes',51280);
            $entity['version'] = $this->_param('version', '');
            $entity['is_latest'] = $this->_param('is_latest', 0);
            $entity['version_desc'] = $this->_param('version_desc', '');

            $result = (new DeviceVersionLogic())->add($entity);
            if ($result['status'] === false) {
                $this->error($result['info']);
            } else {
                $this->success(L('RESULT_SUCCESS'), url('Admin/SunsunDeviceVersion/index'));
            }
        }


    }


    public function edit()
    {
        $id = $this->_param('id', 0);
        if (IS_GET) {

            $map = array('id' => $id);
            $result = (new DeviceVersionLogic())->getInfo($map);

            if ($result['status'] === false) {
                $this->error(L('C_GET_NULLDATA'));
            } else {
                $this->assign("entity", $result['info']);
            }

            $this->assign('id',$id);
            return $this->boye_display();

        } else {
            $entity = [];

            $entity['device_type'] = $this->_param('device_type', '');
            $entity['device_name'] = $this->_param('device_name', '');
            $url = intval($this->_param('url', ''));
            if(!empty($url) && $url > 0){
                $entity['url'] = config('file_download_url').$url;
            }

            $entity['bytes'] = $this->_param('bytes', '0');
            $entity['version'] = $this->_param('version', '');
            $entity['version_desc'] = $this->_param('version_desc', '');
            $result = (new DeviceVersionLogic())->saveByID($id,$entity);
            if ($result['status'] === false) {
                $this->error($result['info']);
            } else {
                $this->success(L('RESULT_SUCCESS'), url('Admin/SunsunDeviceVersion/morever',['device_type'=>$entity['device_type']]));
            }
        }


    }


    public function delete()
    {
        $id = $this->_param('id', -1);

        $result = (new DeviceVersionLogic())->delete(['id' => $id]);
        if ($result['status'] === false) {
            $this->error($result['info']);
        } else {
            $this->success(L('RESULT_SUCCESS'));
        }

    }

    public function latest(){
        $id = $this->_param('id', -1);
        $device_type = $this->_param('device_type','');
        $result = (new DeviceVersionLogic())->save(['device_type'=>$device_type],['is_latest'=>0]);

        $result = (new DeviceVersionLogic())->saveByID($id,['is_latest' => 1]);

        $this->success(L('RESULT_SUCCESS'));
    }


}