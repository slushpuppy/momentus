<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 8/8/2018
 * Time: 11:45 PM
 */

namespace Module\Exception\User;

use Exception;


class Account extends \Module\Exception\_Exception
{
    const USER_EXISTS=1;
}