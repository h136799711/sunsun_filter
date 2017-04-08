<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-10
 * Time: 9:28
 */

namespace app\src\freight\model;


use think\Model;

class FreightTemplate extends Model
{
//    protected $_link = array(
//        'FreightAddress' => array(
//            //'mapping_type'  => self::HAS_MANY,
//            'class_name'    => 'FreightAddress',
//            'foreign_key'   => 'template_id',
//            'mapping_name'  => 'freightAddress',
//            // 'mapping_order' => 'id desc',
//        )
//    );
    /**
     * 重量
     * @author hebidu <email:346551990@qq.com>
     */
    const TYPE_WEIGHT = "2";

    /**
     * 件数
     * @author hebidu <email:346551990@qq.com>
     */
    const TYPE_COUNT = "1";


    public function FreightAddress()
    {
        return $this->hasMany('FreightAddress','template_id','id','freightAddress');
    }

}