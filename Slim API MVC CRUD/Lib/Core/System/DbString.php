<?php


namespace Lib\Core\System;


use Lib\Core\Helper\Db\Map\Column;

/**
 * Class DbString
 * @package Lib\Core\System
 * @property string $name
 */
class DbString extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'sys_string_index';
    protected const ID_COLUMN = 'id';
    protected const SOURCE_MODULE = 'Lib\Core\System\DbString';
    protected static $_column_cache = NULL;

    /**
     *
     * @return Lib\Core\Helper\Db\Map\Column[]
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL)
        {
            static::$_column_cache = [
                (new Column(self::TABLE_NAME, 'name','s')),
            ];
        }
        return static::$_column_cache;
    }
}


