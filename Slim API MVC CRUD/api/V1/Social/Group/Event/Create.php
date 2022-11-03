<?php


namespace api\V1\Social\Group\Event;


use Api\V1\Model\Response;
use Module\Exception\MySQL;
use Module\OAuth\Scope;
use Module\Social\Group\Group;
use Module\Social\Group\Member;
use Module\User\Account;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Create extends \Api\V1\AbstractRestController
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
     * @OA\Post(
     *     path="/social/group/create",
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
            $group = Group::createWithUserId($user,$_POST['title']);
            if ($group_id > 0) {
                $memberGroup = Member::loadWithMemberGroupId($user->id(),$group_id);
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