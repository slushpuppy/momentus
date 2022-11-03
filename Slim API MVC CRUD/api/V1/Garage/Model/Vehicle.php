<?php


namespace api\V1\Garage\Model;

use OpenApi\Annotations as OA;

/**
 * Class Vehicle
 * @package api\V1\Garage\Model
 * @OA\Schema(
 *     type="object",
 *     @OA\Xml(name="Vehicle")
 * )
 */
class Vehicle
{
    /**
     * @var int
     * @OA\Property(type="integer",
     *     description="Only used in response body`")
     */
    public $id;

    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $name;

    /**
     * @var string
     * @OA\Property(
     *     type="string",
     *     description="Vehicle Avatar url - Only in response body"
     * )
     */
    public $avatar_url;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $model;

    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $model_id;
    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $owner_id;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $vin;


    /**
     * @var string
     * @OA\Property(type="string",
     *     description="Vehicle Avatar file - Only in request body"
     *  )
     */
    public $new_avatar;
}