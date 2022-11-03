<?php
require_once (__DIR__.'/../autoloader.php');

use \Lib\Core\Helper\Http\Json;
use \Lib\Core\Helper\Error;
use \Firebase\JWT\JWT;

define('SOURCE_MODULE','handler/oauth/internal/jwt' );


$output = new stdClass();