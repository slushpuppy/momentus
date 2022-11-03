<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 22/11/2018
 * Time: 9:40 PM
 */

namespace Module\Exception;


use Module\Exception\_Exception;

class Social extends _Exception
{
    const POST_LENGTH_TOO_LONG="Post length is too long";
    const MEMBER_IS_NOT_IN_GROUP="Member is not in group";
}