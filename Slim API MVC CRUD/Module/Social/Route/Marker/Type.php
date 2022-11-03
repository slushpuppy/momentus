<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 4/4/2019
 * Time: 1:46 PM
 */

namespace Module\Social\Route\Marker;


use Lib\Core\Helper\Db\Map\Column;

class Type extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'route_marker_type';
    protected const ID_COLUMN = 'id';
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
                new Column(self::TABLE_NAME,'name', 's'),
            ];
        }
        return static::$_column_cache;
    }
}


