<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 5/9/2018
 * Time: 6:42 PM
 */

/*
$_SESSION['fb_access_token'] = (string) $accessToken;
$email = 'test@test.com';
$display_name = 'Luke.L';
$first_name = 'Luke';
$last_name = 'Lim';


$token = \Module\OAuth\AccessToken::create(1);
$scope1 = \Module\OAuth\AccessTokenScope::create(1,'profile');
$scope2 = \Module\OAuth\AccessTokenScope::create(1,'social');
$auth = new \Module\OAuth\Handler\Facebook\Model();
$auth->user_id = 1;
$data = new \Module\OAuth\Handler\Facebook\Data();
$data->facebookId = '1234';
$auth->setDataField($data);
$auth->save(true);
var_dump($auth->getDataField());*/

use PHPUnit\Framework\TestCase;

class OAuthTest extends TestCase {
    public function testToken() {
        $this->expectNotToPerformAssertions();
    }

}