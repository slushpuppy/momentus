<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 7/9/2018
 * Time: 6:26 AM
 */

namespace Module\Social\Feed;


use Lib\Core\Helper\Db\Map\Column;

class Media extends \Lib\Core\Controller\AbstractController
{
    protected const TABLE_NAME = '';
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


