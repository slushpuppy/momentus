<?php


namespace api\V1\Oauth;


use Api\V1\Model\Response;
use api\V1\Oauth\Model\AccessTokenJWT;
use api\V1\Oauth\Model\RequestRefreshToken;
use api\V1\Oauth\Model\ResponseAccessTokenJWT;
use Module\OAuth\AccessToken;
use Module\User\Account;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Renew extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected static $permissionScope = null;
    protected const SOURCE_MODULE = 'api\V1\OAuth\Renew';

    private function __construct()
    {
    }


    public static function i()
    {
        if (self::$_i == NULL)
        {
            self::$_i = new self;
        }
        return self::$_i;
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $res = new ResponseAccessTokenJWT();
        $auth = new RequestRefreshToken();
        if ($auth->isValid($_GET)) {
            $security = AccessToken::loadWithRefreshToken($auth->refresh_token);
            if ($security != null)
            {
                $user = Account::loadWithId($security->user_id);
                $newAuth = new Authenticate();
                $newAuth->access_token = $security->token;
                $newAuth->scope = $security->getScopeForPayload();
                $jwt =  $user->createSession($newAuth);
                $atj = new AccessTokenJWT();

                $atj->scope = $newAuth->scope;
                $atj->jwt = $jwt;
                $atj->access_token = $security->token;
                $atj->expiry = $security->expiry;


                $res->data = $atj;
                $res->status = Response::STATUS_OK;
            } else {
                $res->status = Response::STATUS_ERROR;
                $res->error_description = "Invalid Permissions";
            }
        } else
        {
            $res->status = Response::STATUS_ERROR;
            $res->error_description = "Invalid User";
        }
        return $response->withJson($res);

    }

    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

    }

    public function put(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement put() method.
    }


    public function patch(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement delete() method.
    }
}