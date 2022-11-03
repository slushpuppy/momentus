<?php

namespace Tests\api\V1\Garage;

use Api\V1\Garage\Motorcycle;
use Module\Exception\FileSystem;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class MotorcycleTest extends TestCase
{

    public function testPost()
    {
        try {
            $res = Init::$httpClient->request("post", "garage/motorcycle", [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'new_avatar',
                        'contents' => fopen(Init::$test2mbimage, 'r'),
                        'filename' => 'custom_filename.jpg'
                    ],
                    [
                        'name'     => 'model',
                        'contents' => 'sdfs',
                    ],
                    [
                        'name'     => 'vin',
                        'contents' => '234234423423',
                    ],
                ]
            ]);
            $json = \json_decode($res->getBody());
            $this->assertEquals($json->status,"ERROR");

            $this->assertEquals(FileSystem::FILE_TOO_LARGE,$json->error);

            $res = Init::$httpClient->request("post", "garage/motorcycle", [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'new_avatar',
                        'contents' => fopen(Init::$test100kimage, 'r'),
                        'filename' => 'custom_filename.jpg'
                    ],
                    [
                        'name'     => 'model',
                        'contents' => 'model 1',
                    ],
                    [
                        'name'     => 'vin',
                        'contents' => '234234423423',
                    ],
                ]
            ]);
            $json = \json_decode($res->getBody());
            $this->assertEquals($json->status,"OK");

            $res = Init::$httpClient->request("post", "garage/motorcycle", [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'new_avatar',
                        'contents' => fopen(Init::$test100kimage, 'r'),
                        'filename' => 'custom_filename.jpg'
                    ],
                    [
                        'name'     => 'model',
                        'contents' => 'model 2',
                    ],
                    [
                        'name'     => 'vin',
                        'contents' => '234234423423',
                    ],
                ]
            ]);
            $json = \json_decode($res->getBody());
            $this->assertEquals($json->status,"OK");


            $res = Init::$httpClient->request("post", "garage/motorcycle", [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'new_avatar',
                        'contents' => fopen(Init::$test100kimage, 'r'),
                        'filename' => 'custom_filename.jpg'
                    ],
                    [
                        'name'     => 'model',
                        'contents' => 'model 3',
                    ],
                    [
                        'name'     => 'vin',
                        'contents' => '234234423423',
                    ],
                ]
            ]);
            $json = \json_decode($res->getBody());
            $this->assertEquals($json->status,"OK");


            $res = Init::$httpClient->request("post", "garage/motorcycle", [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'new_avatar',
                        'contents' => fopen(Init::$test100kimage, 'r'),
                        'filename' => 'custom_filename.jpg'
                    ],
                    [
                        'name'     => 'model',
                        'contents' => 'model 4',
                    ],
                    [
                      'name' => 'model_id',
                      'contents' => 556
                    ],
                    [
                        'name'     => 'vin',
                        'contents' => '234234423423',
                    ],
                ]
            ]);
            $json = \json_decode($res->getBody());
            $this->assertEquals($json->status,"OK");
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
    }

    public function testPut()
    {

    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @depends testPost
     */
    public function testPatch()
    {
        try {
            $res = Init::$httpClient->request("post", "garage/motorcycle/2", [
                'headers' => Init::$testUser1AuthHeader,

                'multipart' => [
                    [
                        'name'     => 'model_string',
                        'contents' => 'model patch',
                    ],
                    [
                        'name'     => 'vin',
                        'contents' => '2344423423',
                    ],
                ]


            ]);
            $json = \json_decode($res->getBody());

            $this->assertEquals($json->status,"OK");


            $res = Init::$httpClient->request("get", "garage/motorcycle/2", [
                'headers' => Init::$testUser1AuthHeader,
            ]);
            $json = \json_decode($res->getBody());
            //var_dump($json);
            $this->assertEquals($json->data[0]->model,"model patch");
            $this->assertEquals($json->data[0]->vin,"2344423423");

            $res = Init::$httpClient->request("post", "garage/motorcycle/2", [
                'headers' => Init::$testUser2AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'model_string',
                        'contents' => 'model patch',
                    ],
                    [
                        'name'     => 'vin',
                        'contents' => '2344423334423',
                    ],
                ]
            ]);
            $json = \json_decode($res->getBody());
            //var_dump($json);
            $this->assertEquals("NO_PERMISSION",$json->status);
            //var_dump($json);


            $res = Init::$httpClient->request("post", "garage/motorcycle/2", [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'new_avatar',
                        'contents' => fopen(Init::$test100kimage, 'r'),
                        'filename' => 'custom_filename.jpg'
                    ],
                    [
                        'name'     => 'model',
                        'contents' => 'model 2',
                    ],
                    [
                        'name' => 'model_id',
                        'contents' => 556
                    ],
                    [
                        'name'     => 'vin',
                        'contents' => '234234423423',
                    ],
                ]
            ]);

            $json = \json_decode($res->getBody());
            var_dump($json);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }

    }

    /**
     * @depends testPost
     */
    public function testGet()
    {
        try {
            $res = Init::$httpClient->request("get", "garage/motorcycle", [
                'headers' => Init::$testUser1AuthHeader,

            ]);
            $json = \json_decode($res->getBody());
            $this->assertEquals(count($json->data),4);
            //var_dump($json);

        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }

    }

    public function testDelete()
    {

    }
}
