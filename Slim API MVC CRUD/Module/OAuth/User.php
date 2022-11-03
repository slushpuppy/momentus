<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 7/8/2018
 * Time: 8:23 PM
 */

namespace Module\OAuth;


use Lib\Core\Helper\Db\Conn;


class User extends \Lib\Core\Controller\AbstractController
{
    protected const TABLE_NAME = 'user';
    protected const TABLE_COLUMN_TYPE_MAP = array(
        'first_name' => 's',
        'last_name' => 's',
        'display_name' => 's',
        'email' => 's',
        'phone_number' => 's'
    );
    protected const ID_COLUMN = 'id';
    /**
     * @param $email
     * @param $display_name
     * @param $first_name
     * @param $last_name
     * @param $auth_type
     * @return User
     * @throws \Module\Exception\General
     */
    public static function create($email, $display_name, $first_name, $last_name,$phone_number) {
        $obj = new self;
        $obj->first_name = $first_name;
        $obj->last_name = $last_name;
        $obj->email = $email;
        $obj->display_name = $display_name;
        $obj->phone_number = $phone_number;
        try {
            $obj->save(false);
        }  catch (\Exception $e) {
            throw new \Module\Exception\OAuth\User(
                \Module\Exception\OAuth\User::USER_EXISTS
            );
        }

        return $obj;
    }

    public static function load(string $email,string $phone_number)
    {
        return self::_load('email=? OR phone_number=?','ss',[$email,$phone_number]);

    }

    /**
     * @param $email
     */
    public function authenticate($email) {

    }

    /**
     *
     */
    public function renewToken() {

    }

}