<?php
require_once (__DIR__.'/vendor/autoload.php');


\mb_internal_encoding("UTF-8");
\spl_autoload_register(function($class_name) {
    $sources = '';
    $file =  \str_replace(['\\','/'],DIRECTORY_SEPARATOR,__DIR__.$sources.'/'.$class_name. '.php');


    if (\file_exists($file)) //throw new Exception('File '.$file.' not Found');
        require $file;
});
