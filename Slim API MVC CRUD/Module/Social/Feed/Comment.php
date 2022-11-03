<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 7/9/2018
 * Time: 6:25 AM
 */

namespace Module\Social\Feed;


use Config\OAuth\General;
use Config\Social;
use Lib\Core\Helper\Db\Map\Column;

/**
 * Comment
 * @property int $comment_id
 * @property int $journey_id
 * @property string $post
 * @property int $time
 */
class Comment extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'social_feed_comment';
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
                'journey_id' => new Column('journey_id', 'i'),
                'post' => new Column('journey_id', 's'),
                'time' => new Column('journey_id', 'i'),

            ];
        }
        return static::$_column_cache;
    }
    public static function loadWithJourneyId(int $journey_id) {
        return self::_loadAll("journey_id=? order by time desc","i",[$journey_id]);
    }
    public function save($bool = true) {
        if (\strlen($this->post) > Social::POST_LEN) {
            throw new \Module\Exception\Social(\Module\Exception\Social::POST_LENGTH_TOO_LONG);
        }
        if (!$this->time) {
            $this->time = time();
        }
        parent::save($bool);
    }
}


