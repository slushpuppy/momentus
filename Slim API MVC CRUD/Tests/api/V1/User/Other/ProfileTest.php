<?php

namespace Tests\Api\Profile\Other;

use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class ProfileTest extends TestCase
{


    public function testGet()
    {
        try {
        $res = Init::$httpClient->request("get", "user/other/profile/".Init::$testUser2->id(), [
            'headers' => Init::$testUser1AuthHeader,
        ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        //var_dump((string)$res->getBody());
        $json = \json_decode($res->getBody());
        $this->assertEquals($json->data->avatar_url,Init::$testUser2->getAvatarUrl());

        try {
            $res = Init::$httpClient->request("get", "user/other/profile/".Init::$testUser2->id(), [
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $this->assertNull($json->data);

        try {
            $res = Init::$httpClient->request("get", "user/other/profile/", [
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $this->assertNull($json->data);

        $this->expectException(ClientException::class);
        try {
            $res = Init::$httpClient->request("get", "user/other/profile/fefwevrg", [
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
       // $json = \json_decode($res->getBody());
       // $this->assertNull($json->data);
    }
}
