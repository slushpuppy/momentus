<?php


namespace api\V1\Oauth\Model;


use Api\V1\Model\Response;
use Module\User\JWT;
/**
 * Class ResponseJWT
 * @package api\V1\Oauth\Model
 * @OA\Schema(
 *     type="object",
 *     allOf={
 *     @OA\Schema(ref="#/components/schemas/Response"),
 *     },
 *     @OA\Xml(name="ResponseJWT")
 * )
 */
class ResponseJWT extends Response
{
    /**
     * @var JWT
     * @OA\Property(ref="#/components/schemas/JWT"
     * )
     */
    public $data;
}
