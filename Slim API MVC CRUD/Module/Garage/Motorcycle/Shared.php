<?php


namespace Module\Garage\Motorcycle;


use Lib\Core\Helper\Db\Map\Column;
use Module\Garage\Motorcycle\Vehicle;

/**
 * Class Shared
 * @package Module\Garage
 * @property int $user_id
 * @property int $user_motorcycle_id
 */
class Shared extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'user_motorcycle_shared';
    protected const ID_COLUMN = '';
    protected const SOURCE_MODULE = 'Module\Garage';
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
                (new Column(self::TABLE_NAME,'user_id', 'i')),
                (new Column(self::TABLE_NAME,'user_motorcycle_id', 'i'))


            ];
        }
        return static::$_column_cache;
    }

    /**
     * @param int $userId
     * @param int $vehicleId
     * @return Shared|null
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithUserVehicleId(int $userId,int $vehicleId) {
        return static::_load('user_id=? and user_motorcycle_id=?','ii',[$userId,$vehicleId]);
    }
    /**
     * @param int $userId
     * @param int $vehicleID
     * @return Shared
     * @throws \Module\Exception\MySQL
     */
    public static function createWithUserVehicleId(int $userId,int $vehicleID) {
        $obj = new self();
        $obj->user_id = $userId;
        $obj->user_motorcycle_id = $vehicleID;
        $obj->save(true);
        return $obj;
    }
}


