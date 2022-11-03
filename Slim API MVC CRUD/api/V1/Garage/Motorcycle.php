<?php


namespace api\V1\Garage;


use api\V1\Garage\Model\ResponseMotorcycle;
use api\V1\Garage\Model\ResponseMotorcycles;
use Api\V1\Model\Response;
use Lib\Core\Common;
use Module\Exception\FileSystem;
use Module\Garage\Motorcycle\Vehicle;
use Module\OAuth\Scope;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use OpenApi\Annotations as OA;

class Motorcycle extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected const SOURCE_MODULE = 'Api\V1\Garage\Motorcycle';
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
     *     path="/garage/motorcycle/{bikeID}",
     *     summary="Get Bike Information",
     *     description="Get Bike Information",
     *     security={"accountBearer"},
     * @OA\Parameter(
     *     name="bikeID",
     *     required=false,
     * @OA\Schema(type="int"),
     *     in="path"
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Response after Creating motorcycle vehicle",
     *     @OA\JsonContent(ref="#/components/schemas/ResponseMotorcycles")
     * )
     * )
     * )
     *
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement get() method.
        $res = new ResponseMotorcycles();
        try
        {
            $userId = $this->getActiveUserId();
            $bikes = [];

            if (isset($args[0]) && intval($args[0]) > 0)
            {
                $bikes[] = Vehicle::loadWithOwnerVehicleId($userId, intval($args[0]));
            }
            else
            {
                $bikes = Vehicle::loadAllWithOwnerUserId($userId);
            }

            foreach ($bikes as $bike) {
                $veh = new \api\V1\Garage\Model\Vehicle();
                //\error_log(print_r($bike,TRUE),0);
                Common::copyObjectProperties($bike,$veh);
                $veh->avatar_url = $bike->getBikeAvatarUrl();
                $veh->model = $bike->getMotorcycleModel();
                $res->data[] = $veh;
            }
            $res->status = Response::STATUS_OK;

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
     *     path="/garage/motorcycle",
     *     summary="Create a new bike in garage",
     *     description="Create a new bike in garage",
     *     security={"accountBearer"},
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *     mediaType="multipart/form-data",
     *     @OA\JsonContent(ref="#/components/schemas/Vehicle")
     * )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Response after Creating motorcycle vehicle",
     *     @OA\JsonContent(ref="#/components/schemas/ResponseMotorcycle")
     * )
     * )
     *
     */
    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        try
        {
            $res = new ResponseMotorcycle();
            if (isset($args[0]) && intval($args[0]) > 0) {
                $res = $this->patchMultiPart($request,$response,$args);
            } else {
                $vehicleBody = new \api\V1\Garage\Model\Vehicle();
                Common::copyArrayToObjectProperties($request->getParsedBody(),$vehicleBody);
                //error_log(print_r($vehicleBody,TRUE),0);
                $avatar = null;
                $uploadedFiles = $request->getUploadedFiles();

                // handle single input with single file upload
                $uploadedFile = $uploadedFiles['new_avatar'];
                if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK)
                {
                    $avatar = \Module\Garage\Avatar::createFromUploadedFile($uploadedFile->file);
                }
                if (!$avatar)
                {
                    $avatar = \Module\Garage\Avatar::getDefault();
                }

                $vehicle = Vehicle::createWithUserID($this->getActiveUserId(),
                    $avatar,
                    $vehicleBody->name,
                    $vehicleBody->model,
                    $vehicleBody->model_id,
                    $vehicleBody->vin
                );
//error_log(print_r($vehicle,TRUE),0);
                //       error_log(print_r($vehicle,TRUE),0);
                $data = new \api\V1\Garage\Model\Vehicle();
                $data->id = $vehicle->id();
                $data->name = $vehicle->name;
                $data->vin = $vehicle->vin;
                $data->owner_id = $vehicle->owner_id;
                $data->avatar_url = $vehicle->getBikeAvatarUrl();
                $data->model = $vehicle->getMotorcycleModel();
                $data->model_id = $vehicle->model_id;

                $res->data = $data;
                $res->status = Response::STATUS_OK;
            }


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

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     * @OA\Post(
     *     path="/garage/motorcycle/{bikeID}",
     *     summary="Update existing bike in garage",
     *     description="Update existing bike in garage",
     *     security={"accountBearer"},
     * @OA\Parameter(
     *     name="bikeID",
     *     required=true,
     * @OA\Schema(type="int"),
     *     in="path"
     * ),
     * @OA\RequestBody(
     *     description="Input data",
     *     @OA\MediaType(
     *     mediaType="multipart/form-data",
     *     @OA\JsonContent(ref="#/components/schemas/Vehicle"),
     * )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Response after updating motorcycle vehicle",
     *     @OA\JsonContent(ref="#/components/schemas/ResponseMotorcycle")
     * )
     * )
     *
     */
    private function patchMultiPart(ServerRequestInterface $request, ResponseInterface $response, array $args) {
        try
        {
            $res = new ResponseMotorcycle();
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
     *     path="/garage/motorcycle/{bikeID}",
     *     summary="Deletes bike",
     *     description="Deletes bike",
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