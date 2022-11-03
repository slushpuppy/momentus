<?php

namespace Tests\Module\Garage\Service;

use Module\Garage\Avatar;
use Module\Garage\Motorcycle\Vehicle;
use Module\Garage\Service\Document;
use Module\Garage\Service\Service;
use Module\Garage\Service\ServiceDocument;
use Module\Garage\Service\ServicePart;
use Module\Garage\Service\Type;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class ServiceTest extends TestCase
{
    public function testCreateWithUserID()
    {
        $def = Avatar::getDefault();
        $vec = Vehicle::createWithUserID(Init::$testUser1->id(),$def);
        $vec2 = Vehicle::loadAllWithOwnerUserId(Init::$testUser1->id());

        $this->assertEquals($def->getUrl(),$vec->getBikeAvatarUrl());
        $this->assertEquals($vec2[0]->getBikeAvatarUrl(),$vec->getBikeAvatarUrl());

        $service = Service::createWithVehicleId($vec->id(),time());
        $service->review = 'dsfdsddsdf';
        $service->save();
        $type1 = Type::createWithServiceId($service->id(),Type::TUNEUP);
        $type2 = Type::createWithServiceId($service->id(),Type::GENERAL);
        $type3 = Type::createWithServiceId($service->id(),Type::PREVENTIVE);

        $type1->delete();
        $i = 2;
        foreach($service->getServiceTypes() as $type) {
            if ($type->type == Type::GENERAL) $i--;
            if ($type->type == Type::PREVENTIVE) $i--;
        }
        $this->assertEquals(0,$i);
        $doc = Document::createFromStaticFile(Init::$test100kimage);

        $svcDoc = ServiceDocument::createWithServiceId($service->id(),$doc,ServiceDocument::TYPE_ODOMETRY,time());
        $this->assertEquals($doc->getFilePath(),$svcDoc->media_path);
        ServicePart::createWithServiceIdPartId($service->id(),1);
        $parts = ServicePart::loadAllWithServiceId($service->id());
        //var_dump($parts);
        $service1 = Service::loadWithId($service->id());
        var_dump($service1);
    }
}
