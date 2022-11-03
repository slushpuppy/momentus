<?php


namespace api\V1\User\Model;


use api\V1\Model\Document;
use OpenApi\Annotations as OA;
/**
 * Class ProfileFields
 * @package api\V1\User\Model
 * @OA\Schema(
 *     type="object",
 *     @OA\Xml(name="Profile")
 * )
 */
class Profile
{
    /**
     * @var int
     * @OA\Property(type="int",
     *     description="profile ID - only used for other profile viewing"
     *  )
     */
    public $id;


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
     * @var Document[]
     * @OA\Property(
     *      type="array",
     *      @OA\Items(ref="#/components/schemas/Document"),
     *     description="List of uploaded documents from document vault"
     * )
     */
    public $document_vault;

}