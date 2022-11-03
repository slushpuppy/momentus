<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 9/8/2018
 * Time: 12:29 AM
 */

namespace Module\Exception;


class _Exception extends \Exception
{
    public function __construct($error_code)
    {
        parent::__construct($this->getConstantNameByValue($error_code), $error_code, null);
    }
    public function getConstantNameByValue($value) {
        $class = new \ReflectionClass($this);
        $constants = array_flip($class->getConstants());
        return $constants[$value];
    }
}