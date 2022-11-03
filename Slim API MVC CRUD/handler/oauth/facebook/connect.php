<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 31/7/2018
 * Time: 8:06 PM
 */
require_once (__DIR__.'/../autoloader.php');

use \Lib\Core\Helper\Http\Json;
use \Lib\Core\Helper\Error;

define('SOURCE_MODULE','handler/oauth/facebook/connect' );

$output = new stdClass();
$fb = new Facebook\Facebook([
    'app_id' => Config\OAuth\Facebook::APP_ID, // Replace {app-id} with your app id
    'app_secret' => COnfig\OAuth\Facebook::SECRET_KEY,
    'default_graph_version' => 'v3.1',
    'http_client_handler' => 'memory',
    'http_client_handler' => 'curl',
]);

//$_SESSION['FBRLH_state']=$_GET['state'];

$helper = $fb->getRedirectLoginHelper();
try {
    $accessToken = $helper->getAccessToken();

} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    exitWithError('GRAPH_ERROR',$e->getMessage());
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    exitWithError('SDK_ERROR',$e->getMessage());
}

if (! isset($accessToken)) {
    if ($helper->getError()) {
        \Module\Exception\Log::i()->add(SOURCE_MODULE,'UNAUTHORIZED_'.$helper->getErrorCode(),
            "Error: " . $helper->getError() . "\n
            Error Reason: " . $helper->getErrorReason() . "\n
            Error Description: " . $helper->getErrorDescription() . "\n"
        )->general();
    } else {
        \Module\Exception\Log::i()->add(SOURCE_MODULE,'BAD_REQUEST','Bad Request')->warning();
        \Module\Exception\Log::i()->add(SOURCE_MODULE,'BAD_REQUEST',print_r($_GET,TRUE))->warning();
        \Module\Exception\Log::i()->add(SOURCE_MODULE,'BAD_REQUEST',print_r($_POST,TRUE))->warning();
    }
    exitWithError('','Unknown Error');
}

// Logged in

$output->fb->access_token = $accessToken->getValue();

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);


// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId(Config\OAuth\Facebook::APP_ID); // Replace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
    // Exchanges a short-lived access token for a long-lived one
    try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        exitWithError('TOKEN_RETRIEVAL',$e->getMessage());
    }

    $token = $accessToken->getValue();
    $output->fb->long_life_token = $token;

    try {
        // Returns a `Facebook\FacebookResponse` object
        $response = $fb->get('/me?fields='.\Config\OAuth\Facebook::PROFILE_FIELDS, $token);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        exitWithError('PROFILE_ERROR',$e->getMessage());
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        exitWithError('SDK_EXCEPTION',$e->getMessage());
        exit;
    }

    $fbuser = $response->getGraphUser();
    try {
        $email = $fbuser->getEmail();
        if ($email == '') $email = $fbuser->getId().\Config\OAuth\Facebook::TEMP_EMAIL_DOMAIN;
        $geoip = \Module\GeoIP\Db::I()->resolveIPAddress($_SERVER['SERVER_ADDR']);
        if ($geoip->getCountryIso() == "") $country = null;
        else $country = $geoip->getCountryIso();
        $user = \Module\User\Account::create( $email,'',$fbuser->getFirstName(),$fbuser->getLastName(),'',$country);
        $output->fb->fields = $fbuser->all();

    } catch (\Module\Exception\User\Account $e) {
        if ($e->getCode() == \Module\Exception\User\Account::USER_EXISTS) {
            $user = \Module\User\Account::loadWithId(
                \Module\OAuth\Handler\Facebook\Model::loadUserFromFacebookId($fbuser->getId())
            );
        } else {
            exitWithError('USER_ERROR','');
        }
    }

    $auth = new \Module\OAuth\Handler\Facebook\Model();
    $auth->user_id = $user->id();
    $data = new \Module\OAuth\Handler\Facebook\Data();
    $data->facebookId = $fbuser->getId();
    $auth->setDataField($data);
    $auth->save();

    $user_id = $user->id();
    $token = \Module\OAuth\AccessTokenTest::create($user_id);
    $profile_token = \Module\OAuth\AccessTokenScope::create($user_id,\Module\OAuth\ScopeTest::PROFILE);

    $output->profile->access_token = $profile_token->token;
    $output->profile->expiry = $profile_token->expiry;
    $output->profile->refresh_token = $profile_token->refresh_token;

    $social_token = \Module\OAuth\AccessTokenScope::create($user_id,\Module\OAuth\Scope::SOCIAL_MEDIA);

    $output->profile->access_token = $social_token->token;
    $output->profile->expiry = $social_token->expiry;
    $output->profile->refresh_token = $social_token->refresh_token;

    Json::i()->send($output);

}

function exitWithError($code = '',$msg = 'unknown') {
    $error = new Error();
    \Module\Exception\Log::i()->add(SOURCE_MODULE,$code,$msg)->error();
    Json::i()->send($error->setCode('')->setMsg($msg));
}


