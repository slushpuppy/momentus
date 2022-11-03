<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 7/9/2018
 * Time: 6:26 AM
 */

namespace Module\Social\Feed;


use Lib\Core\Controller\AbstractController;
use Lib\Core\Helper\Db\Map\Column;

class Journey extends AbstractController
{
    public const TABLE_NAME = 'social_feed_thread_journey';
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
                'time' => new Column('time','i'),
                'comment_id' => new Column('comment_id','i'),
                'thread_id' => new Column('thread_id','i'),
            ];
        }
        return static::$_column_cache;
    }

    /**
     * @return Position[]
     */

    function getAllPositions()
    {
        return Position::_loadAll("thread_journey_id=?","i",[$this->id()]);
    }

    function getAllComments()
    {
        return Comment::_loadAll("journey_id=?","i",[
            $this->id()
        ]);
    }
}


