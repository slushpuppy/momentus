<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 26/3/2019
 * Time: 5:59 PM
 */

namespace Module\Social\Group;

use Module\FileSystem\Type\Image;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class GroupTest extends TestCase
{

    public function testGetColumns()
    {
        //var_dump(Group::getColumns());
        $this->expectNotToPerformAssertions();
    }
    public function testCreateWithUserIdAndImage() {
        $img =  Avatar::createFromBlob("\x89PNG\x0d\x0adfsfdfd");
        $group = Group::createWithUserIdAndImage(Init::$testUser1,$img,"Test");
        $newGroup = Group::loadWithId($group->id()); //Group::loadAllGroupsWithOwnerUserId(Init::$testUser1->id())[0];
        $this->assertEquals($newGroup->getAvatar()->path,$img->path);
        $this->assertEquals($newGroup->owner_display_name,Init::$testUser1->display_name);
        $this->assertEquals($group->creation_date,$newGroup->creation_date);
        $this->assertEquals($group->getAvatar()->time,$img->time);
    }
    /**
     * @depends testCreateWithUserIdAndImage
     */
    public function testSetAvatar() {
        $newGroup = Group::loadAllGroupsWithOwnerUserId(Init::$testUser1->id())[0];
        $oldimg = $newGroup->getAvatar();
        $img = Image::createFromBlob("\x89PNG\x0d\x0adfsfdfsdfssfdffsdfsfdd");
        $newGroup->setAvatar($img);
       // var_dump($newGroup);
        $newGroup->save(true);


        $newGroup1 = Group::loadAllGroupsWithOwnerUserId(Init::$testUser1->id())[0];
        $this->assertNotEquals($newGroup1->getAvatar(),$oldimg);
        $newImg = $newGroup1->getAvatar();
        $this->assertEquals($newImg->extension,$img->extension);
        $this->assertEquals($newImg->getUrl(),$img->getUrl());
    }

    /**
     * @depends testCreateWithUserIdAndImage
     */
    public function testIsMemberIdInGroup() {
        $mem = Member::createWithMemberGroup(Init::$testUser1,Init::$testGroup1);
        $this->assertTrue(Init::$testGroup1->isMemberIdInGroup(Init::$testUser1->id()));
        $this->assertFalse(Init::$testGroup1->isMemberIdInGroup(2342342342));

    }
}
