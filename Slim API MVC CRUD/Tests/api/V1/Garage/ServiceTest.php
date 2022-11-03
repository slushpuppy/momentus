<?php

namespace Tests\api\V1\Garage;

use Module\Exception\FileSystem;
use Module\Garage\Avatar;
use Module\Garage\Motorcycle\Vehicle;
use Module\Garage\Service\Service;
use Module\Garage\Service\ServiceDocument;
use Module\Garage\Service\Type;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class ServiceTest extends TestCase
{
    /**
     * @var Vehicle
     */
    private static $veh;

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @depends testPost
     */
    public function testGet()
    {
        $res = Init::$httpClient->request("get", "garage/service/".static::$veh->id(), [
            'headers' => Init::$testUser1AuthHeader
            ]);
        $json = \json_decode($res->getBody());
        var_dump($json);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testPost()
    {
        try {

            $def = Avatar::getDefault();
            $vec = Vehicle::createWithUserID(Init::$testUser1->id(),$def);
            static::$veh = $vec;
            $vecId = $vec->id();
            $res = Init::$httpClient->request("post", "garage/service/".$vecId, [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'start_time',
                        'contents' => time(),

                    ],
                    [
                        'name'     => 'docs[]',
                        'contents' => fopen(__DIR__ . '/../../../resource/2mbimage.jpg', 'r'),
                        'filename' => 'custom_filename.txt'
                    ],
                    [
                        'name'     => 'docs[]',
                        'contents' => fopen(__DIR__ . '/../../../resource/100kbimage.jpg', 'r'),
                        'filename' => 'custom_filename.txt'
                    ],
                    [
                        'name'     => 'docs_type[]',
                        'contents' => ServiceDocument::TYPE_ODOMETRY,
                    ],
                    [
                        'name'     => 'docs_type[]',
                        'contents' => ServiceDocument::TYPE_RECEIPT,
                    ],
                    [
                        'name'     => 'service_types[]',
                        'contents' => Type::GENERAL,
                    ],
                    [
                        'name'     => 'service_types[]',
                        'contents' => Type::PREVENTIVE,
                    ],
                ]
            ]);
            $json = \json_decode($res->getBody());
            $this->assertEquals($json->status,"OK");
            $this->assertIsInt($json->data->id);
            $docs = $json->data->docs;
            $this->assertEquals(\sizeof($docs),2);

            $this->assertEquals($docs[0]->type,ServiceDocument::TYPE_ODOMETRY);
            $this->assertEquals($docs[1]->type,ServiceDocument::TYPE_RECEIPT);

            $this->assertStringContainsString('service/document',$docs[0]->url);
            $this->assertStringContainsString('service/document',$docs[1]->url);

            $svcId = $json->data->id;
            $res = Init::$httpClient->request("post", "garage/service/".$vecId."/".$svcId, [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'end_time',
                        'contents' => time() + 400,

                    ],
                    [
                        'name'     => 'review',
                        'contents' => 'ssddsdsdsdss',

                    ],
                    [
                        'name'     => 'docs[]',
                        'contents' => fopen(__DIR__ . '/../../../resource/2mbimage.jpg', 'r'),
                        'filename' => 'custom_filename.txt'
                    ],
                    [
                        'name'     => 'docs[]',
                        'contents' => fopen(__DIR__ . '/../../../resource/100kbimage.jpg', 'r'),
                        'filename' => 'custom_filename.txt'
                    ],
                    [
                        'name'     => 'docs_type[]',
                        'contents' => ServiceDocument::TYPE_ODOMETRY,
                    ],
                    [
                        'name'     => 'docs_type[]',
                        'contents' => ServiceDocument::TYPE_RECEIPT,
                    ],
                ]
            ]);
            $json = \json_decode($res->getBody());
            $this->assertEquals($json->status,"OK");


        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
    }
}
