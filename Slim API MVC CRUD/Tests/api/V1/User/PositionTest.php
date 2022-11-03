<?php

namespace Test\Api\V1\User;

use Api\V1\User\Position;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class PositionTest extends TestCase
{

    public function testPost()
    {
        try {
            $res = Init::$httpClient->request("post", "user/position", [
                    'form_params' => [
                        'x' => 123.3434,
                        'y' => 3343.03443
                    ],
                    'headers' => Init::$testUser1AuthHeader
                ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $this->assertEquals($json->status,"OK");

    }
}
