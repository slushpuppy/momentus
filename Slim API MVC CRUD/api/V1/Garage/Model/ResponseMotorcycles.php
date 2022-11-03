<?php


namespace api\V1\Garage\Model;


use Api\V1\Model\Response;
use OpenApi\Annotations as OA;

/**
 * Class ResponseMotorcycle
 * @package api\V1\Garage\Model
 * @OA\Schema(
 *     type="object",
 *     allOf={
 *     @OA\Schema(ref="#/components/schemas/Response")
 * },
 *     @OA\Xml(name="ResponseMotorcycle")
 * )
 */
class ResponseMotorcycles extends Response
{
    /**
     * @var Vehicle[]
     * @OA\Property( type="array",
     *     @OA\Items(ref="#/components/schemas/Vehicle")
     * )
     */
    public $data;
}