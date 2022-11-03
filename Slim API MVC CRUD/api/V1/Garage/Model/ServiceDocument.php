<?php


namespace api\V1\Garage\Model;

use api\V1\Model;

/**
 * Class ServiceDocument
 * @package api\V1\Model
 * @OA\Schema(
 *     type="object",
 *     allOf={
 *     @OA\Schema(ref="#/components/schemas/Document"),
 *     },
 *     @OA\Xml(name="ServiceDocument")
 * )
 */
class ServiceDocument extends Model\Document
{
    /**
     * @var string
     * @OA\Property(type="string",
     *     description="Document type",
     *     enum={
     *     \Module\Garage\Service\ServiceDocument::TYPE_RECEIPT,
     *     \Module\Garage\Service\ServiceDocument::TYPE_SERVICE_PHOTO,
     *     \Module\Garage\Service\ServiceDocument::TYPE_ODOMETRY
     *     }
     *     )
     */
    public $type;

}