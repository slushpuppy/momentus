<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 4/4/2019
 * Time: 12:03 PM
 */

namespace Module\Social\Route\Marker;

use Module\Social\Route\Route;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class MarkerTest extends TestCase
{

    public function testGetColumns()
    {
        $this->expectNotToPerformAssertions();
    }

    public function testCreateFromRouteId()
    {
        $route1 = Route::createFromMember(Init::$testUser1,"My Test Route",300);
        $marker = Marker::createFromRouteId($route1->id(),23,242,Name::START);
        $id = $marker->id();
        $newmarker = Marker::loadWithId($id);
        $this->assertEquals($newmarker->getPointX(),23);
        $this->assertEquals($newmarker->getPointY(),242);
        $this->assertEquals($newmarker->marker_name,Name::START);
    }
}
