<?php
namespace Ext\Libs;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class BaseController
 *
 * @package Ext\Libs
 *
 * @author: vzangloo <vzangloo@7mayday.com>
 * @link: https://www.7mayday.com
 * @since 1.0.0
 * @copyright 2021 Web Discovery Solutions
 */
class BaseController
{
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    /**
     *
     */
    public function init()
    {

    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function index(Request $request, Response $response)
    {

    }


    public function withJson($response,$payload)
    {
        $json = \json_encode($payload);
        return $response->withHeader("Content-Type", "application/json")
            ->write($json);
    }
}