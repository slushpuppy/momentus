<?php

namespace Tests\Module\Garage\Motorcycle;

use Module\Garage\Avatar;
use Module\Garage\Motorcycle\Vehicle;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class VehicleTest extends TestCase
{

    public function testCreateWithUserID()
    {
        $def = Avatar::getDefault();
        $vec = Vehicle::createWithUserID(Init::$testUser1->id(),$def);
        $vec2 = Vehicle::loadAllWithOwnerUserId(Init::$testUser1->id());
        $this->assertEquals($def->getUrl(),$vec->getBikeAvatarUrl());
        $this->assertEquals($vec2[0]->getBikeAvatarUrl(),$vec->getBikeAvatarUrl());
    }
}
