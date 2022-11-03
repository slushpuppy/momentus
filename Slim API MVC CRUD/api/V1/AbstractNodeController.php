<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 30/7/2018
 * Time: 11:29 PM
 */

namespace api\V1;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractNodeController
{
    public abstract function get(ServerRequestInterface $request, ResponseInterface $response, array $args);
    public abstract function post(ServerRequestInterface $request, ResponseInterface $response, array $args);
    public abstract function put(ServerRequestInterface $request, ResponseInterface $response, array $args);
    public abstract function patch(ServerRequestInterface $request, ResponseInterface $response, array $args);
    public abstract function delete(ServerRequestInterface $request, ResponseInterface $response, array $args);
}