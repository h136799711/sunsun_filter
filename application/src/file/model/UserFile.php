<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-17
 * Time: 14:46
 */

namespace app\src\file\model;

use think\Model;

/**
 * 文件模型
 * 负责文件的上传
 */

class UserFile extends Model{

    /**
     * 自动完成
     * @var array
     */
    protected $insert = ['status'=>1, 'create_time'];

    protected function setCreateTimeAttr()
    {
        return time();
    }

}
