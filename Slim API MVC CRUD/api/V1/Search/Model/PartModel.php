<?php


namespace api\V1\Search\Model;

/**
 * Class PartModel
 * @package api\V1\Search\Model
 * @OA\Schema(
 *     type="object",
 *     @OA\Xml(name="PartModel")
 * )
 */
class PartModel
{
    /**
     * @var id
     * @OA\Property(type="int",
     *     description="Id of model")
     */
    public $id;
    /**
     * @var string
     * @OA\Property(type="string",
     *     description="model component name")
     */
    public $name;
}