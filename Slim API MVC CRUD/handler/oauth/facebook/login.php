<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 23/8/2018
 * Time: 4:03 PM
 */
require_once (__DIR__.'/../autoloader.php');
$fb = new Facebook\Facebook([
    'app_id' => Config\OAuth\Facebook::APP_ID, // Replace {app-id} with your app id
    'app_secret' => COnfig\OAuth\Facebook::SECRET_KEY,
    'default_graph_version' => 'v3.1',
]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('https://www.rust.bike/handler/oauth/facebook/connect.php', $permissions);

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';