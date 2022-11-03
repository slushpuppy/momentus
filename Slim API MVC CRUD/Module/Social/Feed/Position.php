<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 20/11/2018
 * Time: 3:58 PM
 */

namespace Module\Social\Feed;

use FileSystem\FileInterface;
use Lib\Core\Helper\Db\Map\Column;
use Lib\Core\Controller\AbstractController;
use Lib\Core\Controller\AbstractNodeController;
use Module\FileSystem\File;

/**
 * Position
 * @property double $user_position_id
 * @property double $thread_journey_id
 */
class Position extends AbstractController implements FileInterface
{
    public const TABLE_NAME = 'social_feed_journey_position';
    protected static $_column_cache = NULL;

    protected const ID_COLUMN = 'user_position_id';

    /**
     * @return \Lib\Core\Helper\Db\Map\Column[]
     */
    protected static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                'user_position_id' => new Column('user_position_id','d'),
                'thread_journey_id' => new Column('thread_journey_id','d',(new Column('user_position.data', 's', null, 'user_position.user_id'))->setSelectColumns([
                    'user_position.time',
                ])),
            ];
        }
        return static::$_column_cache;
    }

    /**
     * Get current object class name
     * @return string
     */
    public function FileGetClassHandler()
    {
        return '\Module\Social\Feed\Position';
    }

    /**
     * Get ID of the class name
     * @return int
     */
    public function FileGetClassHandlerId()
    {
        return $this->id();
    }

    protected $files = [];
    /**
     * @param File $file
     * @return void
     */
    public function FileAdd(File $file)
    {
        $this->files[] = $file;
        // TODO: Implement FileAdd() method.
    }

    /**
     * @param File[] $files
     * @return void
     */
    public function FileSetFiles(array $files)
    {
        $this->files = $files;
        // TODO: Implement FileSetFiles() method.
    }

    /**
     * @return File[]
     */
    public function FileGetAll()
    {
        return $this->files;
        // TODO: Implement FileGetAll() method.
    }
}