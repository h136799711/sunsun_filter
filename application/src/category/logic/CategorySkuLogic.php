<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-03-11
 * Time: 14:16
 */

namespace app\src\category\logic;


use app\src\base\logic\BaseLogic;
use app\src\category\model\VCategorySku;
use app\src\extend\Page;
use app\src\goods\logic\SkuLogic;
use think\Db;

class CategorySkuLogic extends BaseLogic
{
    public function queryJoin($cate_id,$page = array('curpage'=>0,'size'=>10),$params = false){
        $query = (new VCategorySku());
        $list = $query ->where(['cate_id'=>$cate_id]) -> page($page['curpage'] . ',' . $page['size']) -> select();

        if ($list === false) {
            $error = $this -> getModel() -> getError();
            return $this -> apiReturnErr($error);
        }

        $count = (new VCategorySku()) ->where(['cate_id'=>$cate_id]) -> count();
        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));
    }

    public function queryRightJoin($cate_id,$page = array('curpage'=>0,'size'=>10),$params = false){
        $cate_id = intval($cate_id);
        $sql = " SELECT sku2.name,sku2.id from itboye_sku as sku2 where sku2.id not in ( SELECT cs.sku_id from itboye_category_sku as cs where cs.cate_id = $cate_id  ) ";
        $curpage = intval($page['curpage']);
        $curpage = $curpage - 1 > 0 ? $curpage - 1 : 0;
        $sql .= " limit ".($curpage * $page['size']). ',' . $page['size'];

        $list = Db::query($sql);

        if ($list === false) {
            $error = $this -> getModel() -> getError();
            return $this -> apiReturnErr($error);
        }

        $result = (new SkuLogic())->count([]);
        $total = $result['info'];
        $cateSkuCnt = $this->getModel() ->where(['cate_id'=>$cate_id]) ->count();
        $total = $total - $cateSkuCnt;

        // 查询满足要求的总记录数
        $Page = new Page($total, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));
    }
}