<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 5/9/2018
 * Time: 11:52 PM
 */

namespace Module\User;


use Lib\Core\Helper\Db\Map\Column;

/**
 * Position
 * @property int[] $point
 * @property int $time
 * @property int $user_id
 */
class Position extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'user_position';
    protected const ID_COLUMN = 'id';
    protected static $_column_cache = NULL;
    /**
     *
     * @return \Lib\Core\Helper\Db\Map\Column[]
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                new Column(self::TABLE_NAME,'point', 'dd','POINT(?,?)','X(point) as `point[0]`,Y(point) as `point[1]`',''),
                new Column(self::TABLE_NAME,'time', 'i'),
               new Column(self::TABLE_NAME,'user_id', 'i'),

            ];
        }
        return static::$_column_cache;
    }

    public static function loadFrom(int $user_id,int $startTime,int $endTime)
    {
        return self::_load('user_id=? AND time >=? AND time<=?','iii',[$user_id,$startTime,$endTime]);
    }


}


