<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 30/7/2018
 * Time: 11:29 PM
 */

namespace api\V1;

use Lib\Core\Helper\Error;
use Lib\Core\Helper\Http\Json;
use Module\OAuth\JWT\Payload;
use Module\User\Account;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
/**
 * @OA\OpenAPI(
 *  @OA\Info(
 *     title="RUST API",
 *     version="0.1",
 *     description="RUST API",
 *     @OA\Contact(email="luke.lim@glass-security.com.sg")
 * ),
 *     @OA\Server(
 *     description="RUST API HOST",
 *     url="https://rust.bike/api/v1/"
 * ),
 *     @OA\ExternalDocumentation(
 *     description="More information",
 *     url="https://rust.bike/"
 * )
 * )
 * @OA\SecurityScheme(
 *     securityScheme="accountBearer",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="jwt",
 * )
 * @OA\Components(
 *     responses="Unauthorized"
 * )
 */
abstract class AbstractRestController
{

    protected static $permissionScope = NULL;
    protected const SOURCE_MODULE = 'AbstractRestController';
    public abstract function get(ServerRequestInterface $request, ResponseInterface $response, array $args);
    public abstract function post(ServerRequestInterface $request, ResponseInterface $response, array $args);
    public abstract function put(ServerRequestInterface $request, ResponseInterface $response, array $args);
    public abstract function patch(ServerRequestInterface $request, ResponseInterface $response, array $args);
    public abstract function delete(ServerRequestInterface $request, ResponseInterface $response, array $args);


    /**
     * @return string
     */
    public function getScope() {
        return static::$permissionScope;
    }

    protected $jwtData = NULL;

    public function setJWTData(Payload $user) {
        $this->jwtData = $user;
    }

    /**
     * @return Payload
     */
    public function getJWTData() {
        return $this->jwtData;
    }


    /**
     * @return int|null
     */
    public function getActiveUserId() {
        if ($this->getJWTData() !== NULL)
        {
            return $this->getJWTData()->user_id;
        }

        return null;
    }
    function exitWithError($code = '',$msg = 'unknown') {
        $error = new Error();
        \Module\Exception\Log::i()->add(static::SOURCE_MODULE,$code,$msg)->error();
        Json::i()->send($error->setCode('')->setMsg($msg));
    }

    /**
     * Check if ID is valid - value is int and more than 0
     * @param string $value
     * @return bool|int
     */
    function isValidArgID(string $value) {
        if (isset($value) && ($ret = \intval($value)) > 0)
        {
            return $ret;
        }
        else {
            return false;
        }
    }
}