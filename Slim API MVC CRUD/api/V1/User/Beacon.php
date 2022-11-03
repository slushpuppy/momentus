<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 26/2/2019
 * Time: 1:02 AM
 */

namespace api\V1\User;
use Lib\Core\Helper\Error;
use Lib\Core\Helper\Http\Rest\Response;
use Module\Hardware\RustBeacon;
use Module\OAuth\Scope;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Beacon extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected static $permissionScope = Scope::PROFILE;

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


    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $user_id = $this->getActiveUserId();
        $res = new Response();
        $request = $request->getParsedBody();
        if (isset($_GET['add_beacon'])) {
            if (isset($request->passkey, $request->mac_address,$request->hardware_version,$request->passkey)) {
                $rustBeacon = RustBeacon::loadUid($request->mac_address,$request->hardware_version);
                $beacon = new \Module\User\Beacon();
                $beacon->user_id = $user_id;
                $beacon->date = time();
                $beacon->passkey = $request->passkey;
                $beacon->rust_beacon_id = $rustBeacon->id;
                $res->status = Response::STATUS_OK;
                return $response->withJson($res);
            }
        }



    }

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $user_id = $this->getActiveUserId();

        $res = new Response();

        if ($_GET['all']) {
            $beacons = \Module\User\Beacon::loadAllWithUserId($user_id);
            $res->data = $beacons;
            $res->status = Response::STATUS_OK;
        }
        return $res;
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