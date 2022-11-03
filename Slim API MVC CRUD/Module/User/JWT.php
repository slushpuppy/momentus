<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 8/10/2018
 * Time: 10:17 PM
 */

namespace Module\User;

/**
 * @OA\Schema(
 *     type="object",
 *     @OA\Xml(name="JWT")
 * )
 */
class JWT
{
    /**
     * @var string
     * @OA\Property(type="string",
     *     description="Only used in response body`")
     */
    public $token;

    /**
     * @var string
     * @OA\Property(type="string",
     *     description="Only used in response body`")
     */
    public $exp;
}