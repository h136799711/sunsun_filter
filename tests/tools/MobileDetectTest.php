<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-18
 * Time: 15:11
 */

namespace tests\tools;


use Detection\MobileDetect;
use tests\TestCase;

class MobileDetectTest extends TestCase
{
    public function testInclude(){
        $this->assertFalse((new MobileDetect())->isMobile(),"is not mobile visit");
        var_dump(MobileDetect::getUserAgents());
    }
}