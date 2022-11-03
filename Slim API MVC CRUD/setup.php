<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/11/2018
 * Time: 8:59 PM
 */

if (php_sapi_name() !== 'cli') die();

require_once(__DIR__.'/autoload.php');
$allfiles = [];

$directory = __DIR__."/Module/";

//get all files in specified directory
$files = glob($directory . "*");

$allfiles = array_merge($allfiles,$files);

$directory = __DIR__."/Lib/";

//get all files in specified directory
$files = glob($directory . "*");

$allfiles = array_merge($allfiles,$files);
//print each file name
foreach($allfiles as $file)
{
    //check to see if the file is a folder/directory
    if(is_dir($file))
    {
        $module = str_replace([__DIR__,'/'],["",'\\'],$file);
        $class = $module.'\Setup\Install';

        if (class_exists($class)) {
            echo "Installing: ".$class."\r\n";
            $obj = new $class();
            $obj->add();
        }

    }
}


