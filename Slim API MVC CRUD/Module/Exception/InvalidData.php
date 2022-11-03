<?php


namespace Module\Exception;


class InvalidData extends \Exception
{
    public function __construct()
    {
        parent::__construct("Invalid Data", 0, null);
    }
}