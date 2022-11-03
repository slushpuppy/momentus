<?php


namespace Module\Garage\Motorcycle;


use Lib\Core\Helper\Db\Map\Column;
use Module\Garage\Avatar;
use OpenApi\Annotations\Property;

/**
 * Class Motorcycle
 * @package Module\User
 * @property int $photo_media_id
 * @property string $photo_media_path
 * @property string $name
 * @property string $model_string
 * @property int $model_id
 * @property int $owner_id
 * @property string $vin
 */
class Vehicle extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'user_motorcycle';
    protected const ID_COLUMN = 'id';
    protected const SOURCE_MODULE = 'Module\Vehicle\Motorcycle';
    protected static $_column_cache = NULL;

    protected const HIDE_COLUMN="hidden";

    /**
     *
     * @return Lib\Core\Helper\Db\Map\Column[]
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL)
        {
            static::$_column_cache = [
                (new Column(self::TABLE_NAME,'name', 's'))->setMaxLength(30),
                (new Column(self::TABLE_NAME,'photo_media_id', 'i'))->join('id',(new Column('media'))->addSelectColumnAlias('path','photo_media_path')),
                (new Column(self::TABLE_NAME,'model_id', 'i')),
                (new Column(self::TABLE_NAME,'model_string', 's'))->setMaxLength(100),
                (new Column(self::TABLE_NAME,'owner_id', 'i')),
                (new Column(self::TABLE_NAME,'vin', 'i'))->setMaxLength(17),

            ];
        }
        return static::$_column_cache;
    }

    /**
     * @param int $userId
     * @param Avatar $bikePhoto
     * @param string $bikeName
     * @param string|NULL $modelString
     * @param int|NULL $modelId
     * @param string|NULL $vin
     * @return Vehicle
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     */
    public static function createWithUserID(int $userId,Avatar $bikePhoto,string $bikeName = NULL,string $modelString = NULL,int $modelId = NULL,string $vin = NULL)
    {
        $obj = new self();
        $obj->owner_id = $userId;
        $obj->name = $bikeName;
        $obj->photo_media_id = $bikePhoto->id();
        $obj->photo_media_path = $bikePhoto->getFilePath();
        $obj->model_id = $modelId;
        $obj->model_string = $modelString;
        $obj->vin = $vin;
        $obj->save();
        return $obj;
    }

    public function setBikeAvatar(Avatar $bikePhoto) {
        $this->photo_media_id = $bikePhoto->id();
        $this->photo_media_path = $bikePhoto->getFilePath();
    }

    /**
     * @return string
     */
    public function getBikeAvatarUrl() {
        return Avatar::getUrlFromPath($this->photo_media_path);
    }

    /**
     * @param int $ownerIdUser
     * @return Vehicle[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithOwnerVehicleId(int $ownerId,int $vehicleId) {
        return static::_load('owner_id=? and '.self::TABLE_NAME.'.id=?','ii',[$ownerId,$vehicleId]);
    }

    /**
     * @param int $ownerIdUser
     * @return Vehicle[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllWithOwnerUserId(int $ownerIdUser) {
        return static::_loadAll('owner_id=?','i',[$ownerIdUser]);
    }

    /**
     * @param array $ids
     * @return Vehicle[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllWithIds(array $ids) {
        $bindClause = implode(',', array_fill(0, count($ids), '?'));
        return static::_loadAll('id in ('.$bindClause.')',\str_repeat('i',count($ids)),$ids);
    }

    /**
     * @return string
     */
    public function getMotorcycleModel() {
        if ($this->model_string != null) return $this->model_string;
        $modelId = \intval($this->model_id);
        if ($modelId > 0)
        {
            $db = \Lib\Core\Helper\Db\Conn::i()->get();
            $res = $db->query(
                'SELECT f.id as family_id,f.name as family_name,mr.id as manufacturer_id,mr.name as manufacturer_name,mm.id as model_id,mm.name as model_name,mm.year as model_year FROM `motorcycle_model` mm left join motorcycle_family f on mm.motorcycle_family_id=f.id left join motorcycle_manufacturer mr on f.manufacturer_id=mr.id where mm.id='.$modelId);

            if (!$res) {
                \Module\Exception\Log::i()->add(self::SOURCE_MODULE."()", $db->errno."", $db->error)->error();
            }
            while ($row = $res->fetch_assoc()) {
                return  \str_replace("  "," ",sprintf('%s, %s %s (%s)',
                    $row['manufacturer_name'],
                    $row['family_name'],
                    $row['model_name'],
                    $row['model_year']));
            }
        }

        return '';
    }

    public function delete()
    {
        $this->_hideDelete(true);
    }

    public function getDocuments() {

    }
}


