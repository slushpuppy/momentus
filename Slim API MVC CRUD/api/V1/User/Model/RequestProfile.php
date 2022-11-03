<?php


namespace api\V1\User\Model;

use api\V1\Model\Document;
use Module\Garage\Service\Service;
use OpenApi\Annotations as OA;

/**
 * Class Service
 * @package api\V1\Garage\Model
 * @OA\Schema(
 *     type="object",
 *     @OA\Xml(name="RequestProfile")
 * )
 */
class RequestProfile extends \Lib\Core\Helper\Http\Request
{
    /**
     * @var string
     * @OA\Property(type="string",
     *     description="Vehicle Avatar file - Only in request body"
     *  )
     */
    public $new_avatar;
    /**
     * @var bool
     * @OA\Property(type="int",
     *     description="set to 1 to remove existing avatar"
     *  )
     */
    public $remove_avatar;
    /**
     * @var string
     * @OA\Property(format="string")
     */
    public $avatar_url;
    /**
     * @var string
     * @OA\Property(format="string")
     */
    public $display_name;
    /**
     * @var string
     * @OA\Property(format="string")
     */
    public $first_name;
    /**
     * @var string
     * @OA\Property(format="string")
     */
    public $last_name;
    /**
     * @var string
     * @OA\Property(format="string")
     */
    public $phone_number;
    /**
     * @var string
     * @OA\Property(format="string")
     */
    public $email;
    /**
     * @var string
     * @OA\Property(format="string")
     */
    public $country_name;

    /**
     * @var string[]
     * @OA\Property(
     *      type="array",
     *      @OA\Items(type="string", format="binary"),
     *     description="List of uploaded documents from document vault"
     * )
     */
    public $document_vault;
}