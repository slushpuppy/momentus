<?php

namespace Tests\Module\Social\Group\Event;

use Module\Social\Group\Event\Event;
use Module\Social\Group\Event\RSVP;
use Module\Social\Route\Route;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class RSVPTest extends TestCase
{

    public function testCreateFromEventId()
    {
        $route = Route::createFromMember(Init::$testUser1,"My Test Route",300);
        $mem = Init::$testUser1;
        $event1 = Event::createWithGroupMemberId(Init::$testGroup1,$mem,$route,"test event","description");

        $rsvp = RSVP::createFromEventId($event1->id(),Init::$testUser1);

        $newrsvp = RSVP::loadWithId($rsvp->id());
        $this->assertEquals($newrsvp->member_display_name,$rsvp->member_display_name);
        $this->assertEquals($newrsvp->social_group_event_id,$rsvp->social_group_event_id);
        $this->assertEquals($newrsvp->time,$rsvp->time);

    }
}
