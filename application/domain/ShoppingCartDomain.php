<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-20
 * Time: 15:26
 */

namespace app\domain;


use app\src\shoppingCart\action\ShoppingCartAddAction;
use app\src\shoppingCart\action\ShoppingCartBulkAddAction;
use app\src\shoppingCart\action\ShoppingCartQueryAction;
use app\src\shoppingCart\action\ShoppingCartQueryV103Action;
use app\src\shoppingCart\logic\ShoppingCartLogic;
use app\src\shoppingCart\validate\CartValidate;

class ShoppingCartDomain extends BaseDomain
{
    /**
     * 查询购物车项
     * 102: 对商品进行了分类，按发布人
     * @author hebidu <email:346551990@qq.com>
     */
    public function query(){
        $this->checkVersion(["103"]);

        $uid = $this->_post('uid','',lang("uid_need"));
        $v = $this->request_api_ver;
        if($v == "103"){
            $action = new ShoppingCartQueryV103Action();
        }else{
            $action = new ShoppingCartQueryAction();
        }

        $result = $action->query($uid);
        $this->exitWhenError($result,true);
    }


    /**
     * 购物车项更新
     * @author hebidu <email:346551990@qq.com>
     */
    public function update(){
        $id = $this->_post('id','',lang('id_need'));
        $cnt = $this->_post('count','',lang('count_need'));
        $uid  = $this->_post('uid','',lang('uid_need'));

        $logic = new ShoppingCartLogic();

        $result = $logic->save(['id'=>$id,'uid'=>$uid],['count'=>$cnt]);

        $this->exitWhenError($result,true);
    }

    /**
     * 购物车批量添加
     * @author hebidu <email:346551990@qq.com>
     */
    public function bulkAdd(){
        $uid = $this->_post('uid');
        $id  = $this->_post("id");
        $count = $this->_post('count');
        $sku_pkid = $this->_post('sku_pkid');

        $count_arr = explode(",",$count);
        $sku_pkid_arr = explode(",",$sku_pkid);

        if(empty($count_arr) || empty($sku_pkid_arr) || count($sku_pkid_arr) != count($count_arr)){
            $this->apiReturnErr(lang("err_param"));
        }

        
        $action = new ShoppingCartBulkAddAction();

        $result = $action->bulkAdd($uid,$id,$count_arr,$sku_pkid_arr);

        $this->exitWhenError($result,true);
    }

    /**
     * 添加
     * 101: 增加购物项限制为 40,测试情况下为 2
     * @author hebidu <email:346551990@qq.com>
     */
    public function add(){
        $this->checkVersion(["101"]);
        $entity = $this->getParams(['uid','count','id','sku_pkid']);

        $validate =  new CartValidate();

        if(!$validate->check($entity)){
            $this->apiReturnErr($validate->getError());
        }

        $action = new ShoppingCartAddAction();
        
        $result = $action->add($entity);
        
        $this->exitWhenError($result,true);
    }

    /**
     * 删除操作
     * @author hebidu <email:346551990@qq.com>
     */
    public function delete(){
        $id  = $this->_post('id','',lang('id_need'));
        $uid  = $this->_post('uid','',lang('uid_need'));

        $logic = new ShoppingCartLogic();

        $id = trim($id,",");


//        $result = $logic->delete(['id'=>$id,'uid'=>$uid]);

        $result = $logic->delete(['id'=>['in',$id],'uid'=>$uid]);

        $this->exitWhenError($result,true);
    }

}