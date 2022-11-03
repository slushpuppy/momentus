<?php


namespace api\V1\Oauth;

use Api\V1\Model\Response;
use Module\OAuth\Scope;
use Module\User\Account;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

class Authorize extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected static $permissionScope = null;
    protected const SOURCE_MODULE = 'Api\V1\Oauth\Authorize';

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
        // TODO: Implement get() method.


    }

    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $res = new Response();
        $output = new stdClass();
        $auth = new \Api\V1\Oauth\Authenticate();
        if ($auth->isValid($_POST))
        {
            $user = Account::loadWithToken($auth->access_token);
            if ($user != null)
            {
                switch ($_GET['do'])
                {
                    case 'newScope':


                        break;
                }
            }
        }
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