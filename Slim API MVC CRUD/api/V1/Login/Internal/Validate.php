<?php


namespace api\V1\Login\Internal;


use api\V1\Login\Internal\Model\ResponseRefreshToken;
use Api\V1\Model\Response;
use Lib\Core\Cache;
use Module\OAuth\Scope;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Validate extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected static $permissionScope = null;
    protected const SOURCE_MODULE = 'Api\V1\Login\Internal\Validate';

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
        $res = new ResponseRefreshToken();
        if ($_GET['channel']) {
            if ($key = Cache::i()->getEncrypted($_GET['channel'])) {
                $res->data = $key;
            }
            else {
                $res->error = "Not Found";
                $res->setCode("NOT_FOUND");
                $res->status = Response::STATUS_ERROR;
            }
        }  else {
            $res->error = "Not Found";
            $res->setCode("NOT_FOUND");
            $res->status = Response::STATUS_ERROR;
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