<?php
namespace Lib\Core\Helper\Http\Rest;

/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 2/11/2018
 * Time: 11:59 AM
 */
class Response extends \Lib\Core\Helper\Error {
    const STATUS_OK="OK";
    const STATUS_ERROR="ERROR";
    const STATUS_INVALID_PARAM = "INVALID_PARAM";

    public $data,$status;

    public function __construct()
    {
        $this->status = STATUS_INVALID_PARAM;
    }
}