<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 30/7/2018
 * Time: 6:29 PM
 */

namespace api\V1\Search;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Thread extends \Api\V1\AbstractRestController
{
    private static $_i;

    private function __construct()
    {
    }

    public static function i()
    {
        if (self::$_i == NULL) {
            self::$_i = new self;
        }
        return self::$_i;
    }
    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $db = \Lib\Core\Helper\Db\Conn::i()->get();
        $user_id = $this->getActiveUserId();
        $start_date = $_GET['start'];

      //  \Module\Social\Feed\Activity::loadWithUserId($user_id)

    }

    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement post() method.
    }

    public function put(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement put() method.
    }

    public function patch(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement patch() method.
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement delete() method.
    }
}