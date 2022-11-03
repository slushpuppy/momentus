<?php
namespace api\V1\Search\Model;

/**
 * Class MotorcycleModel
 * @package api\V1\Search\Model
 * @OA\Schema(
 *     type="object",
 *     @OA\Xml(name="MotorcycleModel")
 * )
 */
class MotorcycleModel {
    /**
     * @var int
     * @OA\Property(type="int",
     *     description="motorcycle id")
     */
    public $id;
    /**
     * @var string
     * @OA\Property(type="string",
     *     description="motorcycle name")
     */
    public $name;
}