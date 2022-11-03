<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/11/2018
 * Time: 3:05 PM
 */

namespace Lib\Core\Helper\Db\Map;


class ColumnStringIndex extends Column
{
    public function __construct()
    {
        parent::__construct('sys_string_index', 'name', 's');
    }
}