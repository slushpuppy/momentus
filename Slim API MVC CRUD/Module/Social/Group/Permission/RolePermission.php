<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/3/2019
 * Time: 12:58 AM
 */

namespace Module\Social\Group\Permission;


use Lib\Core\Helper\Db\Map\Column;

class RolePermission extends \Lib\Core\Controller\AbstractController
{

    public const TABLE_NAME = 'social_group_role_permission';
    protected const ID_COLUMN = 'id';
    protected static $_column_cache = NULL;

    /**
     *
     * @return Lib\Core\Helper\Db\Map\Column[]
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                'title' => new Column('title', 's'),
                'description' => new Column('description', 's'),
                'name' => new Column('name', 's'),
            ];
        }
        return static::$_column_cache;
    }
}


