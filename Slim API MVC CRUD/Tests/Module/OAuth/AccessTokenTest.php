<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 22/3/2019
 * Time: 7:48 PM
 */
namespace Tests\Module\OAuth;

use Module\OAuth\AccessToken;
use Module\OAuth\AccessTokenScope;
use Module\OAuth\Scope;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class AccessTokenTest extends TestCase
{
    /**
     * @var AccessToken
     */
    private static $token;

    public function testCreate()
    {
        $tmp = AccessToken::create(Init::$testUser1->id());
        $this->assertNotNull($tmp);
        $this->assertIsInt($tmp->id());
        $this->assertGreaterThan(time(),$tmp->expiry);

        $this->assertEquals($tmp->auth_type,'internal');

        $tmp1 = AccessToken::create(Init::$testUser1->id());
        $this->assertNotEquals($tmp1->token,$tmp->token);

        self::$token = $tmp1;
    }
    /**
     * @depends testCreate
     */
    public function testLoadWithToken()
    {
        $this->expectNotToPerformAssertions();
    }

    /**
     * @depends testCreate
     */
    public function testUpdateToken()
    {
        $this->expectNotToPerformAssertions();
    }

    /**
     * @depends testCreate
     */
    public function testLoginWithToken()
    {
        $profile_token = AccessTokenScope::create(self::$token->id(), Scope::PROFILE);
        $this->assertEquals($profile_token->token_id,self::$token->id());
       /* $internal = AccessToken::loginWithToken(self::$token->token,Scope::PROFILE,'internal');
        var_dump($internal);
        var_dump(self::$token);
        $this->assertEquals($internal->token,self::$token->token);

        $internal2 = AccessToken::loginWithToken(self::$token->token,Scope::PROFILE,'facebook');
        $this->assertNull($internal2);*/
    }


    /**
     * @depends testCreate
     */
    public function testLoadWithUserId()
    {
        $tmp = AccessToken::loadWithUserId(Init::$testUser1->id(),'internal');
        $this->assertEquals($tmp->token,self::$token->token);

        $tmp = AccessToken::loadWithId(342324);
        $this->assertNull($tmp);
    }


}
