<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 30/7/2018
 * Time: 3:55 PM
 */

namespace api;

use Api\V1\Model\Response;
use Module\Exception\OAuth\NotInScopeException;
use Slim\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class V1Controller
{
    private $container;

    public function __construct(Collection $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        /**
         * Check if path exists
         */
        if (!isset($args['params']))
            return $response->withStatus(404);


        $path = $this->capitalizePath(explode('/',trim($args['params'],'/')));

        $newarg = [];
        for($i = sizeof($path) - 1;$i >= 0; $i--) {
            if (\intval($path[$i]) > 0) {
                \array_unshift($newarg,$path[$i]);
                unset($path[$i]);
            }
        }


        /**
         * If path is invalid
         */
        if (count($path) <= 0)
            return $response->withStatus(404);


        $class_name = '\Api\V1\\'.implode('\\',$path);
        $method = \mb_strtolower($request->getMethod());
        if (\class_exists($class_name)) {
            $return = new Response();
            if ($class_name::i()->getScope() != NULL) {
                try {
                    $user = \Module\User\Account::verifyJWT($class_name::i()->getScope());
                } catch(NotInScopeException $e) {
                    $return->status = Response::STATUS_ERROR;
                    $return->setMsg("No Permission Scope");
                    $return->setCode("SCOPE_PERMISSION");
                    return $response->withJson($return);
                }

                if ($user == NULL) {

                    $return->status = Response::STATUS_ERROR;
                    $return->setMsg("Invalid User Authentication");
                    return $response->withJson($return);
                }
                $class_name::i()->setJWTData($user);
            }

            return $class_name::i()->$method($request, $response, $newarg);
        } else {
            return $response->withStatus(404);
        }

    }
    public function capitalizePath(array $array) {
        return \array_map(function($n) {

            if (\strpos($n,'_') !== FALSE) {
                return \ucfirst(\preg_replace_callback(
                    '/(\_([a-z]))/',
                    function($w) {
                        return \ucfirst($w[2]);
                    },$n

                ));
            }
            else {
                return \ucfirst($n);
            }
        },$array);
    }
}