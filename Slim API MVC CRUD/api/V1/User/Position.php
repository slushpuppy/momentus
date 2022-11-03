<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 3/9/2018
 * Time: 8:27 PM
 */


namespace api\V1\User;
use Api\V1\Model\Response;
use Lib\Core\Helper\Error;
use Module\OAuth\Scope;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Position extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected static $permissionScope = Scope::PROFILE;

    private function __construct()
    {
    }


    public static function i()
    {
        if (self::$_i == NULL) {
            self::$_i = new self;
        }
        return self::$_i;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     * @throws \Module\Exception\MySQL
     * @OA\Post(
     *     path="/user/position",
     *     summary="Update user position",
     *     description="Update user position",
     *     security={"accountBearer"},
     *     @OA\Response(
     *     response=200,
     *     description="Response after submitting user request",
     *     @OA\JsonContent(ref="#/components/schemas/Response")
     * )
     * )
     */
    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $res = new Response();
        $request = $request->getParsedBody();
        if (isset($request["x"],$request["y"])) {
            $position = new \Module\User\Position();
            $position->user_id = $this->getActiveUserId();
            $position->point = [$request["x"],$request["y"]];
            $position->time = \time();
            $position->save();

            $res->status = Response::STATUS_OK;
            return $response->withJson($res);
        } else {
            $res->status = Response::STATUS_ERROR;
            $res->error = Error::INVALID_PARAMETER_COUNT;
            return $response->withJson($res);
        }



    }

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

    }

    public function put(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement put() method.
    }

    public function patch(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement patch() method.
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement delete() method.
    }
}