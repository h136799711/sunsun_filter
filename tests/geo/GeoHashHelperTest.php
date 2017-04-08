<?php

namespace tests\geo;
use app\src\tool\helper\GeoHashHelper;
use tests\TestCase;

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-01-17
 * Time: 14:59
 */
class GeoHashHelperTest extends TestCase
{
    public function testEncode(){
        $lat = "39.908700982285396";
        $lng = "116.3974965092";
        $helper = new GeoHashHelper();

        $geohash = $helper->encode($lat,$lng);
        echo "hash= ".$geohash;
        $this->assertNotEmpty($geohash,"geohash");
        $hash = "wtms5nfmytr";
        $place = $helper->decode($hash);
        //0=>纬度、1=>经度
        $this->assertNotEmpty($place,"geohash解析失败");

    }
}