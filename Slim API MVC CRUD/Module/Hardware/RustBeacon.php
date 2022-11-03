<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 27/2/2019
 * Time: 9:07 PM
 */

namespace Module\Hardware;

use Lib\Core\Helper\Db\Map\Column;

/**
 * RustBeacon
 * @property int $id
 * @property string $mac_address
 * @property int $hardware_version
 */
class RustBeacon extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'rust_beacon';
    protected const ID_COLUMN = 'id';
    protected static $_column_cache = NULL;

    /**
     * @param int $id
     * @return RustBeacon
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithUid(int $macAddrInHex,int $hardwareVersion)
    {
        return static::_load(static::TABLE_NAME . '.mac_address' . '=? AND'.static::TABLE_NAME . '.hardware_version' . '=?', 'bi', [pack("H*", $macAddrInHex),$hardwareVersion]);
    }

    /**
     *
     * @return \Lib\Core\Helper\Db\Map\Column[]
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                'id' => new Column('id', 'i'),
                'mac_address' => new Column('mac_address', 'b'),
                'hardware_version' => new Column('hardware_version', 'i')
            ];
        }
        return static::$_column_cache;
    }
}