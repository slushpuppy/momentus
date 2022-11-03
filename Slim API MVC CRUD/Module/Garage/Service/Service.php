<?php


namespace Module\Garage\Service;


use Lib\Core\Helper\Db\Map\Column;
use Module\Garage\Motorcycle\Vehicle;

/**
 * Class Service
 * @package Module\Garage\Service
 * @property int $vehicle_id
 * @property int $start_time
 * @property int $end_time
 * @property string $review
 * @property int $owner_id
 * @property int $vehicle_workshop_id
 * @property int $is_draft
 */
class Service extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'vehicle_service';
    protected const ID_COLUMN = 'id';
    protected const SOURCE_MODULE = "\Module\Garage\Service\\Service";
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
                (new Column(self::TABLE_NAME, 'vehicle_id','i'))->join('id',(new Column(Vehicle::TABLE_NAME))->addSelectColumn('owner_id')),
                (new Column(self::TABLE_NAME, 'start_time','i')),
                (new Column(self::TABLE_NAME, 'end_time','i')),
                (new Column(self::TABLE_NAME, 'review','s')),
                (new Column(self::TABLE_NAME, 'vehicle_workshop_id','i')),
                (new Column(self::TABLE_NAME, 'is_draft','i')),
            ];
        }
        return static::$_column_cache;
    }

    /**
     * @return Type[]
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     */
    public function getServiceTypes() {
        return Type::loadAllWithServiceId($this->id());
    }

    /**
     * @return ServiceDocument[]
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     */
    public function getDocuments() {
        return ServiceDocument::loadAllWithServiceId($this->id());
    }

    /**
     * @return ServicePart[]
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     */
    public function getServicedParts() {
        return ServicePart::loadAllWithServiceId($this->id());
    }

    /**
     * @param int $id
     * @return Service
     */
    public static function createWithVehicleId(int $id) {
        $ret = new static();
        $ret->vehicle_id = $id;
        $ret->setAsDraft(true);
        $ret->save();
        return $ret;
    }

    /**
     * @param int $id
     * @return Service[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllWithVehicleId(int $id) {
        return static::_loadAll('vehicle_id=?','i',[$id]);
    }

    /**
     * @param bool $true
     */
    public function setAsDraft(bool $true) {
        $this->is_draft = 1;
    }

}


