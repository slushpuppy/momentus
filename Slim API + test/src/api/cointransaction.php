<?php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

require __DIR__.'/../autoload.php';

$app = new \Slim\App([ 'debug' => true]);

//var_dump($app->getContainer()->get('request')->getUri());

$app->get('/cointransaction/balance', \Controller\CoinTransaction\CoinTransaction::class. ":getBalance");
$app->post('/cointransaction/credit/{type:[a-zA-Z\-]+}', \Controller\CoinTransaction\CoinTransaction::class. ":credit");
$app->post('/cointransaction/deposit/{type:[a-zA-Z\-]+}', \Controller\CoinTransaction\CoinTransaction::class. ":deposit");
$app->run();

