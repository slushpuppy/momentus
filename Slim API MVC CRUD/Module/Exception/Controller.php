<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 9/8/2018
 * Time: 6:21 PM
 */

namespace Module\Exception;


class Controller extends _Exception
{
    const QUERY_RETURNS_MORE_ONE_ROW=1;
    const ID_NOT_EXIST=2;
}