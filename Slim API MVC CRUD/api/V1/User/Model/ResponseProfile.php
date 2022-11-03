<?php


namespace api\V1\User\Model;


use api\V1\Model\Response;

/**
 * Class ProfileGet
 * @package Api\V1\User
 * @OA\Schema(
 *     type="object",
 *     allOf={
 *     @OA\Schema(ref="#/components/schemas/Response"),
 *     },
 *     @OA\Xml(name="ResponseProfile")
 * )
 */
class ResponseProfile extends Response {
    /**
     * @var ProfileFields
     * @OA\Property(ref="#/components/schemas/Profile")
     */
    public $data;
}