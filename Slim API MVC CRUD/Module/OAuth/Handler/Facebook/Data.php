<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 20/8/2018
 * Time: 10:46 PM
 */

namespace Module\OAuth\Handler\Facebook;


use Lib\Core\Helper\Db\Conn;

class Data extends \Module\OAuth\Handler\Data implements \Module\OAuth\Handler\IData
{
    public $facebookId;
}