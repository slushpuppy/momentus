<?php
require_once (__DIR__.'/../vendor/autoload.php');


\mb_internal_encoding("UTF-8");
\spl_autoload_register(function($class_name) {

    if ((\strpos($class_name,'Config\\Site') !== FALSE ||
            \strpos($class_name,'Config\\Db') !== FALSE ||
            \strpos($class_name,'Config\\Log') !== FALSE) &&
        \defined("DEVELOPMENT_MODE"))
    {
        $class_name = 'Config-dev\\'.substr($class_name,7);
    }
    if (\strpos($class_name,'Api\\') !== FALSE) {
        $class_name = 'api\\'.substr($class_name,4);
    }
    if (strpos($class_name,'Tests') !== FALSE) echo $class_name;
    $sources = '/../';
    $file =  \str_replace(['\\','/'],DIRECTORY_SEPARATOR,__DIR__.$sources.'/'.$class_name. '.php');
    if (\file_exists($file)) //throw new Exception('File '.$file.' not Found');
        require $file;
});
Tests\Init::tearDown();
Tests\Init::setUp();
/*
register_shutdown_function(function () {
    /Tests\Init::tearDown();
});*/