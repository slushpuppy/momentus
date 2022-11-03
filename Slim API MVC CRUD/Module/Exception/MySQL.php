<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 7/8/2018
 * Time: 11:27 PM
 */

namespace Module\Exception;


use Throwable;

class MySQL extends \Exception
{
    /**
     * MySQL constructor.
     * @param \Lib\Core\Helper\Db\mysqli|\Lib\Core\Helper\Db\mysqli_stmt $obj
     * @param Throwable|null $previous
     */
    public function __construct($obj, Throwable $previous = null)
    {
        parent::__construct($obj->error, $obj->errno, $previous);
    }
}