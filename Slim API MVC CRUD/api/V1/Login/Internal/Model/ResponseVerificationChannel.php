<?php


namespace api\V1\Login\Internal\Model;


use api\V1\Model\Response;

class ResponseVerificationChannel extends Response
{
    /**
     * @var VerificationChannel
     */
    public $data;
}