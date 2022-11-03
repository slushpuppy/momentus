<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/11/2018
 * Time: 8:57 PM
 */

namespace Module\FileSystem\Setup;

use Config\File;
use Lib\Core\Helper\Db\Conn;
use Lib\Core\InstallInterface;

class Install implements InstallInterface {


    function add()
    {

        if (!\file_exists(File::SAVE_PATH.'/image'))
            \mkdir(File::SAVE_PATH.'/image',0750,true);
        if (!\file_exists(File::SAVE_PATH.'/video'))
            \mkdir(File::SAVE_PATH.'/video',0750,true);

    }

    function remove()
    {
        // TODO: Implement remove() method.
    }
}