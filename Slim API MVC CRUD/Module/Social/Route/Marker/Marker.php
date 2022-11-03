<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 4/4/2019
 * Time: 11:43 AM
 */

namespace Module\Social\Route\Marker;


use Lib\Core\Helper\Db\Map\Column;

/**
 * Class Marker
 * @package Module\Social\Route
 * @property int $route_id
 * @property string $marker_name
 */
class Marker extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'route_marker';
    protected const ID_COLUMN = 'id';
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
                new Column(self::TABLE_NAME,'route_id', 'i'),
                new Column(self::TABLE_NAME,'point', 'dd','POINT(?,?)','X(point) as `point[0]`,Y(point) as `point[1]`',''),
                (new Column(self::TABLE_NAME,'marker_type_id', 'i'))->join('id',(new Column(Type::TABLE_NAME,'name','s'))->setAlias('marker_name')),


            ];
        }
        return static::$_column_cache;
    }

    /**
     * @param int $routeId
     * @param int $x
     * @param int $y
     * @param string $marker_name Marker name from Module\Social\Route\Marker\Name
     */
    public static function createFromRouteId(int $routeId,int $x,int $y,string $marker_name)
    {
        $obj = new self();
        $obj->setPoint($x,$y);
        $obj->marker_name = $marker_name;
        $obj->route_id = $routeId;
        $obj->save();
        return $obj;
    }

    /**
     * @return float
     */
    public function getPointX() {
        return $this->point[0];
    }

    /**
     * @return float
     */
    public function getPointY() {
        return $this->point[1];
    }

    /**
     * @param $x
     * @param $y
     * @return void
     */
    public function setPoint($x,$y) {
        $this->point = [$x,$y];
    }
}


