<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/11/2018
 * Time: 8:57 PM
 */

namespace Module\Social\Setup;

use Lib\Core\Helper\Db\Conn;
use Lib\Core\InstallInterface;

class Install implements InstallInterface {


    function add()
    {
        $mysqli = Conn::i()->get();
        $mysqli->query("INSERT IGNORE INTO sys_string_index(`name`) VALUES('\\\Module\\\Social\\\Feed\\\Position')
        ");
    }

    function remove()
    {
        // TODO: Implement remove() method.
    }
}