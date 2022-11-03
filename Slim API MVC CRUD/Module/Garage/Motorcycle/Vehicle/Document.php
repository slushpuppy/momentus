<?php


namespace Module\Garage;


use Lib\Core\Helper\Db\Map\Column;

/**
 * Class Document
 * @package Module\Garage
 * @property int $user_motorcycle_id
 * @property string $media_path
 */
class Document extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'user_motorcycle_document';
    protected const ID_COLUMN = '';
    protected const SOURCE_MODULE = 'Module\Garage\VehicleDocument';
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
                (new Column(self::TABLE_NAME, 'user_motorcycle_id','i')),
                (new Column(self::TABLE_NAME, 'media_id','i'))->join('id',(new Column('media'))->addSelectColumnAlias('path','media_path')),

            ];
        }
        return static::$_column_cache;
    }
}


