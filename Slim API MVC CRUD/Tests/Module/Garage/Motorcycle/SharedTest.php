<?php

namespace Tests\Module\Garage\Motorcycle;

use Module\Garage\Avatar;
use Module\Garage\Motorcycle\Shared;
use Module\Garage\Motorcycle\Vehicle;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class SharedTest extends TestCase
{

    public function testCreateWithUserVehicleId()
    {
        $def = Avatar::getDefault();
        $vec = Vehicle::createWithUserID(Init::$testUser1->id(),$def);
        $sh = Shared::createWithUserVehicleId(Init::$testUser1->id(),$vec->id());
        $sh1 = Shared::loadWithUserVehicleId(Init::$testUser1->id(),$vec->id());
        $this->assertEquals($sh1,$sh);
    }
}
