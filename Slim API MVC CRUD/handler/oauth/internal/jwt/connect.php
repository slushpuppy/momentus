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
use \Firebase\JWT\JWT;

define('SOURCE_MODULE','handler/oauth/internal/connect' );

$output = new stdClass();
switch ($_POST['grant_type']) {
    default:
        $auth = new \handler\oauth\internal\Request\Authenticate();

        if ($auth->isValid($_POST)) {

            $user = \Module\User\Account::loadWithToken($auth->access_token,$auth->scope,'internal');
            if ($user != null) {
                $jwt = $user->createSession($auth);
                $output->jwt = $jwt->token;
                $output->exp = $jwt->exp;
                $output->state = $auth->state;
                $output->display_name = $user->display_name;
                $profiles = \Module\User\Profile\Core::loadAllWithUserId($user->id());
                $output->profile = new stdClass();
                foreach($profiles as $field) {
                    $v = $field->label;
                    $output->profile[$v] = $field->getData();
                }
                Json::i()->send($output);
            } else {
                $error = new Error();
                Json::i()->send($error->setCode('')->setMsg('User not found'));
            }
        }
}






