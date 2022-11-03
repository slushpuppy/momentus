<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 9/8/2018
 * Time: 12:43 AM
 */

namespace Module\OAuth;

use Lib\Core\Helper\Db\Map\Column as Column;


class AuthMethod extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'auth_method';
    protected const TABLE_COLUMN_TYPE_MAP = array(
        'name' => 's',
        );
    protected const ID_COLUMN = 'id';
    protected static $_column_cache = NULL;


    /**
     * @param string $name
     * @return $this
     * @throws \Module\Exception\MySQL
     */
    public static function loadByName(string $name) {
        return static::_load('name LIKE ?','s',[$name]);
    }


    /**
     * @return \Lib\Core\Helper\Db\Map\Column[]
     */
    protected static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache =  [
                'name' => new Column('name','s'),

            ];
        }
        return static::$_column_cache;
        // TODO: Implement getColumns() method.
    }
}