<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 20/3/2019
 * Time: 4:21 PM
 */

namespace api\V1\User;


use Api\V1\Model\Response;
use api\V1\User\Model\ProfileFields;
use api\V1\User\Model\ResponseProfile;
use Lib\Core\Common;
use Module\Exception\FileSystem;
use Module\Exception\MySQL;
use Module\OAuth\Scope;
use Module\User\Account;
use Module\User\Avatar;
use Module\User\Document;
use Module\User\Profile\AccountDocument;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Profile extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected static $permissionScope = Scope::PROFILE;

    protected const SOURCE_MODULE = 'Api\V1\User\Profile';

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
     *     path="/user/profile",
     *     summary="Get All User Profile information",
     *     description="Update user profile",
     *     security={"accountBearer"},
     *     @OA\Response(
     *     response=200,
     *     description="Response after submitting user request",
     *     @OA\JsonContent(ref="#/components/schemas/ResponseProfile")
     * )
     * )
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement get() method.
        $res = new ResponseProfile();
        try
        {
            $user = Account::loadWithId($this->getActiveUserId());
        } catch (MySQL $e)
        {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE,$e->getCode(),$e->getMessage())->error();
            $res->status = Response::STATUS_ERROR;
            return $response->withJson($res);
        }
        $pf = new \api\V1\User\Model\Profile();
        $pf->phone_number = $user->phone_number;
        $pf->avatar_url = $user->getAvatarUrl();
        $pf->country_name = $user->country_name;
        $pf->display_name = $user->display_name;
        $pf->first_name = $user->first_name;
        $pf->last_name = $user->last_name;
        $pf->email = $user->email;

        $docs = $user->getDocuments();

        foreach($docs as $doc) {
            $tp = new \api\V1\Model\Document();
            $tp->url = $doc->getUrl();
            $tp->id = $doc->id();
            $pf->document_vault[] = $tp;
        }

        $res->data = $pf;
        $res->status = Response::STATUS_OK;
        return $response->withJson($res);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     * @throws MySQL
     * @OA\Post(
     *     path="/user/profile",
     *     summary="Update user avatar",
     *     description="Update user avatar",
     *     security={"accountBearer"},
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *     mediaType="multipart/form-data",
     *     @OA\Schema(ref="#/components/schemas/RequestProfile")
     * )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Response after submitting user request",
     *     @OA\JsonContent(ref="#/components/schemas/ResponseProfile")
     * )
     * )
     */
    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $res = new ResponseProfile();
        try
        {

                $profile = new \api\V1\User\Model\RequestProfile();
                $body = $request->getParsedBody();
                Common::copyArrayToObjectProperties($body,$profile);
                $avatar = null;
                $uploadedFiles = $request->getUploadedFiles();

                $user = Account::loadWithId($this->getActiveUserId());

                // handle single input with single file upload

                $changed = false;

                $uploadedFile = $uploadedFiles['new_avatar'];
                if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK)
                {
                    $avatar = Avatar::createFromUploadedFile($uploadedFile->file);
                    $user->setAvatar($avatar);
                    $changed = true;
                }
                if ($user->avatar_media_path == '' || $profile->remove_avatar)
                {
                    $avatar = Avatar::getDefault();
                    $user->setAvatar($avatar);
                    $changed = true;
                }

                $docs = $uploadedFiles['document_vault'];
                if (\is_array($docs)) {
                    foreach($docs as $doc) {
                        if ($doc && $doc->getError() === UPLOAD_ERR_OK)
                        {
                            $doc = Document::createFromUploadedFile($doc->file);
                            $accDoc = AccountDocument::createWithUserId($doc,$user->id());
                        }
                    }
                }

                $user->display_name = $profile->display_name;



                if (\is_array($body))
                foreach($body as $field => $value) {
                    if (isset($user->$field) && !\in_array($field,['photo_media_id', 'photo_media_path'])) {

                        $user->$field = $value;
                        $changed = true;
                    }
                }
            if ($changed)
                $user->save(true);

                $pf = new \api\V1\User\Model\Profile();
                $pf->display_name = $user->display_name;
                $pf->email = $user->email;
                $pf->avatar_url = $user->getAvatarUrl();
                $pf->last_name = $user->last_name;
                $pf->first_name = $user->first_name;
                $pf->country_name = $user->country_name;
                $pf->phone_number = $user->phone_number;


                $res->data = $pf;
                $res->status = Response::STATUS_OK;


        } catch(FileSystem $e) {
            $res->status = Response::STATUS_ERROR;
            $res->error = $e->getMessage();
        }
        catch (\Throwable $e) {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE, $e->getLine(), $e->getMessage())->error();
            $res->status = Response::STATUS_ERROR;
            $res->error = 'Unknown error';
        } finally {
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
    }
}

