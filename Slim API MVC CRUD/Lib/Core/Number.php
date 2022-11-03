<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 7/8/2018
 * Time: 10:25 PM
 */

namespace Lib\Core;


class Number
{
    public static function isFloat($f)
    {
        return ($f == (string)(float)$f);
    }
    function isInteger($input){
        return(ctype_digit(strval($input)));
    }
}