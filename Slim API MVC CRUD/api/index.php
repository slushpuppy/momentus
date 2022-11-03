<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 30/7/2018
 * Time: 12:36 PM
 */

namespace api;

require_once __DIR__.'/../autoload.php';
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

if (\defined("DEVELOPMENT_MODE"))
{
    $configuration = [
        'settings' => [
            'displayErrorDetails' => true,
        ],
    ];
} else {
    $configuration = [];
}
/*$configuration['settings']['RequireBearer'] = [
    'Feed',
    'User',
];*/
$configuration['V1Controller'] = function ($container) {
    return new \Api\V1Controller($container->get('settings'));
};

$c = new \Slim\Container($configuration);

$app = new \Slim\App($c);
$app->any('/v1[/{params:.*}]', 'V1Controller');
$app->run();