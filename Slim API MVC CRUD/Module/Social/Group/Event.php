<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/3/2019
 * Time: 12:57 AM
 */

namespace Module\Social\Group;


use Lib\Core\Helper\Db\Map\Column;
use Module\Exception\Social;
use Module\Social\Route\Route;
use Module\User\Account;

/**
 * Class Event
 * @package Module\Social\Group
 * @property string $title
 * @property string $description
 * @property int $start_time
 * @property int $end_time
 * @property int $route_id
 * @property int $group_id
 * @property int $author_user_id
 * @property string $author_display_name
 * @property string $author_avatar
 */
class Event extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'social_group_event';
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
                new Column(self::TABLE_NAME,'title', 's'),
                new Column(self::TABLE_NAME,'description', 's'),
                new Column(self::TABLE_NAME,'start_time', 'i'),
                new Column(self::TABLE_NAME,'end_time', 'i'),
                new Column(self::TABLE_NAME,'route_id', 'i'),
                new Column(self::TABLE_NAME,'group_id', 'i'),
                (new Column(self::TABLE_NAME,'author_user_id','i'))->join(
                    'id',
                    Account::getAvatarDisplayNameColumns('author_display_name','author_avatar')
                    )

            ];
        }
        return static::$_column_cache;
    }

    /**
     * @param int $group_id
     * @return array
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllWithGroupId(int $group_id) {
        return static::_loadAll("group_id=?",'i',$group_id);
    }

    /**
     * @param Group $group
     * @param Account $member
     * @param string $title
     * @param string $description
     * @return Event
     * @throws Social
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     */
    public static function createWithGroupMemberId(Group $group,Account $member, Route $route, string $title, string $description) {

        if (!$group->isMemberIdInGroup($member->id()))
        {
            throw new Social(Social::MEMBER_IS_NOT_IN_GROUP);
        }
        $obj = new self();
        $obj->author_user_id = $member->id();
        $obj->author_avatar = $member->avatar_media_path;
        $obj->author_display_name = $member->display_name;
        $obj->title = $title;
        $obj->description = $description;
        $obj->group_id = $group->id();

        $obj->route_id = $route->id();
        $obj->save();
        return $obj;
    }
}


