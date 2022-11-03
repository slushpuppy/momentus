<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 4/4/2019
 * Time: 1:24 AM
 */

namespace Module\Social\Group;

use PHPUnit\Framework\TestCase;
use Tests\Init;

class ChatTest extends TestCase
{


    public function testCreate()
    {
        for($i = 0; $i < 10; $i++) {
            $chat = Chat::Create(Init::generateRandomString(\random_int(5,10)),Init::$testUser1,Init::$testGroup1->id());
            $this->assertEquals($chat->author_display_name,Init::$testUser1->display_name);
            $this->assertEquals($chat->group_id,Init::$testGroup1->id());
            sleep(3);
        }

    }

    /**
     * @depends testCreate
     */
    public function testLoadAllWithGroupIdFrom()
    {
        $start = time()-10;
        $end = time();
        $chats = Chat::loadAllWithGroupIdFrom(Init::$testGroup1->id(),$start,$end);
        foreach ($chats as $chat) {
            $this->assertLessThan($end,$chat->time);
            $this->assertGreaterThan($start,$chat->time);
        }
    }

    /**
     * @depends testCreate
     */
    public function testEditMessage()
    {
        $start = time()-10;
        $end = time();
        $chat = Chat::loadAllWithGroupIdFrom(Init::$testGroup1->id(),$start,$end)[0];
        $oldmsgid = $chat->id();
        $oldmsg = $chat->message;
        $chat->message = "test";
        $chat->save();

        $newchat = Chat::loadWithId($oldmsgid);
        $this->assertEquals($newchat->message,"test");

    }
}
