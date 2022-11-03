<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 7/11/2018
 * Time: 5:54 PM
 */

namespace Module\Social\Feed;


use Lib\Core\Controller\AbstractController;
use Lib\Core\Helper\Db\Map\Column;

/**
 * Activity
 * @package Module\Social\Feed
 * @property int $user_id
 * @property int $feed_thread_id
 * @property string $activity_type_id
 * @property int $date
 */
class Activity extends AbstractController
{
    public const TABLE_NAME = 'social_feed_activity';
    protected const ID_COLUMN = 'id';
    protected static $_column_cache = NULL;
    /**
     * @return \Lib\Core\Helper\Db\Map\Column[]
     */
    protected static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                'user_id' => new Column('user_id', 'i'),
                'feed_thread_id' => new Column('feed_thread_id','i'),
                'activity_type_id' => new Column('activity_type_id','i',
                    new Column('social_feed_activity_type.name', 's', null, 'social_feed_activity_type.id')
                ),
                'date' => new Column('date','i'),
            ];
        }
        return static::$_column_cache;
    }


    /**
     * @param int $user_id
     * @param int $start_date
     * @param int $end_date
     * @return Activity[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithUserIdDateRange(int $user_id, int $start_date, int $end_date) {
        return static::_loadAll("user_id=? and date >= ? and date <= ?","iii",[
            $user_id,
            $start_date,
            $end_date
        ]);
    }

    /**
     * @param int $user_id
     * @param int $post_id
     * @param int $limit
     * @return Activity[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithUserIdAfterPostId(int $user_id, int $post_id=0,int $limit) {
        return static::_loadAll("user_id=? and id < ? LIMIT ?","iii",[
            $user_id,
            $post_id,
            $limit
        ]);
    }

    /**
     * @param int $user_id
     * @param int $post_id
     * @param int $limit
     * @return Activity[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithUserIdBeforePostId(int $user_id, int $post_id=0,int $limit) {
        return static::_loadAll("user_id=? and id > ? LIMIT ?","iii",[
            $user_id,
            $post_id,
            $limit
        ]);
    }
    /**
     * @return Thread
     */
    function getThread()
    {
        return Thread::_loadAll($this->feed_thread_id);
    }
}