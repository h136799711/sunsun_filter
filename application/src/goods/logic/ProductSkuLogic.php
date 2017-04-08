<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-20
 * Time: 17:03
 */

namespace app\src\goods\logic;


use app\src\base\helper\ExceptionHelper;
use app\src\base\logic\BaseLogic;
use app\src\goods\model\ProductMemberPrice;
use app\src\goods\model\ProductSku;
use think\Db;
use think\exception\DbException;

class ProductSkuLogic extends BaseLogic
{

    public function _init()
    {
        $this->setModel(new ProductSku());
    }

    /**
     * 获取商品skuId信息
     * @param 商品id|bool $pid 商品id
     * @param bool $field
     * @return array
     */
    public function getSkuId($pid = false,$field = false){

        if($pid === false){
            return $this -> apiReturnErr("缺少商品id参数");
        }
        if($field === false){
            return $this -> apiReturnErr("缺少字段参数");
        }

        $map = array('product_id'=>$pid);

        $list = ($this->queryNoPaging($map,false,$field));

        $res = $this-> getSkuJson($list['info'],$field);
        return $this -> apiReturnSuc($res);
    }


    private function getSkuJson($list,$field){
        //进行数组整合

        $result = array();
        foreach($list as $vo){
            $tmp = explode(';',$vo[$field]);
            foreach ($tmp as $k => $v) {
                if(!isset($result[$k])){
                    $result[$k] = array();
                }
                array_push($result[$k],$v);
            }
        }

        //去重
        foreach($result as $k => $v){
            $result[$k] = array_unique($v);
        }

        $res = array();
        $flag = array();
        $i = 0;
        foreach ($result as $k => $v) {
            foreach($v as $kk => $vv){
                $tmp = explode(':',$vv);

                if(count($tmp) < 2) continue;
                if(!isset($flag[$tmp[0]])){
                    $flag[$tmp[0]] = $i;
                    $i++;
                    $res[$flag[$tmp[0]]]['id'] = $tmp[0];
                }
                if(!isset($res[$flag[$tmp[0]]]['vid'])){
                    $res[$flag[$tmp[0]]]['vid'] = array();
                }
                array_push($res[$flag[$tmp[0]]]['vid'],$tmp[1]);
            }
        }


        return json_encode($res);
    }

    /**
     * 判断是否多规格
     * @param $pid
     * @return array
     */
    public function hasSku($pid = false){

        if($pid === false){
            return $this -> apiReturnErr("缺少商品id参数");
        }

        $map = array('product_id'=>$pid);

        $list = $this -> getModel() -> where($map) ->select();

        if(is_null($list)){
            return $this -> apiReturnSuc(0);
        }

        if(count($list) == 1 && $list[0]['sku_id'] == ""){
            return $this -> apiReturnSuc(0);
        }

        return $this -> apiReturnSuc(1);

    }

    public function queryNoPagingWithMemberPrice($map = null) {
        $query = $this->getModel();
        if(!is_null($map)){
            $query = $query->where($map);
        }

        $list = $query -> select();

        //查询会员价
        $sku_list = $list;
        if($sku_list != null){
            $logic = new ProductMemberPriceLogic();
            foreach ($sku_list as &$val){
                $result = $logic->queryNoPaging(['sku_id'=>$val['id']]);
                if($result['status']){
                    $val['member_price'] = $result['info'];
                }

            }
        }

        return $this -> apiReturnSuc($sku_list);
    }

    public function getInfoWithMemberPrice($map,$order=false){
        if($order === false){
            $result = $this -> getModel() -> where($map)-> find();
        }else{
            $result = $this->getModel()->  where($map)-> order($order) -> find();
        }

        //查询会员价
        $sku = $result;
        if($sku!=null){
            $result =  (new ProductMemberPriceLogic())->queryNoPaging(['sku_id'=>$sku['id']]);
            if($result['status']){
                $sku['member_price'] = $result['info'];
            }
        }

        return $this -> apiReturnSuc($sku);
    }


    public function addSkuList($id,$has_sku,$list){

        //删除表内原有相关数据
        Db::startTrans();
        try {
            $flag = true;
            $error = "";
            $map = array('product_id' => $id);

            $result = $this->getModel()->where($map)->delete();

            //清除会员价
            $MemberPrice = new ProductMemberPriceLogic();
            $map = array('p_id' => $id);
            $result = $MemberPrice->delete($map);

            //重新插入数据
            if ($has_sku == 0) {
                //统一规格
                $entity = array(
                    'product_id' => $id,
                    'sku_id' => '',
                    'ori_price' => ($list['ori_price'] * 100),
                    'price' => ($list['price'] * 100),
                    'cnt1' => $list['cnt1'],
                    'price2' => ($list['price2'] * 100),
                    'cnt2' => $list['cnt2'],
                    'price3' => ($list['price3'] * 100),
                    'cnt3' => $list['cnt3'],
                    'quantity' => $list['quantity'],
                    'product_code' => $list['product_code'],
                    'icon_url' => '',
                    'sku_desc' => "",
                );

                $result = $this->add($entity);

                $sku_id = $result['info'];

                foreach ($list['member_price'] as $vo) {
                    //插入会员价数据
                    $entity = array(
                        'p_id' => $id,
                        'member_group_id' => $vo['member_group_id'],
                        'sku_id' => $sku_id,
//                               'price' => $vo['price']
                    );
                    $insertResult = $MemberPrice->add($entity);
                    if (!$insertResult['status']) {
                        $flag = false;
                        $error = $insertResult['info'];
                    }
                    if (!$flag) {
                        //出错则跳出
                        break;
                    }
                }

            } else {
                //多规格
                foreach ($list as $vo) {
                    $entity = array(
                        'product_id' => $id,
                        'sku_id' => $vo['sku_id'],
                        'ori_price'=>($vo['ori_price'] * 100),
                        'price' => ($vo['price'] * 100),
                        'cnt1' => ($vo['cnt1'] * 1),
                        'price2' => ($vo['price2'] * 100),
                        'cnt2' => ($vo['cnt2'] * 1),
                        'price3' => ($vo['price3'] * 100),
                        'cnt3' => ($vo['cnt3'] * 1),
                        'quantity' => $vo['quantity'],
                        'product_code' => $vo['product_code'],
                        'icon_url' => $vo['icon_url'],
                        'sku_desc' => $vo['sku_desc'],
                    );

                    $result = $this->add($entity);

                    $sku_id = $result['info'];
                    foreach ($vo['member_price'] as $xo) {
                        //插入会员价数据
                        $entity = array(
                            'p_id' => $id,
                            'member_group_id' => $xo['member_group_id'],
                            'sku_id' => $sku_id,
//                                'price' => $xo['price']
                        );
                        $insertResult = $MemberPrice->add($entity);

                        if (!$insertResult['status']) {
                            $flag = false;
                            $error = $insertResult['info'];
                        }
                        if (!$flag) break;
                    }

                }

            }

            if ($flag) {
                Db::commit();
                return $this->apiReturnSuc($result);
            } else {
                Db::rollback();
                return $this->apiReturnErr($error);
            }
        }catch (DbException $ex){
            Db::rollback();
            return $this->apiReturnErr(ExceptionHelper::getErrorString($ex));
        }
    }

    public function isSku($pid){

        if($pid === false){
            return $this -> apiReturnErr("缺少商品id参数");
        }

        $map = array('product_id' => $pid);
        $result = $this -> getModel() -> where($map) -> count();

        if($result == 0){
            return $this -> apiReturnSuc(0);
        }else{
            return $this -> apiReturnSuc(1);
        }
    }

    /**
     * 获取规格名称
     * @param bool|false $pid
     * @param bool|false $field
     * @return array
     */
    public function getSkuName($pid = false,$field = false){

        if($pid === false){
            return $this -> apiReturnErr("缺少商品id参数");
        }

        if($field === false){
            return $this -> apiReturnErr("缺少字段参数");
        }

        $map = array('product_id' => $pid);

        $list = $this ->getModel() -> where($map) -> field($field) -> select();

        if($list === false){
            $error = $this -> getModel() -> getDbError();
            return $this -> apiReturnErr($error);
        }

        $res = $this -> getSkuJson($list,$field);

        return $this -> apiReturnSuc($res);

    }

}