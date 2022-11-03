<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 21/3/2019
 * Time: 4:14 PM
 */

namespace Tests\Module\User;

use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{

    public function testAuthenticate()
    {
        $this->expectNotToPerformAssertions();
    }

    public function testRenewToken()
    {
        $this->expectNotToPerformAssertions();
    }

    public function testLoad()
    {
        $this->expectNotToPerformAssertions();
    }

    public function testCreateSession()
    {
        $this->expectNotToPerformAssertions();
    }

    public function testDelete()
    {
        $user = \Module\User\Account::create( "12232434233@shark.com",'200puppy',"luke","lim",'+2732666342243',"Singapore");
        $user_id = $user->id();
        $user->delete();
        $newuser = \Module\User\Account::loadWithId($user_id);
        $this->assertNull($newuser);
    }
    public function testCreate()
    {
        $user = \Module\User\Account::create( "123@abcd.com",'puppy',"luke","lim",'+2332342243',"Singapore");

        $user = \Module\User\Account::loadWithId($user->id());

        $this->assertEquals($user->country_name,"Singapore");
        $this->assertEquals($user->email,"123@abcd.com");
        $this->assertEquals($user->display_name,"puppy");
        $this->assertEquals($user->last_name,"lim");
        $this->assertEquals($user->first_name,"luke");
        $this->assertEquals($user->phone_number,"+2332342243");
    }

    public function testVerifyJWT()
    {
        $this->expectNotToPerformAssertions();
    }

    public function testLoadWithToken()
    {
        $this->expectNotToPerformAssertions();
    }
}
