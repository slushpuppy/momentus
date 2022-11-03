<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/3/2019
 * Time: 12:58 AM
 */

namespace Module\Social\Group;


use Lib\Core\Helper\Db\Map\Column;

class Role extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = '';
    protected const ID_COLUMN = '';
    protected static $_column_cache = NULL;

    /**
     *
     * @return Lib\Core\Helper\Db\Map\Column[]
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                '' => new Column('', 's'),


            ];
        }
        return static::$_column_cache;
    }
}


