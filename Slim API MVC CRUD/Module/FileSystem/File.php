<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 14/11/2018
 * Time: 6:04 PM
 */

namespace Module\FileSystem;


use Lib\Core\Controller\AbstractController;
use Lib\Core\Helper\Db\Map\ColumnStringIndex;
use Module\Exception\FileSystem;
use Lib\Core\Helper\Db\Map\Column;

/**
 * File
 * @property string $path
 * @property double $time
 */
class File extends AbstractController
{

    public const TABLE_NAME = 'media';
    protected const ID_COLUMN = 'id';
    protected static $_column_cache = NULL;

    public $blob = NULL,
        $extension = '',
        $size = 0,
        $checksum;
    protected $uploadFilePath = NULL;

    const MAX_SIZE = "5000000";

    /**
     * @param File $obj
     * @return void
     */
    public static function loadFromController(AbstractController $obj,int $fileId) {


        $return = new static();
        $array = [
            "time" => $obj->media_time,
            "path" => $obj->media_path,
            static::ID_COLUMN => $fileId
        ];
        $return->_loadData($array);
        $return->extension = substr(strrchr($obj->media_path, '.'),1);
        return $return;
    }


    /**
     * @param string $path
     * @return File
     */
    public static function createFromUploadedFile(string $path)
    {
        $return = new static();
        $return->size = \filesize($path);
        $return->checksum = \sha1_file($path);
        $return->uploadFilePath = $path;
        $return->save();
        return $return;

    }

    public static function createFromStaticFile(string $path) {
        $return = new static();
        $return->size = \filesize($path);
        $return->extension = \substr(strrchr($path, '.'),1);
        $return->checksum = \basename($path,".".$return->extension);
        $return->save(true);
        return $return;
    }

    public static function createFromBlob(string $blob)
    {
        $return = new static();
        $return->blob = $blob;
        $return->size = strlen($blob);
        $return->checksum = \sha1($blob);
        $return->save();
        return $return;
    }

    /**
     * @return bool
     * @throws FileSystem
     */
    public function save(bool $updateIfFound = true)
    {
        if ($this->time == NULL)
            $this->time = time();

        if ($this->size > static::MAX_SIZE) {
            throw new FileSystem(FileSystem::FILE_TOO_LARGE);
        }

        $this->path = $this->getFilePath();

        /*if (!\is_int($this->class_handler_id)) {
            throw new FileSystem(FileSystem::INVALID_SAVE_OBJECT);
        }*/
        if ($this->blob != NULL) {

           // \error_log($this->getSavePath().' '.get_called_class(),0);
           // \error_log(print_r(debug_backtrace(),TRUE),0);
            if (!\file_put_contents($this->getSavePath(),$this->blob)) {
                throw new FileSystem(FileSystem::SAVE_ERROR);
            }
        }
        if ($this->uploadFilePath != NULL) {
            \move_uploaded_file($this->uploadFilePath,$this->getSavePath());
        }



        parent::save($updateIfFound);
    }


    public function setExtensionType(string $extension) {
        $this->extension = $extension;
    }

    /**
     * @return string
     */
    public function getSavePath()
    {
        return \Config\File::SAVE_PATH.$this->getFilePath();
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->checksum.'.'.$this->extension;
    }


    /**
     * @return \Lib\Core\Helper\Db\Map\Column[]
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                new Column(static::TABLE_NAME,'path', 's'),
                new Column(static::TABLE_NAME,'time', 'i'),
            ];
        }
        return static::$_column_cache;
    }

    public function getUrl()
    {
        return \Config\File::DOMAIN . \Config\File::SAVE_PATH . $this->path;
    }
}