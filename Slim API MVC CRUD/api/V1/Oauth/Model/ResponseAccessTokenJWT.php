<?php


namespace api\V1\Oauth\Model;


use api\V1\Model\Response;

/**
 * Class ResponseAccessTokenJWT
 * @package api\V1\Oauth\Model
 * @OA\Schema(
 *     type="object",
 *     allOf={
 *     @OA\Schema(ref="#/components/schemas/Response"),
 *     },
 *     @OA\Xml(name="ResponseAccessTokenJWT")
 * )
 */
class ResponseAccessTokenJWT extends Response
{
    /**
     * @var AccessTokenJWT
     * @OA\Property(ref="#/components/schemas/AccessTokenJWT"
     * )
     */
    public $data;
}
