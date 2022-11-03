<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 1/4/2019
 * Time: 10:01 PM
 */

namespace Module\Social\Group\Permission;


use Lib\Core\Helper\Db\Map\Column;

/**
 * Class MemberRole
 * @package Module\Social\Group\Permission
 * @property int $group_member_id
 * @property string $role_permission_name
 * @property-read string $title
 */
class MemberRole extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'social_group_role_assigned_permission';
    protected const ID_COLUMN = '';
    protected static $_column_cache = NULL;

    /**
     *
     * @return Lib\Core\Helper\Db\Map\Column[]
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                new Column(self::TABLE_NAME,'group_member_id', 'i'),
                (new Column(self::TABLE_NAME,'role_permission_id', 'i'))->join('id',(new Column('social_group_role_permission','name','s'))->setAlias('role_permission_name')->addSelectColumn('title'))
            ];
        }
        return static::$_column_cache;
    }

    public static function createUsingGroupMemberId(int $groupMemberId,string $permName) {
        $obj = new self();
        $obj->group_member_id = $groupMemberId;
        $obj->role_permission_name = $permName;
        $obj->save(true);
        return $obj;
    }

    /**
     * @param int $groupMemberId
     * @return MemberRole[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllWithUserId(int $groupMemberId) {
        var_dump($groupMemberId);
        return self::_loadAll('group_member_id=?','i',[$groupMemberId]);
    }

}


