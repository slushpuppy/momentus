<?php

namespace api\V1\Social\Group;

use Api\V1\Social\Group\Create;
use Module\User\Account;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class CreateTest extends TestCase
{

    public function testPost()
    {
        try {
            $res = Init::$httpClient->request("post", "social/group/create", [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'new_avatar',
                        'contents' => fopen(__DIR__.'/../../../../resource/2mbimage.jpg', 'r'),
                        'filename' => 'custom_filename.txt'
                    ]
                ]
            ]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $this->assertEquals("ERROR",$json->status);
        $this->assertEquals("File too large",$json->error);

        try {
            $res = Init::$httpClient->request("post", "social/group/create", [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name'     => 'new_avatar',
                        'contents' => fopen(__DIR__.'/../../../../resource/100kbimage.jpg', 'r'),
                        'filename' => 'custom_filename.txt'
                    ],
                    [
                        'name' => 'title',
                        'contents' => 'My first group'
                    ]

            ]]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $this->assertEquals($json->data->title,'My first group');
        $this->assertEquals($json->status,"OK");


        try {
            $res = Init::$httpClient->request("post", "social/group/create", [
                'headers' => Init::$testUser1AuthHeader,
                'multipart' => [
                    [
                        'name' => 'title',
                        'contents' => 'My first group'
                    ]

                ]]);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($res->getBody());
        $this->assertEquals($json->data->title,'My first group');
        $this->assertEquals($json->status,"OK");
        $this->assertStringContainsStringIgnoringCase("default.png",$json->data->group_avatar_url);

    }
}
