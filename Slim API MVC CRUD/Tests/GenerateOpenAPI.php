<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 23/3/2019
 * Time: 8:02 PM
 */

require(__DIR__."/../autoload.php");
$openapi = \OpenApi\scan([__DIR__.'/../api',
    __DIR__.'/../Module/User/JWT.php'
]);
header('Content-Type: application/x-yaml');
file_put_contents("rustapi.json",$openapi->toJson());
