<?php


namespace Module\User\Profile;


use Lib\Core\Helper\Db\Map\Column;
use Module\FileSystem\File;
use Module\FileSystem\Type\Image;
use Module\User\Document;

/**
 * Class Document
 * @package Module\User\Profile
 * @property int $user_id
 * @property string $media_path
 */
class AccountDocument extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'user_document';
    protected const ID_COLUMN = '';
    protected const SOURCE_MODULE = "Module\User\\Document";
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
                (new Column(self::TABLE_NAME, 'user_id','i')),
                (new Column(self::TABLE_NAME, 'media_id','i'))->join('id',(new Column('media'))->addSelectColumnAlias('path','media_path')),


            ];
        }
        return static::$_column_cache;
    }

    public function id() {
        return $this->media_id;
    }

    /**
     * @param int $user_id
     * @return AccountDocument[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllWithUserId(int $user_id) {
        return static::_loadAll('user_id=?','i',[$user_id]);
    }

    /**
     * @param int $id
     * @return AccountDocument|null
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithId(int $id)
    {
        return static::_load('media_id=?','i',[$id]);
    }


    /**
     * @param string $blob
     * @param int $user_id
     * @return AccountDocument
     */
    public static function createWithUserId(Document $doc, int $user_id)
    {
        $obj = new self();

        $obj->media_id = $doc->id();
        $obj->media_path = $doc->path;
        $obj->user_id = $user_id;
        $obj->save(true);
        return $obj;
    }




    /**
     * @return int
     */
    public function getUserId() {
        return $this->user_id;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return Document::getUrlFromPath($this->media_path);
    }
}


