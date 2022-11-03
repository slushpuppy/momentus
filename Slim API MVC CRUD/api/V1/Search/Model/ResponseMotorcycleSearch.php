<?php


namespace api\V1\Search\Model;

use Api\V1\Model\Response;

/**
 * Class ResponseMotorcycle
 * @package api\V1\Garage\Model
 * @OA\Schema(
 *     type="object",
 *     @OA\Xml(name="ResponseMotorcycleSearch")
 * )
 */
class ResponseMotorcycleSearch extends Response
{
    /**
     * @var MotorcycleModel[]
     * @OA\Property( type="array",
     *     @OA\Items(ref="#/components/schemas/MotorcycleModel")
     * )
     */
    public $data;
}