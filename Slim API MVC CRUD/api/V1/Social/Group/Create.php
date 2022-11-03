<?php


namespace Api\V1\Social\Group;


use Api\V1\Model\Response;
use api\V1\User\Model\ProfileFields;
use Module\Exception\FileSystem;
use Module\Exception\MySQL;
use Module\OAuth\Scope;
use Module\Social\Group\Avatar;
use Module\Social\Group\Group;
use Module\User\Account;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Create extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected static $permissionScope = Scope::SOCIAL_MEDIA;

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


    public function patch(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement delete() method.
    }
}