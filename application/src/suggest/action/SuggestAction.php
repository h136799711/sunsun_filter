<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-17
 * Time: 11:53
 */

namespace app\src\suggest\action;


use app\src\base\action\BaseAction;
use app\src\base\helper\ValidateHelper;
use app\src\suggest\logic\SuggestLogic;

class SuggestAction extends BaseAction
{
    public function add($data){
        return (new SuggestLogic())->add($data);
    }
}