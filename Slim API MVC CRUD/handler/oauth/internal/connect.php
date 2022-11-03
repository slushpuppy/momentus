<?php

use Api\V1\Oauth\Authenticate;

require_once (__DIR__.'/../autoloader.php');
$md5VerifySum = md5($_GET['verify']);
if (isset($_GET['verify']) && \Lib\Core\Cache::i()->get($md5VerifySum) === FALSE) {
    $payload = \Module\User\Login\Internal::verifyJWT($_GET['verify']);
    if ($payload != null) {

        try {
            $user = \Module\User\Account::create( $payload->email,'','','','','');


        } catch (\Module\Exception\User\Account $e) {
            if ($e->getCode() == \Module\Exception\User\Account::USER_EXISTS) {
                $user = \Module\User\Account::loadWithEmail($payload->email);
            } else {
                $error = "Unknown Error";
            }
        }
        catch(\Exception $e) {
            $error = "Unknown Error";
        }
        if ($user) {
            $user_id = $user->id();
            $token = \Module\OAuth\AccessToken::create($user_id);

            $profile_token = \Module\OAuth\AccessTokenScope::create($token->id(),\Module\OAuth\Scope::PROFILE);
            $social_token = \Module\OAuth\AccessTokenScope::create($token->id(),\Module\OAuth\Scope::SOCIAL_MEDIA);
            $garage_token = \Module\OAuth\AccessTokenScope::create($token->id(),\Module\OAuth\Scope::GARAGE);
            $auth = new Api\V1\Oauth\Authenticate();
            $auth->scope = \Module\OAuth\Scope::PROFILE.' '.\Module\OAuth\Scope::SOCIAL_MEDIA.' '.\Module\OAuth\Scope::GARAGE;
            //\Module\Notification\Websocket\Publish::I()->send(md5($_GET['verify']),$token->refresh_token);
            try {
                \Lib\Core\Cache::i()->setExEncrypted($md5VerifySum,$token->refresh_token,\Config\Login\Internal::TOKEN_VALIDITY_SEC);
            } catch (\RedisException $e) {
                \Module\Exception\Log::i()->add(self::SOURCE_MODULE,$e->getCode(),$e->getMessage())->error();
                $error = "Unknown Error";
            }



        }
    } else {
        $error = "Expired token";
    }
} else {
    $error = "Expired token";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login Successful</title>
<script type="application/javascript">
    var copy = function(elementId) {

        var input = document.getElementById(elementId);
        var isiOSDevice = navigator.userAgent.match(/ipad|iphone/i);

        if (isiOSDevice) {

            var editable = input.contentEditable;
            var readOnly = input.readOnly;

            input.contentEditable = true;
            input.readOnly = false;

            var range = document.createRange();
            range.selectNodeContents(input);

            var selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);

            input.setSelectionRange(0, 999999);
            input.contentEditable = editable;
            input.readOnly = readOnly;

        } else {
            input.select();
        }

        document.execCommand('copy');
    }
</script>
</head>
<body style="background-color:rgb(255,182,121);">

<div style="margin: 0;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);color:white;text-align: center;width:40%; font-weight: bold;font-size: 1.2em">
    <img style="width:100%;height:auto" src="../../../../asset/core/logo.png" />
    <div>
    <?php

    if ($error) {
        echo $error;
    }
    if ($token) {
        echo 'Email validated. Go visit the app';
    }

    ?>
    </div>
</div>
</body>
</html>