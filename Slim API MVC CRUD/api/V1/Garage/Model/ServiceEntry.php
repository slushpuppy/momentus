<?php


namespace api\V1\Garage\Model;

use api\V1\Model\Document;
use Module\Garage\Service\Service;
use OpenApi\Annotations as OA;

/**
 * Class Service
 * @package api\V1\Garage\Model
 * @OA\Schema(
 *     type="object",
 *     @OA\Xml(name="ServiceEntry")
 * )
 */
class ServiceEntry
{
    /**
     * @var int
     * @OA\Property(type="integer",
     *     description="Only used in response body`")
     */
    public $id;

    /**
     * @var int
     * @OA\Property(type="integer", description="start time of service")
     */
    public $start_time;
    /**
     * @var int
     * @OA\Property(type="integer", description="end time of service")
     */
    public $end_time;
    /**
     * @var int
     * @OA\Property(type="string", description="review")
     */
    public $review;
    /**
     * @var int[]
     * @OA\Property(
     *      type="array",
     *      @OA\Items(
     *          type="integer"
     *      ),
     *     description="Parts list"
     * )
     */
    public $parts;
    /**
     * @var ServiceDocument[]
     * @OA\Property(
     *      type="array",
     *      @OA\Items(ref="#/components/schemas/ServiceDocument"),
     *     description="List of uploaded media documents"
     * )
     */
    public $docs;
    /**
     * @var string[]
     * @OA\Property(
     *      type="array",
     *      @OA\Items(
     *          type="string",
     *       enum={
     *     \Module\Garage\Service\Type::GENERAL,
     *     \Module\Garage\Service\Type::TUNEUP,
     *     \Module\Garage\Service\Type::FULL,
     *     \Module\Garage\Service\Type::VALVE,
     *     \Module\Garage\Service\Type::ACCIDENT,
     *     \Module\Garage\Service\Type::BREAKDOWN,
     *     \Module\Garage\Service\Type::CHECKUP,
     *     \Module\Garage\Service\Type::PREVENTIVE,
     *     \Module\Garage\Service\Type::MOD
     *
     *     }
     *      ),
     *     description="List of service types"
     * )
     */
    public $service_types;


}