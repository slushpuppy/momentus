<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 21/8/2018
 * Time: 7:08 PM
 */

namespace Module\OAuth\Handler;


class Data implements IData
{
    public function __construct(Array $properties=array())
    {
        foreach ($properties as $key => $value) {
            $this->{$key} = $value;
        }
    }
}