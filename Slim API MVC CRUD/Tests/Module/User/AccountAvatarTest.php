<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 20/3/2019
 * Time: 9:08 PM
 */

namespace Tests\Module\User;

use Module\User\Profile\AccountAvatar;
use PHPUnit\Framework\TestCase;
/*
class AccountAvatarTest extends TestCase
{
 public $user_id = null;
   public function setUp()
   {
       if ($this->user_id == null) {
           $user = \Module\User\Account::load("1233@shark.com","asa");
           if ($user == null) throw new \Exception();
           $this->user_id = $user->id();
       }


   }


   public function testCreateFromBlob()
   {
       $avatar = AccountAvatar::createFromBlobWithUserId("\x89PNG\x0d\x0adfsfdfd",$this->user_id);
       $avatar->save();

       $new = AccountAvatar::loadWithUserId(1);
       $this->assertStringEndsWith(".png",$new->path);
       $this->assertEquals($new->getUserId(),$this->user_id);

   }

   public function testCreateFromUploadedFile()
   {
       $this->expectNotToPerformAssertions();
   }
}
*/