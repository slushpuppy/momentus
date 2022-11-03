<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 19/11/2018
 * Time: 4:11 PM
 */

namespace Module\Exception;


use Throwable;

class NotImplemented extends \Exception
{
    public function __construct()
    {
        parent::__construct("Not Implemented", 0, "");
    }
}