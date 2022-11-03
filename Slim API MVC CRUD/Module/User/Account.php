<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 7/8/2018
 * Time: 8:23 PM
 */

namespace Module\User;


use Api\V1\Oauth\Authenticate;
use Config\File;
use Firebase\JWT\ExpiredException;
use Lib\Core\Helper\Db\Conn;
use Lib\Core\Helper\Db\Map\Column;
use \Firebase\JWT\JWT;
use Lib\Core\Http;
use Module\FileSystem\Type\Image;
use Module\Notification\Websocket\Channel;
use Module\Notification\Websocket\IChannel;
use Module\OAuth\JWT\Payload;
use Module\User\Profile\AccountAvatar;
use Module\User\Profile\AccountDocument;
use UnexpectedValueException;


/**
 * Account
 * @property string $email
 * @property string $display_name
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 * @property string $country_name
 * @property int $avatar_media_id
 * @property string $avatar_media_path
 */
class Account extends \Lib\Core\Controller\AbstractController implements IChannel
{
    const SOURCE_MODULE = 'Module/User/Account';

    public const TABLE_NAME = 'user';

    protected const ID_COLUMN = 'id';
    protected static $_column_cache = NULL;
    /**
     * @param $email
     * @param $display_name
     * @param $first_name
     * @param $last_name
     * @param $auth_type
     * @param $country
     * @return Account
     * @throws \Module\Exception\General
     */
    public static function create($email, $display_name, $first_name, $last_name,$phone_number,$country) {
        $obj = new self;
        $obj->first_name = $first_name;
        $obj->last_name = $last_name;
        $obj->email = $email;
        $obj->display_name = $display_name;
        $obj->phone_number = $phone_number;
        $obj->country_name = $country;
        $image = Avatar::createFromStaticFile(File::SAVE_PATH.'/image/profile/avatar/default.png');
        //var_dump($image);
        $obj->avatar_media_id = $image->id();
        $obj->avatar_media_path = $image->path;
        try {
            $obj->save(false);
        }  catch (\Exception $e) {
            \Module\Exception\Log::i()->addException($e)->general();
            throw new \Module\Exception\User\Account(
                \Module\Exception\User\Account::USER_EXISTS
            );
        }
        return $obj;
    }

    /**
     * @param string $access_token
     * @return Account|null
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithToken(string $access_token) {
        $token = \Module\OAuth\AccessToken::loadWithToken($access_token);
        if (!empty($token) && $token->user_id > 0) {
            return \Module\User\Account::loadWithId($token->user_id);
        } else return null;
    }

    /**
     * @param string $refresh_token
     * @return Account|null
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithRefreshToken(string $refresh_token) {
        $token = \Module\OAuth\AccessToken::loadWithRefreshToken($refresh_token);
        if (!empty($token) && $token->user_id > 0) {
            return \Module\User\Account::loadWithId($token->user_id);
        } else return null;
    }

    /**
     * @param string $email
     * @param string $phone_number
     * @return Account|null
     * @throws \Module\Exception\MySQL
     */
    public static function load(string $email,string $phone_number)
    {
        return self::_load('email=? OR phone_number=?','ss',[$email,$phone_number]);
    }

    /**
     * @param string $email
     * @return Account|null
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithEmail(string $email)
    {
        return self::_load('email=?','s',[$email]);
    }

    /**
     * @param string $scope
     * @return Payload
     */
    public static function verifyJWTNoScope() {
        $jwt = Http::getJWTAuthorizationToken();

        if ($jwt == '') return null;
        try {
            $decoded = JWT::decode($jwt, \Config\OAuth\General::JWT_SECRET_KEY, array('HS256'));
            if ($decoded != null) {
                return new Payload($decoded->user_id,$decoded->ip_address, $decoded->jti, $decoded->exp);

            }
        }catch (\Exception $e) {
            \Module\Exception\Log::i()->add(static::SOURCE_MODULE,'',$e->getMessage())->warning();

        }catch (ExpiredException $e) {
            \Module\Exception\Log::i()->add(static::SOURCE_MODULE,'',$e->getMessage())->warning();
        }
        catch (\UnexpectedValueException $t) {
            \Module\Exception\Log::i()->add(static::SOURCE_MODULE,'',$t->getMessage())->warning();
        }
        catch (\Throwable $t)
        {
            \Module\Exception\Log::i()->add(static::SOURCE_MODULE,'',$t->getMessage())->error();
        }
        return null;

    }

    /**
     * @param string $scope
     * @return Payload
     */
    public static function verifyJWT(string $scope) {
        $payload = static::verifyJWTNoScope();
        if ($payload) {
            if (in_array($scope,explode(' ',$payload->jti))) {
                return $payload;
            } else {
                throw new \Module\Exception\OAuth\NotInScopeException();
            }

        }

        return null;

    }

    /**
     * @return AccountDocument[]
     * @throws \Module\Exception\Controller
     * @throws \Module\Exception\MySQL
     */
    public function getDocuments() {
        return AccountDocument::loadAllWithUserId($this->id());
    }

    /**
     *
     */
    public function renewToken() {

    }

    /**
     * @param Authenticate $auth
     * @return \Module\User\JWT|null
     * @throws \Module\Exception\Controller
     */
    public function createSession(Authenticate $auth) {
        $time = time()+240;
        $payload = new Payload($this->id(),Http::getIPAddress(),$auth->scope,$time);

        try{
            $jwt = JWT::encode($payload, \Config\OAuth\General::JWT_SECRET_KEY);
            $return = new \Module\User\JWT();
            $return->token = $jwt;
            $return->exp = $time;

            return $return;
        }catch (UnexpectedValueException $e) {
            \Module\Exception\Log::i()->add(static::SOURCE_MODULE,'',$e->getMessage())->error();
        }
        return null;
    }

    /**
     * return string
     */
    public function getAvatarUrl() {
        return Avatar::getUrlFromPath($this->avatar_media_path);
    }

    public function setAvatar(Avatar $avatar) {
        $this->avatar_media_id = $avatar->id();
        $this->avatar_media_path = $avatar->path;
    }

    /**
     * Init class functions
     * @return void
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                new Column(self::TABLE_NAME,'first_name', 's'),
                new Column(self::TABLE_NAME,'last_name', 's'),
                new Column(self::TABLE_NAME,'display_name', 's'),
                new Column(self::TABLE_NAME,'email', 's'),
                new Column(self::TABLE_NAME,'phone_number', 's'),
                (new Column(self::TABLE_NAME,'country_id','i'))->join('id',(new Column('country','name','s'))->setAlias("country_name")),
                (new Column(self::TABLE_NAME,'avatar_media_id','i'))->join('id',(new Column('media'))->addSelectColumnAlias('path','avatar_media_path'))
            ];
        }
        return static::$_column_cache;
    }

    public static function getAvatarDisplayNameColumns(string $displayNameLabel = "member_display_name", string $avatarMediaPathLabel="member_avatar") {
        return (new Column('user','avatar_media_id','i'))->addSelectColumnAlias('display_name',$displayNameLabel)->join('id',(new Column('media'))->addSelectColumnAlias('path',$avatarMediaPathLabel));
    }

    public function save(bool $updateIfFound = true)
    {
        parent::save($updateIfFound); // TODO: Change the autogenerated stub
        if ($this->id() > 0) {
            $chan = Channel::createChannelFromObject($this,$this->id());
        }
    }

    /**
     * @return string
     */
    public function getChannelName()
    {
        return Channel::createDefaultChannelName(self::TABLE_NAME,$this->id());
    }
}