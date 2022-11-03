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
 * AccessToken
 * @property string $token
 * @property int $user_id
 * @property int $expiry
 * @property string $auth_type
 * @property string $refresh_token
 */
class AccessToken extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'auth_oauth_access_token';

    private static $_column_cache = NULL;

    protected const ID_COLUMN = 'id';

    public function generateTokenHex() {
        $bytes = \openssl_random_pseudo_bytes(\Config\OAuth\General::TOKEN_BYTE_LENGTH, $cstrong);
        return bin2hex($bytes);
    }

    public static function create(int $user_id) {
        $obj = new self;
        $obj->user_id = $user_id;
        $obj->token = $obj->generateTokenHex();
        $obj->expiry = time() + \Config\OAuth\General::TOKEN_LIFESPAN_SECS;
        $obj->refresh_token = $obj->generateTokenHex();
        $obj->auth_type = 'internal';
        $obj->save(true);
        return $obj;
    }

    private static function createWithFacebook(int $user_id,string $token,int $expiry,string $refresh_token)
    {
        return static::_createWithDetails($user_id,$token,$expiry,$refresh_token,'facebook');
    }
    private static function _createWithDetails(int $user_id,string $token,int $expiry,string $refresh_token,string $type) {
        $obj = new self;
        $obj->user_id = $user_id;
        $obj->token = $token;
        $obj->expiry = $expiry;
        $obj->refresh_token = $refresh_token;
        $obj->auth_type = $type;
        $obj->save(true);
        return $obj;
    }
    public function updateToken() {
        $this->token = $this->generateTokenHex();
        $this->expiry = time() + \Config\OAuth\General::TOKEN_LIFESPAN_SECS;
        $this->refresh_token = $this->generateTokenHex();
        $this->save();
    }
    public static function loadWithUserId(int $id,string $auth_type)
    {
        return static::_load('user_id=? AND auth_type_id=(select id from auth_method where name=?)','is',[$id,$auth_type]);
    }

    /**
     * @param string $token
     * @return AccessToken|null
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithToken(string $token) {
        return static::_load('token=?','s',[$token]);
    }

    /**
     * @param string $token
     * @return AccessToken|null
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithRefreshToken(string $token) {
        return static::_load('refresh_token=?','s',[$token]);
    }

    /**
     * @return AccessTokenScope[]
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     */
    public function getScope() {
        return AccessTokenScope::loadAllWithTokenId($this->id());
    }

    private $_scopeCache = null;

    /**
     * @return string
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     */
    public function getScopeForPayload() {
        if ($this->_scopeCache == null)
        {
            $this->_scopeCache = '';
            $scopes = $this->getScope();
            foreach ($scopes as $scope)
            {
                $this->_scopeCache .= $scope->token_name.' ';
            }
            if (\strlen($this->_scopeCache) > 0)
            $this->_scopeCache = \substr($this->_scopeCache,0,-1);
        }
        return $this->_scopeCache;
    }

    /**
     * @param string $token
     * @param string $scope
     * @param string $auth_type
     * @return AccessToken|null
     * @throws \Module\Exception\MySQL

    public static function loginWithToken(string $token, string $scope, string $auth_type) {

        $db = Conn::i()->get();
        $time = \time();

        $scope_array = explode(',',$scope);
        $bindClause = implode(',', array_fill(0, count($scope_array), '?'));
        $bindString = str_repeat('s', count($scope_array));
        $stmt = $db->prepare('SELECT user_id,token,expiry,refresh_token,m.name as auth_type_id from '.self::TABLE_NAME.' t left join auth_oauth_access_token_scope s on s.auth_oauth_access_token_id=t.id left join auth_oauth_scope ss on s.auth_oauth_scope_id=ss.id left join auth_method m on t.auth_type_id=m.id where t.token=? and t.expiry>=? and m.name=? AND ss.name in ('.$bindClause.') LIMIT 1');

        var_dump('SELECT user_id,token,expiry,refresh_token,m.name as auth_type_id from '.self::TABLE_NAME.' t left join auth_oauth_access_token_scope s on s.auth_oauth_access_token_id=t.id left join auth_oauth_scope ss on s.auth_oauth_scope_id=ss.id left join auth_method m on t.auth_type_id=m.id where t.token=? and t.expiry>=? and m.name=? AND ss.name in ('.$bindClause.') LIMIT 1');

        var_dump($bindString);
        var_dump($scope_array);

        $stmt->bind_param('sis'.$bindString,$token,$time,$auth_type,...$scope_array);
        $stmt->execute();

        $out = [];
        $stmt->stmt_bind_assoc($out);
        $stmt->execute();
        if ($stmt->num_rows <= 0) return null;

        while ($stmt->fetch());
        $obj = new self();
        $obj->_loadData($out);
        return $obj;
    }*/

    protected static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                new Column(static::TABLE_NAME,'user_id', 'i'),
                new Column(static::TABLE_NAME,'token', 's'),
                new Column(static::TABLE_NAME,'expiry', 'i'),
                new Column(static::TABLE_NAME,'refresh_token', 's'),
                (new Column(static::TABLE_NAME,'auth_type_id', 'i'))->join('id',
                    (new Column('auth_method','name', 's'))->setAlias('auth_type'))

                ,

            ];
        }
        return static::$_column_cache;
/*    protected const TABLE_COLUMN_TYPE_MAP = array(
        'user_id' => 'i',
        'token' => 's',
        'expiry' => 'i',
        'refresh_token' => 's',
        'auth_type_id' => array(
            'type' => 's',
            'join_col' => 'auth_method.id',
            'join_param' => 'auth_method.name'
        )
    );
 * */
    }
}
