<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 20/3/2019
 * Time: 5:44 PM
 */

namespace Tests;



use GuzzleHttp\Client;
use Lib\Core\Helper\Db\Conn;
use Module\FileSystem\Type\Image;
use Module\OAuth\Scope;
use Module\Social\Group\Avatar;
use Module\Social\Group\Group;
use Module\User\Account;
use stdClass;

/**
 * Class Init
 * @package Tests
 */
class Init
{
    /**
     * @var Client
     */
    public static $httpClient;

    public static $test2mbimage = __DIR__.'/resource/2mbimage.jpg';

    public static $test100kimage = __DIR__.'/resource/100kbimage.jpg';

    /**
     * @var array
     */
    public static $testUser1AuthHeader;

    /**
     * @var array
     */
    public static $testUser2AuthHeader;

    public static $authClass;
    /**
     * @var Account
     */
    public static $testUser1;

    /**
     * @var string
     */
    public static $testUser1JWT;

    /**
     * @var Account
     */
    public static $testUser2;
    /**
     * @var Account
     */
    public static $testUser3;

    /**
     * @var Group
     */
    public static $testGroup1;

    /**
     * @var Image
     */
    public static $testImage;
    /**
     * @var string
     */
    private static $testUser2JWT;

    /**
     * @throws \Module\Exception\General
     */
    public static function setUp()
    {
        self::$httpClient = new Client(['base_uri' => 'http://127.0.0.1/rust/api/v1/']);
        //$img = Image::createFromBlob("\x89PNG\x0d\x0adfsfdfd");



        self::$testUser1 = \Module\User\Account::create( "1233@shark.com",'1puppy',"luke","lim",'+2732342243',"Singapore");

        self::$testUser2 = \Module\User\Account::create( "new@user.com",'2puppy',"luke","lim",'+2732777342243',"Singapore");


        self::$testUser3 = \Module\User\Account::create( "122233@shark.com",'3puppy',"luke","lim",'+2732666342243',"Singapore");
        $user_id = self::$testUser1->id();
        $token = \Module\OAuth\AccessToken::create($user_id);

        $profile_token = \Module\OAuth\AccessTokenScope::create($token->id(),\Module\OAuth\Scope::PROFILE);
        $social_token = \Module\OAuth\AccessTokenScope::create($token->id(),\Module\OAuth\Scope::SOCIAL_MEDIA);
        $garage_token = \Module\OAuth\AccessTokenScope::create($token->id(),\Module\OAuth\Scope::GARAGE);
        try
        {
        $result = Init::$httpClient->request("get","oauth/jwt/renew?refresh_token=".$token->refresh_token);

        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($result->getBody());
        self::$testUser1AuthHeader = [
            'Authorization' => 'Bearer ' . $json->data->token,
            'Accept'        => 'application/json',
        ];

        self::$testUser2JWT = $json->data->jwt;


        $user_id = self::$testUser2->id();
        $token = \Module\OAuth\AccessToken::create($user_id);

        $profile_token = \Module\OAuth\AccessTokenScope::create($token->id(),\Module\OAuth\Scope::PROFILE);
        $social_token = \Module\OAuth\AccessTokenScope::create($token->id(),\Module\OAuth\Scope::SOCIAL_MEDIA);
        $garage_token = \Module\OAuth\AccessTokenScope::create($token->id(),\Module\OAuth\Scope::GARAGE);
        try
        {
            $result = Init::$httpClient->request("get","oauth/jwt/renew?refresh_token=".$token->refresh_token);
        }catch(\GuzzleHttp\Exception\ServerException $e) {
            echo $e->getResponse()->getBody()->getContents();
        }
        $json = \json_decode($result->getBody());

        self::$testUser2AuthHeader = [
            'Authorization' => 'Bearer ' . $json->data->token,
            'Accept'        => 'application/json',
        ];

        self::$testUser2JWT = $json->data->jwt;


        self::$testImage = Avatar::createFromBlob("\x89PNG\x0d\x0adfsfdfd");
        self::$testGroup1 = Group::createWithUserIdAndImage(Init::$testUser1,self::$testImage,"Test Chat Group");
    }


    public static function tearDown()
    {
        $mysql = Conn::i()->get();

        $res = $mysql->query("show tables");

        $tables = [];
        while ($row = $res->fetch_array()) {
            $tables[] = $row["Tables_in_rust.bike"];
        }
        $res->close();
        $ignored_tables = [
            "country",
            "motorcycle_model",
            "motorcycle_family",
            "motorcycle_part",
            "motorcycle_part_family",
            "motorbike_manufacturer",
            "user_profile_field_type",
            "auth_method",
            "auth_oauth_scope",
            "social_group_role_permission",
            "route_marker_type",
            "sys_string_index"
        ];
        $query = "SET FOREIGN_KEY_CHECKS = 0;";
        foreach($tables as $table) {

            if (\in_array($table,$ignored_tables)) continue;
            $query .= "truncate table `".$table."`;";
        }
        $mysql->multi_query($query."SET FOREIGN_KEY_CHECKS = 1;");

            while ($mysql->more_results()) {$mysql->next_result();}

        $files = glob(__DIR__.'/../upload/*'); // get all file names

        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }
    }
    public static function generateRandomString($length = 10) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
}