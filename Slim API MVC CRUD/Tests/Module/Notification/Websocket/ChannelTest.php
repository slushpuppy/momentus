<?php

namespace Tests\Module\Notification\Websocket;

use Module\Notification\Websocket\Channel;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class ChannelTest extends TestCase
{

    public function testChannelCreation()
    {
        $channel = Channel::loadChannelsFromUserId(Init::$testUser1->id());
        $this->assertEquals('user-1',$channel[0]->channel_name);
    }
}
