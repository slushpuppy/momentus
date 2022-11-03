<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 30/3/2019
 * Time: 6:49 PM
 */

namespace Module\Social\Group\Event;

use Module\Social\Route\Route;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class EventTest extends TestCase
{

    public function testGetColumns()
    {
        $this->doesNotPerformAssertions();
        $this->expectNotToPerformAssertions();
       // var_dump(Event::getColumns());
    }
    public function testLoadAllWithGroupId() {
       // $img = Image::createFromBlob("\x89PNG\x0d\x0adfsfdfd");
       // $group = Group::createWithUserIdAndImage(Init::$testUser1,$img,"Test");
        //$newGroup = Group::loadAllGroupsWithOwnerUserId(Init::$testUser1->id())[0];

    }
    public function testCreateWithGroupMemberId() {
        $route = Route::createFromMember(Init::$testUser1,"My Test Route",300);
        $mem = Init::$testUser1;
        $event1 = Event::createWithGroupMemberId(Init::$testGroup1,$mem,$route,"test event","description");

        $event = Event::loadWithId($event1->id());
        $this->assertEquals($event->author_display_name,$mem->display_name);
        $this->assertEquals($event->author_user_id, $mem->id());
    }
}
