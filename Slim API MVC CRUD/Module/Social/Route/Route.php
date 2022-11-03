<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/3/2019
 * Time: 12:56 AM
 */

namespace Module\Social\Route;


use Lib\Core\Helper\Db\Map\Column;
use Module\User\Account;

/**
 * Class Route
 * @package Module\Social\Route
 * @property int $author_user_id
 * @property string $author_display_name
 * @property string $author_avatar
 * @property string $title
 * @property int $duration_mins
 * @property int $creation_time
 */
class Route extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'route';
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
                (new Column(self::TABLE_NAME, 'author_user_id','i'))->join("id",Account::getAvatarDisplayNameColumns('author_display_name','author_avatar')),
                (new Column(self::TABLE_NAME,'title','s')),
                (new Column(self::TABLE_NAME,'duration_mins','i')),
                (new Column(self::TABLE_NAME,'creation_time','i')),

            ];
        }
        return static::$_column_cache;
    }

    /**
     * @param Account $member
     * @param string $title
     * @param int $duration
     * @return Route
     * @throws \Module\Exception\MySQL
     */
    public static function createFromMember(Account $member, string $title, int $duration)
    {
        $obj = new self;
        $obj->author_display_name = $member->display_name;
        $obj->author_avatar = $member->avatar_media_path;
        $obj->author_user_id = $member->id();
        $obj->title = $title;
        $obj->duration_mins = $duration;
        $obj->creation_time = time();
        $obj->save();
        return $obj;
    }

    /**
     * @param int $memberId
     * @return Route[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllWithUserIdDesc(int $memberId)
    {
        return static::_loadAll('author_user_id=? order by id desc','i',[$memberId]);
    }
}


