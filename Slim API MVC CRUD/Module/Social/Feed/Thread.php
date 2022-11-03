<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 7/9/2018
 * Time: 6:25 AM
 */

namespace Module\Social\Feed;


use Lib\Core\Controller\AbstractController;
use Lib\Core\Controller\AbstractNodeController;
use Lib\Core\Helper\Db\Map\Column;

/**
 * Thread
 * @property int $user_id
 * @property string $title;
 * @property int $comment_count
 * @property int $view_count
 * @property int $like_count
 */
class Thread extends AbstractController
{
    public const TABLE_NAME = 'social_feed_thread';
    protected const ID_COLUMN = 'id';
    protected static $_column_cache = NULL;

    /**
     *
     * @return Lib\Core\Helper\Db\Map\Column[]
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                'user_id' => new Column('user_id', 'i'),
                'title' => new Column('title', 's'),
                'meta_data' => new Column('meta_data', 's'),
            ];
        }
        return static::$_column_cache;
    }

    /**
     * @return Journey[]
     */
    public function getAllJourney()
    {
        return Journey::_loadAll("thread_id=?","i",[$this->id()]);
    }


}


