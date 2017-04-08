<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/12
 * Time: 11:37
 */

namespace tests\authsystem;


use app\src\authsystem\logic\AuthDataObjectLogic;
use tests\TestCase;

class AuthDataLogicTest extends TestCase
{
    public function testCreate(){
        $logic = new AuthDataObjectLogic();
        $this->assertNotNull($logic);
        $this->assertNotNull($logic->getModel());
    }
}