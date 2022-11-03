<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 8/8/2018
 * Time: 11:45 PM
 */

namespace Module\Exception\OAuth;

use Exception;


class User extends \Module\Exception\_Exception
{
    const USER_EXISTS=1;
}