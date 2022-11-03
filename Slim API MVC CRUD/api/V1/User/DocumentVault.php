<?php


namespace api\V1\User;


use Api\V1\Model\Response;
use api\V1\User\Model\ResponseProfile;
use Module\Exception\FileSystem;
use Module\Exception\InvalidPermission;
use Module\Exception\MySQL;
use Module\OAuth\Scope;
use Module\User\Account;
use Module\User\Profile\AccountDocument;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DocumentVault extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected static $permissionScope = Scope::PROFILE;
    protected const SOURCE_MODULE = 'api\V1\User\DocumentVault';

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

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     * @OA\Delete(
     *     path="/user/document_vault/{mediaID}",
     *     summary="Delete document from vault",
     *     description="Delete document from vault",
     *     security={"accountBearer"},
     * @OA\Parameter(
     *     name="mediaID",
     *     required=true,
     * @OA\Schema(type="int"),
     *     in="path"
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Response after deleting document",
     *     @OA\JsonContent(ref="#/components/schemas/Response")
     * )
     * )
     * )
     *
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $res = new Response();
        try
        {
            if (isset($args[0]) && intval($args[0]) > 0)
            {
                $doc = AccountDocument::loadWithId($args[0]);
                if ($doc->user_id != $this->getActiveUserId()) {
                    throw new InvalidPermission();
                }

                $doc->delete();

                $res->data = null;
                $res->status = Response::STATUS_OK;
            }




        } catch (MySQL $e)
        {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE,$e->getCode(),$e->getMessage())->error();
            $res->status = Response::STATUS_ERROR;
        } catch(FileSystem $e)
        {
            $res->status = Response::STATUS_ERROR;
            $res->error = $e->getMessage();
        }
        catch (InvalidPermission $e) {
            $res->status = Response::STATUS_NO_PERMISSION;
            $res->error = Response::STATUS_NO_PERMISSION;
        }
        catch (\Throwable $e) {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE, $e->getLine(), $e->getMessage())->error();
            $res->status = Response::STATUS_ERROR;
            $res->error = 'Unknown error';
        }
        finally
        {
            return $response->withJson($res);
        }
    }
}