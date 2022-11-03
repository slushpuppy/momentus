<?php


namespace api\V1\Social\Group;


use Api\V1\Model\Response;
use Module\Exception\FileSystem;
use Module\Exception\MySQL;
use Module\OAuth\Scope;
use Module\Social\Group\Avatar;
use Module\Social\Group\Group;
use Module\Social\Group\Member;
use Module\User\Account;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Edit extends \Api\V1\AbstractRestController
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
     * @throws MySQL
     * @throws \Module\Exception\Controller
     * @OA\Post(
     *     path="/social/group/edit",
     *     summary="Creates a social group",
     *     description="Creates a social group",
     *     @OA\RequestBody(
     *     description="Input data",
     *     @OA\MediaType(
     *     mediaType="application/x-www-form-urlencoded",
     *     @OA\Schema(
     *     type="object",
     *     @OA\Property(
     *     property="new_avatar",
     *     description="Group Avatar file",
     *     type="string",
     *     format="binary"),
     *     @OA\Property(
     *     property="title",
     *     type="string",
     *     description="title of group")
     * )
     * )
     * ),
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

        $body = $request->getParsedBody();

        try
        {
            $user = Account::loadWithId($this->getActiveUserId());
        } catch (MySQL $e)
        {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE,$e->getCode(),$e->getMessage())->error();
            $res->status = Response::STATUS_ERROR;
            return $response->withJson($res);
        }
        $avatar = null;
        $uploadedFiles = $request->getUploadedFiles();

        // handle single input with single file upload
        $uploadedFile = $uploadedFiles['new_avatar'];
        if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
            try {
                $avatar = Avatar::createFromUploadedFile($uploadedFile->file);

            } catch(FileSystem $e) {

                \Module\Exception\Log::i()->add(self::SOURCE_MODULE,$e->getCode(),$e->getMessage())->error();
                $res->status = Response::STATUS_ERROR;
                $res->error = $e->getMessage();

                return $response->withJson($res);
            }

        }
        if (!$avatar)
        {
            $avatar = Avatar::getDefault();
        }

        $group = Group::createWithUserIdAndImage($user,$avatar,$body['title']);

        $data = new \stdClass();
        $data->title = $group->title;
        $data->id = $group->id();
        $data->group_avatar_url = $group->getAvatar()->getUrl();
        $res->data = $data;
        $res->status = Response::STATUS_OK;
        return $response->withJson($res);

    }

    public function put(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement put() method.
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     * @throws MySQL
     * @throws \Module\Exception\Controller
     * @OA\Patch(
     *     path="/social/group/edit/{groupID}",
     *     summary="Edit a social group",
     *     description="Edit a social group",
     *     @OA\Parameter(
     *     name="groupID",
     *     required=true,
     *     @OA\Schema(type="int"),
     *     in="path"
     * ),
     *     @OA\RequestBody(
     *     description="Input data",
     *     @OA\MediaType(
     *     mediaType="application/x-www-form-urlencoded",
     *     @OA\Schema(
     *     type="object",
     *     @OA\Property(
     *     property="new_avatar",
     *     description="Group Avatar file",
     *     type="string",
     *     format="binary"),
     *     @OA\Property(
     *     property="title",
     *     type="string",
     *     description="title of group")
     * )
     * )
     * ),
     *     security={"accountBearer"},
     *     @OA\Response(
     *     response=200,
     *     description="Response after submitting user request",
     *     @OA\JsonContent(ref="#/components/schemas/Response")
     * )
     * )

     */
    public function patch(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $res = new Response();

        if (!isset($args[0]) || intval($args[0]) < 1) {
            $res->status = Response::STATUS_INVALID_PARAM;
            return $response->withJson($res);
        } else {
            $groupId = \intval($args[0]);
        }
        $body = $request->getParsedBody();

        try
        {
            $group = Group::loadWithId($groupId);
            if ($group->owner_user_id != $this->getActiveUserId()) {

            }
        } catch (MySQL $e)
        {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE,$e->getCode(),$e->getMessage())->error();
            $res->status = Response::STATUS_ERROR;
            return $response->withJson($res);
        }
        $avatar = null;
        $uploadedFiles = $request->getUploadedFiles();

        // handle single input with single file upload
        $uploadedFile = $uploadedFiles['new_avatar'];
        if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
            try {
                $avatar = Avatar::createFromUploadedFile($uploadedFile->file);

            } catch(FileSystem $e) {

                \Module\Exception\Log::i()->add(self::SOURCE_MODULE,$e->getCode(),$e->getMessage())->error();
                $res->status = Response::STATUS_ERROR;
                $res->error = $e->getMessage();

                return $response->withJson($res);
            }

        }
        if (!$avatar)
        {
            $avatar = Avatar::getDefault();
        }

        $group = Group::createWithUserIdAndImage($user,$avatar,$body['title']);

        $data = new \stdClass();
        $data->title = $group->title;
        $data->id = $group->id();
        $data->group_avatar_url = $group->getAvatar()->getUrl();
        $res->data = $data;
        $res->status = Response::STATUS_OK;
        return $response->withJson($res);

    }
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     * @throws \Module\Exception\Controller
     * @OA\Delete(
     *     path="/social/group/edit/{groupId}",
     *     summary="deletes a social group",
     *     description="deletes a social group",
     *     @OA\Parameter(
     *     name="groupId",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="int")
     * ),
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
        // TODO: Implement delete() method.
        $res = new Response();
        try
        {
            $user = Account::loadWithId($this->getActiveUserId());
            $group_id = intval($_GET['group_id']);
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
}