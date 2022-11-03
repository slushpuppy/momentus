<?php

namespace Tests\api\V1\User;

use Api\V1\Model\Response;
use Api\V1\User\Profile;
use Module\User\Account;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class ProfileTest extends TestCase
{

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @depends testPost
     */
    public function testGet()
    {
        try
        {
            $res = Init::$httpClient->request("get", "user/profile", [
                'headers' => Init::$testUser1AuthHeader,
                //  'debug' => true
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $this->assertEquals($json->status,Response::STATUS_OK);
        //$this->assertEquals($json->data->avatar_url,Init::$testUser1->getAvatarUrl());

        try {
            $res = Init::$httpClient->request("get", "user/profile", [
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $this->assertNull($json->data);
    }

    public function testPost()
    {
        try {
            $res = Init::$httpClient->request("post", "user/profile?act=upload_avatar", [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                    'name'     => 'new_avatar',
                    'contents' => fopen(__DIR__ . '/../../../resource/2mbimage.jpg', 'r'),
                    'filename' => 'custom_filename.txt'
                    ]
                ]
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        //var_dump((string)$res->getBody() );
        $this->assertEquals($json->status,"ERROR");
        $this->assertEquals($json->error,"File too large");
        try {
            $res = Init::$httpClient->request("post", "user/profile?act=upload_avatar", [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'new_avatar',
                        'contents' => fopen(__DIR__ . '/../../../resource/100kbimage.jpg', 'r'),
                        'filename' => 'custom_filename.txt'
                    ]
                ]
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $mem = Account::loadWithId(Init::$testUser1->id());
        $this->assertEquals($json->data->avatar_url,$mem->getAvatarUrl());


        $profile = [
            "email" => "test@test.com",
            "phone_number" => "+233232332",
            "country_name" => "Afghanistan",
        ];
        try
        {
            $res = Init::$httpClient->request("post", "user/profile", [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'email',
                        'contents' => 'test@test.com',
                    ],
                    [
                        'name'     => 'phone_number',
                        'contents' => '+233232332',
                    ]
                ]
                //  'debug' => true
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $this->assertEquals($json->status,Response::STATUS_OK);
        $mem = Account::loadWithId(Init::$testUser1->id());
        $this->assertEquals("test@test.com",$mem->email);
        $this->assertEquals($mem->phone_number,"+233232332");


        $res = Init::$httpClient->request("post", "user/profile", [
            'headers' => Init::$testUser1AuthHeader,
            'multipart' => [
                [
                    'name'     => 'document_vault[]',
                    'contents' => fopen(__DIR__ . '/../../../resource/2mbimage.jpg', 'r'),
                    'filename' => 'custom_filename.jpg'
                ]
            ]
        ]);
    }

    /**
     * @depends testGet
     */
    public function testDelete() {
        try
        {
            $res = Init::$httpClient->request("get", "user/profile", [
                'headers' => Init::$testUser1AuthHeader,
                //  'debug' => true
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $doc = $json->data->document_vault[0];
        var_dump($doc);


        try
        {
            $res = Init::$httpClient->request("delete", "user/document_vault/".$doc->id, [
                'headers' => Init::$testUser2AuthHeader,
                //  'debug' => true
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $this->assertEquals("NO_PERMISSION",$json->status);

        try
        {
            $res = Init::$httpClient->request("delete", "user/document_vault/".$doc->id, [
                'headers' => Init::$testUser1AuthHeader,
                //  'debug' => true
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $this->assertEquals("OK",$json->status);


        try
        {
            $res = Init::$httpClient->request("get", "user/profile", [
                'headers' => Init::$testUser1AuthHeader,
                //  'debug' => true
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $this->assertEquals($json->status,Response::STATUS_OK);
        $this->assertEquals(0,count($json->data->document_vault));
    }

}
