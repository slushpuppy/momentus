<?php


namespace Module\Garage\Service;


use Lib\Core\Helper\Db\Map\Column;

/**
 * Class Type
 * @package Module\Garage\Service
 * @property int $vehicle_service_id
 * @property string $type
 */
class Type extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'vehicle_service_type';
    protected const ID_COLUMN = '';
    protected const SOURCE_MODULE = "Module\Garage\Service\\Type";
    protected static $_column_cache = NULL;

    public const GENERAL="general_service";
    public const FULL="full_service";
    public const VALVE="valve_clearance";
    public const ACCIDENT="accident_repair";
    public const BREAKDOWN="breakdown_repair";
    public const TUNEUP="tune_up";
    public const CHECKUP="checkup";
    public const PREVENTIVE="preventive_repair";
    public const MOD="modification";


    /**
     *
     * @return Lib\Core\Helper\Db\Map\Column[]
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL)
        {
            static::$_column_cache = [
                (new Column(self::TABLE_NAME, 'vehicle_service_id','i')),
                (new Column(self::TABLE_NAME, 'service_type_string_id','i'))->join('id',Column::getSysTextColumn()->setAlias('type')),

            ];
        }
        return static::$_column_cache;
    }

    /**
     * @param int $serviceId
     * @param string $type
     * @return Type
     */
    public static function createWithServiceId(int $serviceId,string $type) {
        $ret = new static();
        $ret->vehicle_service_id = $serviceId;
        $ret->type = $type;
        $ret->save(true);
        return $ret;
    }

    /**
     * @param int $serviceId
     * @return Type[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllWithServiceId(int $serviceId) {
        return static::_loadAll('vehicle_service_id=?','i',[$serviceId]);
    }
}


