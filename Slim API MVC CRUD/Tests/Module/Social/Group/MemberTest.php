<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 1/4/2019
 * Time: 2:22 PM
 */

namespace Module\Social\Group;

use Module\Social\Group\Permission\MemberRole;
use Module\Social\Group\Permission\Name;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class MemberTest extends TestCase
{

    public function testCreateWithMemberGroup()
    {
        $this->doesNotPerformAssertions();
    }

    public function testGetColumns()
    {
        $this->doesNotPerformAssertions();
    }

    public function testIsMemberIdInGroup()
    {
        $member = Init::$testUser1;

        $group = Group::createWithUserIdAndImage($member,Init::$testImage,"Test group 2");

        $mem = Member::loadWithMemberGroupId($member->id(),$group->id());
        $this->assertEquals($mem->member_display_name,$member->display_name);
        $this->assertEquals($group->title,"Test group 2");
        //var_dump($mem);
        $this->assertEquals($group->media_path,Init::$testImage->path);
    }
}
