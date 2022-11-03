<?php


namespace api\V1\Model;

/**
 * Class Document
 * @package api\V1\Model
 * @OA\Schema(
 *     type="object",
 *     @OA\Xml(name="Document")
 * )
 */
class Document
{
    /**
     * @var int
     * @OA\Property(
     *     format="int64",
     *     description="media id")
     */
    public $id;
    /**
     * @var string
     * @OA\Property(type="string",
     *     description="Only used in response body`")
     */
    public $type;

    /**
     * @var string
     * @OA\Property(type="string", description="url")
     */
    public $url;
}