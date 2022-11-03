<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 20/3/2019
 * Time: 4:21 PM
 */

namespace api\V1\User\Other;


use Api\V1\Model\Response;
use api\V1\User\Model\ProfileFields;
use api\V1\User\Model\ResponseProfile;
use Lib\Core\Helper\Error;
use Module\Exception\MySQL;
use Module\OAuth\Scope;
use Module\User\Account;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Profile extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected static $permissionScope = Scope::PROFILE;

    protected const SOURCE_MODULE = 'Api\V1\User\Public\Profile';

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
     * @OA\Get(
     *     path="/user/public/profile/{userId}",
     *     @OA\Parameter(
     *     name="userId",
     *     required=true,
     *     @OA\Schema(type="int"),
     *     in="path"
     * ),
     *     summary="Get User Profile information from user ID",
     *     description="Get user profile",
     *     security={"accountBearer"},
     *     @OA\Response(
     *     response=200,
     *     description="Response after submitting user request",
     *     @OA\JsonContent(ref="#/components/schemas/Response")
     * )
     * )
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement get() method.
        $res = new ResponseProfile();

        if (!isset($args[0]) || intval($args[0]) < 1) {
            $res->status = Response::STATUS_INVALID_PARAM;
            return $response->withJson($res);
        }

        try
        {
            $user = Account::loadWithId($args[0]);
        } catch (MySQL $e)
        {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE,$e->getCode(),$e->getMessage())->error();
            $res->status = Response::STATUS_ERROR;
            return $response->withJson($res);
        }
        $pf = new \api\V1\User\Model\Profile();
        $pf->avatar_url = $user->getAvatarUrl();
        $pf->country_name = $user->country_name;
        $pf->display_name = $user->display_name;

        $res->data = $pf;
        $res->status = Response::STATUS_OK;
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