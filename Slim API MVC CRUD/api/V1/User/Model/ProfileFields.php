<?php


namespace api\V1\User\Model;

use OpenApi\Annotations as OA;
/**
 * Class ProfileFields
 * @package api\V1\User\Model
 * @OA\Schema(
 *     type="object",
 *     @OA\Xml(name="ProfileFields")
 * )
 */
class ProfileFields
{
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
}