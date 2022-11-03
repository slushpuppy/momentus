<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 26/3/2019
 * Time: 12:17 AM
 */

namespace Module\Social\Group;


use Lib\Core\Helper\Db\Conn;
use Lib\Core\Helper\Db\Map\Column;
use Module\FileSystem\Type\Image;
use Module\Notification\Websocket\Channel;
use Module\Social\Group\Permission\MemberRole;
use Module\Social\Group\Permission\Name;
use Module\User\Account;

/**
 * Class Group
 * @package Module\Social\Group
 * @property string $title
 * @property int $creation_date
 * @property string $owner_display_name
 * @property string $owner_avatar
 * @property string $owner_user_id
 * @property string $media_path
 * @property string $invite_code
 * @property int $media_time
 */
class Group extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'social_group';
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
                new Column(self::TABLE_NAME,'creation_date', 'i'),
                (new Column(self::TABLE_NAME,'owner_user_id', 'i'))->join('id', Account::getAvatarDisplayNameColumns('owner_display_name','owner_avatar')),
                (new Column(self::TABLE_NAME,'avatar_media_id', 'i'))->join('id',Image::getDbColumnJoin('group_avatar')),
                new Column(self::TABLE_NAME,'invite_code', 's'),
            ];
        }
        return static::$_column_cache;
    }


    /**
     * @param int $id
     * @return Group[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllGroupsWithOwnerUserId(int $id) {
        return static::_loadAll('owner_user_id=?','i',[$id]);
    }
    private $_avatarCache = NULL;

    /**
     * @return null|Image
     */
    public function getAvatar() {
        if ($this->_avatarCache == NULL) {
            $this->_avatarCache = Image::loadFromController($this,$this->avatar_media_id);
        }

        return $this->_avatarCache;
    }

    public function setAvatar(Image $image) {
        $this->avatar_media_id = $image->id();
        $this->media_time = $image->time;
        $this->media_path = $image->path;
        $this->_avatarCache = $image;
    }

    /**
     * @param Account $account
     * @param Avatar $image
     * @param string $title
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     * @return Group
     */
    public static function createWithUserIdAndImage(Account $account,Avatar $image, string $title) {
        $db = Conn::i()->get();
        $db->autocommit(false);
        $obj = new self();
        $obj->setAvatar($image);
        $obj->owner_display_name = $account->display_name;
        $obj->owner_user_id = $account->id();
        $obj->title = $title;
        $obj->creation_date = time();
        $obj->save();

        Member::createWithMemberGroup($account,$obj);
        MemberRole::createUsingGroupMemberId($obj->id(),Name::OWNER);
        $db->commit();
        $db->autocommit(true);
        return $obj;
    }

    /**
     * @param Account $account
     * @param string $title
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     * @return Group
     */
    public static function createWithUserId(Account $account, string $title) {
        $db = Conn::i()->get();
        $db->autocommit(false);
        $obj = new self();
        $obj->owner_display_name = $account->display_name;
        $obj->owner_user_id = $account->id();
        $obj->title = $title;
        $obj->creation_date = time();
        $obj->save();

        Member::createWithMemberGroup($account,$obj);
        MemberRole::createUsingGroupMemberId($obj->id(),Name::OWNER);
        $db->commit();
        $db->autocommit(true);
        return $obj;
    }

    /**
     * @param int $start
     * @param int $end
     * @return Chat[]
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     */
    public function getChatMessagesFrom(int $start,int $end) {
        return Chat::loadAllWithGroupIdFrom($this->id(),$start,$end);
    }

    /**
     * @return Member[]
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     */
    public function getMembers() {
        return Member::_loadAll('social_group_id=?','i',[$this->id()]);
    }

    /**
     * @param int $memberID
     * @return bool
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     */
    public function isMemberIdInGroup(int $memberID) {
        return Member::loadWithMemberGroupId($memberID,$this->id()) != null;
    }


    public function generateInviteCode() {
        $this->invite_code = random_bytes(10);
    }

    public function save(bool $updateIfFound = true)
    {
        if (empty( $this->invite_code)) {
            $this->generateInviteCode();
        }
        parent::save($updateIfFound); // TODO: Change the autogenerated stub
    }

}


