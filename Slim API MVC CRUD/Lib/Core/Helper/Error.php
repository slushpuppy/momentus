<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 21/8/2018
 * Time: 11:47 PM
 */

namespace Lib\Core\Helper;


class Error
{

    const INVALID_PARAMETER_COUNT="InvalidParameterCount";

    public $error;
    public $error_uri;
    public $error_description;

    public function __construct()
    {

    }

    /**
     * @param mixed $msg
     * @return Error
     */
    public function setMsg($msg)
    {
        $this->error_description = $msg;
        return $this;
    }

    /**
     * @param mixed $code
     * @return Error
     */
    public function setCode($code)
    {
        $this->error = $code;
        return $this;
    }

    public function setUri($uri) {
        $this->error_uri = $uri;
        return $this;
    }
}