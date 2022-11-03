<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/3/2019
 * Time: 1:00 AM
 */

namespace Module\Social\Group;


use Lib\Core\Helper\Db\Map\Column;
use Module\User\Account;


/**
 * Class Chat
 * @package Module\Social\Group
 * @property string $message
 * @property int $time
 * @property int $author_user_id
 * @property string $author_display_name
 * @property string $author_avatar
 * @property int $group_id
 */
class Chat extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'chat_message';
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
                new Column(self::TABLE_NAME,'message', 's'),
                new Column(self::TABLE_NAME,'time', 'i'),
                (new Column(self::TABLE_NAME,'author_user_id', 'i'))->join('id',Account::getAvatarDisplayNameColumns("author_display_name","author_avatar")),
                (new Column(self::TABLE_NAME,'social_group_id', 'i'))->setAlias("group_id"),
            ];
        }
        return static::$_column_cache;
    }

    /**
     * @param string $message
     * @param int $user_id
     * @param int $group_id
     * @return Chat
     * @throws \Module\Exception\MySQL
     */
    public static function Create(string $message, Account $user, int $group_id) {
        $obj = new self();
        $obj->message = $message;
        $obj->author_user_id = $user->id();
        $obj->author_avatar = $user->avatar_media_path;
        $obj->author_display_name = $user->display_name;
        $obj->group_id = $group_id;
        $obj->time = time();
        $obj->save();
        return $obj;
    }

    public function editMessage(string $newMessage) {
        $this->message = $newMessage;
    }

    /**
     * @param int $group_id
     * @param int $start
     * @param $end
     * @return Chat[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllWithGroupIdFrom(int $group_id,int $start,$end) {
        return static::_loadAll("social_group_id=? and ".self::TABLE_NAME.".time > ? and ".self::TABLE_NAME.".time < ? order by time asc","iii",[$group_id,$start,$end]);
    }
}


