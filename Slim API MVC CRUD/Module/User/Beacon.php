<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 26/2/2019
 * Time: 1:18 AM
 */

namespace Module\User;

use Lib\Core\Helper\Db\Map\Column;

/**
 * Beacon
 * @property int $user_id
 * @property int $rust_beacon_id
 * @property int $date
 * @property string $passkey
 */
class Beacon extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'user_beacon';
    protected const ID_COLUMN = 'id';
    protected static $_column_cache = NULL;

    /**
     * @param int $id
     * @return Beacon[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllWithUserId(int $id)
    {
        return static::_loadAll(static::TABLE_NAME.'.user_id'.'=?','i',[$id]);
    }

    /**
     *
     * @return \Lib\Core\Helper\Db\Map\Column[]
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                'user_id' => new Column('user_id', 'i'),
                'rust_beacon_id' => (new Column('rust_beacon_id', 'i'))->addSelectColumn('rust_beacon.hardware_version')->addSelectColumn('rust_beacon.mac_address'),
                'user_id' => new Column('user_id', 'i'),
                'date' => new Column('date', 'i'),
                'passkey' => new Column('passkey', 's'),

            ];
        }
        return static::$_column_cache;
    }

    public static function loadFrom(int $user_id, int $startTime, int $endTime)
    {
        return self::_load('user_id=? AND time >=? AND time<=?', 'iii', [$user_id, $startTime, $endTime]);
    }
}
