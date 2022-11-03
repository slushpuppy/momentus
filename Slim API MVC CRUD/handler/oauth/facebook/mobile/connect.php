<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 31/7/2018
 * Time: 8:06 PM
 */
require_once (__DIR__.'/../../autoloader.php');

use \Lib\Core\Helper\Http\Json;
use \Lib\Core\Helper\Error;

define('SOURCE_MODULE','handler/oauth/facebook/connect' );

$output = new stdClass();
$fb = new Facebook\Facebook([
    'app_id' => Config\OAuth\Facebook::APP_ID, // Replace {app-id} with your app id
    'app_secret' => COnfig\OAuth\Facebook::SECRET_KEY,
    'default_graph_version' => 'v3.1'
]);



$access_token = $_REQUEST['access_token'];

if (!isset($access_token)) exitWithError('','Invalid token');

$fb->setDefaultAccessToken($access_token);



    try {
        // Returns a `Facebook\FacebookResponse` object
        $response = $fb->get('/me?fields='.\Config\OAuth\Facebook::PROFILE_FIELDS, $access_token);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        exitWithError('PROFILE_ERROR',$e->getMessage());
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        exitWithError('SDK_EXCEPTION',$e->getMessage());
        exit;
    }

    $fbuser = $response->getGraphUser();
    $fbdata = $fbuser->all();

    try {
        $email = $fbuser->getEmail();
        if ($email == '') $email = $fbuser->getId().\Config\OAuth\Facebook::TEMP_EMAIL_DOMAIN;
        $fbdata['email'] = $email;
        //$email = 'jbkrotpusa_1534936975@tfbnw.net';
        $user = \Module\User\Account::create( $email,'',$fbuser->getFirstName(),$fbuser->getLastName(),'',$fbuser->c);
       // $user = \Module\OAuth\Account::create( $email,'','','','');
        $auth = new \Module\OAuth\Handler\Facebook\Model();
        $auth->user_id = $user->id();
        $data = new \Module\OAuth\Handler\Facebook\Data();
        $data->facebookId = $fbuser->getId();
        $auth->setDataField($data);
        $auth->save();

    } catch (\Module\Exception\User\Account $e) {
        if ($e->getCode() == \Module\Exception\User\Account::USER_EXISTS) {
            $findUser = \Module\OAuth\Handler\Facebook\Model::loadUserFromFacebookId($fbuser->getId());
            $user = \Module\User\Account::loadWithId(
                $findUser->user_id
            );
        } else {
            exitWithError('USER_ERROR','');
        }
    }
    catch(\Exception $e) {
        exitWithError('USER_ERROR',$e->getMessage());
    }
    $user_id = $user->id();
    $token = \Module\OAuth\AccessToken::create($user_id);

    $profile_token = \Module\OAuth\AccessTokenScope::create($token->id(),\Module\OAuth\Scope::PROFILE);
    $social_token = \Module\OAuth\AccessTokenScope::create($token->id(),\Module\OAuth\Scope::SOCIAL_MEDIA);
    $garage_token = \Module\OAuth\AccessTokenScope::create($token->id(),\Module\OAuth\Scope::GARAGE);

    $output->access_token = $token->token;
    $output->expiry = $token->expiry;
    $output->refresh_token = $token->refresh_token;
    $auth = new Api\V1\Oauth\Authenticate();
    $auth->scope = \Module\OAuth\Scope::PROFILE.' '.\Module\OAuth\Scope::SOCIAL_MEDIA.' '.\Module\OAuth\Scope::GARAGE;
    $output->scope = $auth->scope;
    $output->jwt = $user->createSession($auth);
    $output->FBData = $fbuser->all();

    Json::i()->send($output);

function exitWithError($code = '',$msg = 'unknown') {
    $error = new Error();
    \Module\Exception\Log::i()->add(SOURCE_MODULE,$code,$msg)->error();
    Json::i()->send($error->setCode('')->setMsg($msg));
}


