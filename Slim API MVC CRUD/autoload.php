<?php
require_once (__DIR__.'/vendor/autoload.php');


\mb_internal_encoding("UTF-8");
\spl_autoload_register(function($class_name) {

    if (($class_name === 'Config\\Site' ||
        $class_name === 'Config\\Db' ||
           $class_name === 'Config\\Log') &&
        \defined("DEVELOPMENT_MODE"))
    {
        $class_name = 'Config-dev\\'.substr($class_name,7);
    }
    if (\strpos($class_name,'Api\\') !== FALSE) {
        $class_name = 'api\\'.substr($class_name,4);
    }

    $sources = '';
    $file =  \str_replace(['\\','/'],DIRECTORY_SEPARATOR,__DIR__.$sources.'/'.$class_name. '.php');

    if (\file_exists($file)) //throw new Exception('File '.$file.' not Found');
        require $file;
});
