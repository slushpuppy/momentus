<?php


namespace Module\Garage\Service;


use Lib\Core\Helper\Db\Map\Column;

/**
 * Class ServicePart
 * @package Module\Garage\Service
 * @property int $service_id
 * @property int $motorcycle_part_id
 * @property string $part_name
 * @property string $part_family_name
 */
class ServicePart extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'vehicle_service_part';
    protected const ID_COLUMN = '';
    protected const SOURCE_MODULE = "Module\Garage\Service\\ServicePart";
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
                (new Column(self::TABLE_NAME, 'service_id','i')),
                (new Column(self::TABLE_NAME,'motorcycle_part_id',i))->join('id',(new Column('motorcycle_part','family_id','i'))->addSelectColumnAlias('name','part_name')->join('id',(new Column('motorcycle_part_family','name','s'))->setAlias('part_family_name')))


            ];
        }
        return static::$_column_cache;
    }

    /**
     * @param int $id
     * @param int $partId
     * @return ServicePart
     */
    public static function createWithServiceIdPartId(int $id,int $partId) {
        $ret = new static();
        $ret->service_id = $id;
        $ret->motorcycle_part_id = $partId;
        $ret->save(true);
        return $ret;
    }

    /**
     * @param int $id
     * @return ServicePart[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllWithServiceId(int $id) {
        return static::_loadAll('service_id=?','i',[$id]);
    }
}


