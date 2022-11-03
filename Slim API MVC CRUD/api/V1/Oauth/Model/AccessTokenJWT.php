<?php


namespace api\V1\Oauth\Model;



/**
 * Class AccessTokenJWT
 * @package api\V1\Oauth\Model
 * @OA\Schema(
 *     type="object",
 *     @OA\Xml(name="AccessTokenJWT")
 * )
 */
class AccessTokenJWT
{
    /**
     * @var string
     * @OA\Property(type="string",
     *     description="access token")
     */
    public $access_token;
    /**
     * @var int
     * @OA\Property(type="int",
     *     description="expiry of jwt and access token")
     */
    public $expiry;
    /**
     * @var string
     * @OA\Property(type="string",
     *     description="access token scope separated by space")
     */
    public $scope;
    /**
     * @var string
     * @OA\Property(type="string",
     *     description="jwt session data")
     */
    public $jwt;
}