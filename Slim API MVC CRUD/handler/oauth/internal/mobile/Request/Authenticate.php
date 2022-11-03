<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 22/9/2018
 * Time: 8:04 PM
 */

namespace handler\oauth\internal\mobile\Request;


class Authenticate extends \Lib\Core\Helper\Http\Request
{
    public $access_token,$grant_type,$scope,$state;
}