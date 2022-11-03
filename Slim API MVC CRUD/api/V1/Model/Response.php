<?php
namespace api\V1\Model;

use OpenApi\Annotations as OA;

/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 2/11/2018
 * Time: 11:59 AM
 * @OA\Schema(
 *     type="object",
 *     @OA\Xml(name="Response")
 * )
 */
class Response extends \Lib\Core\Helper\Error {
    const STATUS_OK="OK";
    const STATUS_ERROR="ERROR";
    const STATUS_INVALID_PARAM = "INVALID_PARAM";
    const STATUS_NO_PERMISSION="NO_PERMISSION";
    const STATUS_SERVER_ERROR="SERVER_ERROR";
    const STATUS_NOT_FOUND="ID_NOT_FOUND";

    /**
     * @var array|object
     * @OA\Property(format="mixed")
     */
    public $data;
    /**
     * @var string
     * @OA\Property(
     *     format="string",
     *     enum={
     *     \Api\V1\Model\Response::STATUS_OK,
     *     \Api\V1\Model\Response::STATUS_ERROR,
     *     \Api\V1\Model\Response::STATUS_INVALID_PARAM,
     *     \Api\V1\Model\Response::STATUS_SERVER_ERROR,
     *     \Api\V1\Model\Response::STATUS_NOT_FOUND
     *     }
     * )
     */
    public $status;

    public function __construct(object $dataObj = null)
    {
        $this->status = self::STATUS_OK;
        if ($dataObj != null) $this->data = $dataObj;
    }
}