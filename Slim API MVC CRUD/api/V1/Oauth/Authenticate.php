<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 22/9/2018
 * Time: 8:04 PM
 */

namespace api\V1\Oauth;

class Authenticate extends \Lib\Core\Helper\Http\Request
{
    public $access_token,$grant_type,$scope,$state;
}