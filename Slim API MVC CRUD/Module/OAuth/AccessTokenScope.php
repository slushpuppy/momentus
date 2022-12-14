<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 7/8/2018
 * Time: 8:19 PM
 */

namespace Module\OAuth;


use Lib\Core\Helper\Db\Conn;
use Lib\Core\Helper\Db\Map\Column as Column;


/**
 * AccessTokenScope
 * @property string $token_name
 * @property int $token_id
 */
class AccessTokenScope extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'auth_oauth_access_token_scope';

    protected static $_column_cache = NULL;

    /**
     * @param int $token_id
     * @param string $scope
     * @return AccessTokenScope
     * @throws \Module\Exception\MySQL
     */
    public static function create(int $token_id,string $scope) {
        $obj = new self;
        $obj->token_id = $token_id;
        $obj->token_name = $scope;
        $obj->save(true);
        return $obj;
    }

    public function _loadData(array $array)
    {
        parent::_loadData($array); // TODO: Change the autogenerated stub
        if (\is_numeric($this->token_name)) {
            $this->token_name = $this->getScopeNameById($this->token_name);
        }
    }

    /**
     * @param int $id
     * @return AccessTokenScope[]
     * @throws \Module\Exception\MySQL
     */
    public static function loadAllWithTokenId(int $id) {
        return static::_loadAll('auth_oauth_access_token_id=?','i',[$id]);
    }

    /**
     * @param int $id
     * @return AccessTokenScope|null
     * @throws \Module\Exception\MySQL
     */
    public static function loadByTokenId(int $id)
    {
        $return = self::_load('auth_oauth_access_token_id=?','i',[$id]);
        return $return;
    }

    /**
     * Init class functions
     * @return void
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                (new Column(static::TABLE_NAME,'auth_oauth_access_token_id', 'i'))->setAlias('token_id'),
                (new Column(static::TABLE_NAME,'auth_oauth_scope_id', 'i'))->setAlias('token_name')

            ];
        }
        return static::$_column_cache;
        /*    protected const TABLE_COLUMN_TYPE_MAP = array(
        'auth_oauth_scope_id' => array(
            'type' => 's',
            'join_col' => 'auth_oauth_scope.id',
            'join_param' => 'auth_oauth_scope.name'
        ),
        'auth_oauth_access_token_id' => 'i',
    );
         * */
        // TODO: Implement init() method.
    }

    /**
     * @param int $id
     * @return string
     */
    public function getScopeNameById(int $id) {
        foreach(Scope::Indexes as $name => $index) {
            if ($index == $id) return $name;
        }
        throw new \OutOfBoundsException();
    }


    public function save(bool $updateIfFound = true)
    {
        $this->token_name = Scope::Indexes[$this->token_name];
        parent::save($updateIfFound); // TODO: Change the autogenerated stub
    }
}
