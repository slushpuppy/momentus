<?php


namespace api\V1\Social\Group;


use Api\V1\Model\Response;
use Module\Exception\MySQL;
use Module\OAuth\Scope;
use Module\Social\Group\Group;
use Module\User\Account;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Member extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected static $permissionScope = null;

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

        $user = $this->getActiveUser();

    }
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     * @OA\Post(
     *     path="/social/group/member/{groupID}",
     *     summary="Join social group",
     *     description="Join social group",
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
        try
        {
            $user = Account::loadWithId($this->getActiveUserId());
            $groupId = $this->isExistAndInt($args[0]);
            if (!isset($args[0]) || intval($args[0]) < 1)
            {
                throw new \Module\Exception\Response(Response::STATUS_INVALID_PARAM);
            }



            if ($groupId > 0) {
                $memberGroup = \Module\Social\Group\Member::createWithMemberGroup($user,Group::loadWithId($groupId));
                if ($memberGroup == null) {
                   throw new \Module\Exception\Response(Response::STATUS_INVALID_PARAM);
                }
                $res->status = Response::STATUS_OK;
            }
        } catch (MySQL $e)
        {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE,$e->getCode(),$e->getMessage())->error();
            $res->status = Response::STATUS_ERROR;
        }
        catch (\Module\Exception\Response $e) {
            $res->status = $e->getMessage();
        }
        finally {
            return $response->withJson($res);
        }

    }

    public function put(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement put() method.
    }


    public function patch(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @OA\Delete(
     *     path="/social/group/member/{groupID}",
     *     summary="Leaves social group",
     *     description="Leaves social group",
     *     security={"accountBearer"},
     *     @OA\Response(
     *     response=200,
     *     description="Response after submitting user request",
     *     @OA\JsonContent(ref="#/components/schemas/Response")
     * )
     * )
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $res = new Response();
        try
        {
            $user = Account::loadWithId($this->getActiveUserId());
            if (!isset($args[0]) || intval($args[0]) < 1)
            {
                $res->status = Response::STATUS_INVALID_PARAM;
                return $response->withJson($res);
            } else {
                $groupId = \intval($args[0]);
            }
            if ($groupId > 0) {
                $memberGroup = \Module\Social\Group\Member::createWithMemberGroup($user,Group::loadWithId($groupId));
                if ($memberGroup != null) {
                    $memberGroup->delete();
                    $res->status = Response::STATUS_OK;
                    return $response->withJson($res);
                }
            }
        } catch (MySQL $e)
        {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE,$e->getCode(),$e->getMessage())->error();
            $res->status = Response::STATUS_ERROR;
            return $response->withJson($res);
        }
    }
}