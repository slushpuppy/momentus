<?php


namespace api\V1\Garage;


use api\V1\Garage\Model\ResponseMotorcycle;
use api\V1\Garage\Model\ResponseMotorcycles;
use api\V1\Garage\Model\ResponseServiceEntry;
use api\V1\Garage\Model\ServiceDocument;
use api\V1\Garage\Model\ServiceEntry;
use api\V1\Model\Document;
use Api\V1\Model\Response;
use http\Exception\UnexpectedValueException;
use Lib\Core\Common;
use Module\Exception\FileSystem;
use Module\Exception\InvalidData;
use Module\Exception\InvalidPermission;
use Module\Garage\Motorcycle\Vehicle;
use Module\OAuth\Scope;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use OpenApi\Annotations as OA;

class Service extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected const SOURCE_MODULE = 'Api\V1\Garage\Service';
    protected static $permissionScope = Scope::GARAGE;

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

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     * @OA\Get(
     *     path="/garage/service/{bikeID}/{serviceID}",
     *     summary="Get service Information",
     *     description="Get service Information",
     *     security={"accountBearer"},
     * @OA\Parameter(
     *     name="serviceID",
     *     required=false,
     * @OA\Schema(type="int"),
     *     in="path"
     * ),
     * @OA\Parameter(
     *     name="bikeID",
     *     required=false,
     * @OA\Schema(type="int"),
     *     in="path"
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Response after Creating motorcycle vehicle",
     *     @OA\JsonContent(ref="#/components/schemas/ResponseServiceEntry")
     * )
     * )
     * )
     *
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement get() method.
        $res = new ResponseServiceEntry();
        try
        {
            $userId = $this->getActiveUserId();

            $svcId = 0;
            if (count($args) >= 1)
            {
                $bikeId = $args[0];
                if (isset($args[1]))
                {
                    $svcId = $args[1];
                }

            } else {
                throw new \UnexpectedValueException();
            }


            $bike = Vehicle::loadWithOwnerVehicleId($userId,$bikeId);
            if (!$bikeId) {
                $res->status = Response::STATUS_NO_PERMISSION;
                $res->error = "No Permission";
            }
            else {
                /** @var \Module\Garage\Service\Service[] $entries */
                $entries = [];
                if ($svcId > 0)
                {
                    $entries[] = \Module\Garage\Service\Service::loadWithId($svcId);
                } else {
                    $entries = \Module\Garage\Service\Service::loadAllWithVehicleId($bikeId);
                }
                /** @var ServiceEntry[] $data */
                $data = [];
                //var_dump()
                foreach($entries as $svc) {
                    if ($svc != null) {
                        $tp = new ServiceEntry();
                        $tp->id = $svc->id();
                        $tp->start_time = $svc->start_time;
                        $tp->end_time = $svc->end_time;
                        $tp->review = $svc->review;
                        $parts = [];
                        foreach($svc->getServicedParts() as $part) {
                            $parts[] = $part->part_name.'('.$part->part_family_name.')';
                        }
                        $tp->parts = $parts;

                        $docs = [];
                        foreach($svc->getDocuments() as $doc) {
                            $d = new Document();
                            $d->type = $doc->media_type;
                            $d->url = $doc->getUrl();
                            $docs[] = $d;
                        }
                        $tp->docs = $docs;

                        $svcTypes = [];

                        foreach($svc->getServiceTypes() as $type) {
                            $svcTypes[] = $type->type;
                        }
                        $tp->service_types = $svcTypes;
                    }
                    $data[] = $tp;
                }
                $res->data = $data;
                $res->status = Response::STATUS_OK;

            }

        }catch (\Throwable $e) {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE, $e->getCode(), $e->getMessage())->error();
            $res->status = Response::STATUS_ERROR;
            $res->error = 'Unknown error';
        }
        finally {
            return $response->withJson($res);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     * @OA\Post(
     *     path="/garage/service/{bikeID}/{serviceID}",
     *     summary="Create a new bike or update existing bike in garage",
     *     description="Create a new bike or update existing bike  in garage",
     *     security={"accountBearer"},
     * @OA\Parameter(
     *     name="serviceID",
     *     required=false,
     * @OA\Schema(type="int"),
     *     in="path"
     * ),
     * @OA\Parameter(
     *     name="bikeID",
     *     required=true,
     * @OA\Schema(type="int"),
     *     in="path"
     * ),
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *     mediaType="multipart/form-data",
     *     @OA\JsonContent(ref="#/components/schemas/RequestServiceEntry")
     * )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Response after Creating motorcycle vehicle",
     *     @OA\JsonContent(ref="#/components/schemas/ResponseServiceEntry")
     * )
     * )
     *
     */
    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        try
        {
            $res = new ResponseServiceEntry();
            $activeUser = $this->getActiveUserId();
            if (isset($args[1]) && intval($args[1]) > 0)
            {
                $service = \Module\Garage\Service\Service::loadWithId($args[0]);
                if ($service->owner_id != $activeUser)
                {
                    throw new InvalidPermission();
                }
            } else if (isset($args[0]) && \intval($args[0]) > 0)
            {
                $service = \Module\Garage\Service\Service::createWithVehicleId(\intval($args[0]));
            } else
            {
                throw new InvalidData('No vehicle ID');
            }
                $serviceBody = new \api\V1\Garage\Model\RequestServiceEntry();
                Common::copyArrayToObjectProperties($request->getParsedBody(),$serviceBody);
                //error_log(print_r($vehicleBody,TRUE),0);
                $avatar = null;
                $uploadedFiles = $request->getUploadedFiles();

                if (count($uploadedFiles['docs']) != count($serviceBody->docs_type))
                {
                    throw new InvalidData('docs count not equals to docs type count');
                }

                // handle single input with single file upload
                $docs = [];
                if (\is_array($uploadedFiles['docs']))
                foreach ($uploadedFiles['docs'] as $index => $uploadedFile)
                {
                    if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK)
                    {
                        $doc = \Module\Garage\Service\Document::createFromUploadedFile($uploadedFile->file);
                        $svcDoc = \Module\Garage\Service\ServiceDocument::createWithServiceId($service->id(),$doc,$serviceBody->docs_type[$index],time());
                        if ($svcDoc)
                        {
                            $d = new ServiceDocument();
                            $d->type = $svcDoc->media_type;
                            $d->url = $doc->getUrl();
                            $docs[] = $d;
                        }

                    }
                }

            if (\is_array($serviceBody->service_types))
                foreach ($serviceBody->service_types as $type)
                {
                    try
                    {
                        $svc_type = \Module\Garage\Service\Type::createWithServiceId($service->id(), $type);
                    } catch (\Exception $e) {}
                }

            if (\is_array($serviceBody->parts))
                foreach ($serviceBody->parts as $part)
                {
                    try
                    {
                        $part = \Module\Garage\Service\ServicePart::createWithServiceIdPartId($service->id(), $part);
                    } catch (\Exception $e) {}
                }

                $serviceBody->setPropertiesIfExists($service);


                $service->setAsDraft(($serviceBody->is_draft == 1)? true : false);

                $service->save(true);
//error_log(print_r($vehicle,TRUE),0);
                //       error_log(print_r($vehicle,TRUE),0);
                $data = new \api\V1\Garage\Model\ServiceEntry();
                $data->id = $service->id();
                $data->docs = $docs;

                $res->data = $data;
                $res->status = Response::STATUS_OK;


        } catch(FileSystem $e) {
            $res->status = Response::STATUS_ERROR;
            $res->error = $e->getMessage();
        }
        catch(InvalidPermission $e) {
            $res->status = Response::STATUS_NO_PERMISSION;
            $res->error = "";
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


    private function patchMultiPart(ServerRequestInterface $request, ResponseInterface $response, array $args) {
        try
        {
            $res = new Res();
            $bikeId = \intval($args[0]);
            $bike = Vehicle::loadWithId($bikeId);

            if (!$bike) {
                $res->status = Response::STATUS_NOT_FOUND;
                throw new \InvalidArgumentException();
            }

            if ($bike->owner_id != $this->getActiveUserId())
            {
                $res->status = Response::STATUS_NO_PERMISSION;
                throw new \Module\Exception\Response(Response::STATUS_NO_PERMISSION);
            }

            $body = $request->getParsedBody();

            $avatar = null;
            $changed = false;
            $uploadedFiles = $request->getUploadedFiles();

            // handle single input with single file upload
            $uploadedFile = $uploadedFiles['new_avatar'];

            //error_log(print_r($uploadedFile,TRUE),0);
            if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK)
            {
                try
                {
                    $avatar = \Module\Garage\Avatar::createFromUploadedFile($uploadedFile->file);
                    $bike->setBikeAvatar($avatar);
                    $changed = true;

                } catch (FileSystem $e)
                {
                    \Module\Exception\Log::i()->add(self::SOURCE_MODULE, $e->getCode(), $e->getMessage())->error();
                }

            }

            if (\is_array($body))
                foreach($body as $field => $value) {
                    if (isset($bike->$field) && !\in_array($field,['photo_media_id', 'photo_media_path'])) {

                        $bike->$field = $value;
                        $changed = true;
                    }
                }
            if ($changed)
                $bike->save(true);


            $data = new \api\V1\Garage\Model\Vehicle();
            $data->id = $bike->id();
            $data->name = $bike->name;
            $data->vin = $bike->vin;
            $data->owner_id = $bike->owner_id;
            $data->avatar_url = $bike->getBikeAvatarUrl();
            $data->model = $bike->getMotorcycleModel();
            $data->model_id = $bike->model_id;

            $res->data = $data;
            $res->status = Response::STATUS_OK;

        } catch (\Throwable $e) {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE."(".$e->getFile().")", $e->getLine(), $e->getMessage())->error();
        }
        finally {
            return $res;
        }
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
     *     path="/garage/motorcycle/{serviceId}",
     *     summary="Deletes service",
     *     description="Deletes service",
     *     security={"accountBearer"},
     * @OA\Parameter(
     *     name="bikeID",
     *     required=true,
     * @OA\Schema(type="int"),
     *     in="path"
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Response after updating motorcycle vehicle",
     *     @OA\JsonContent(ref="#/components/schemas/Response")
     * )
     * )
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        try
        {
            $res = new Response();
            if (!isset($args[0]) || intval($args[0]) < 1) {
                $res->status = Response::STATUS_INVALID_PARAM;
                throw new \InvalidArgumentException();
            }
            $bikeId = \intval($args[0]);

            $bike = Vehicle::loadWithId($bikeId);

            if (!$bike) {
                $res->status = Response::STATUS_NOT_FOUND;
                throw new \InvalidArgumentException();
            }
            if ($bike->owner_id != $this->getActiveUserId())
            {
                $res->status = Response::STATUS_NO_PERMISSION;
                throw new \Module\Exception\Response(Response::STATUS_NO_PERMISSION);
            }

            $bike->delete();

        } catch (\Throwable $e) {
            \Module\Exception\Log::i()->add(self::SOURCE_MODULE, $e->getCode(), $e->getMessage())->error();
        }
        finally {
            return $response->withJson($res);
        }
    }


}