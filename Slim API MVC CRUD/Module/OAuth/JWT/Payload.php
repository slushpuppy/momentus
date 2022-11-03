<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 2/11/2018
 * Time: 1:09 AM
 */

namespace Module\OAuth\JWT;


use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class Payload
{
    const SOURCE_MODULE='Module\OAuth\JWT\Payload';
    public $user_id,$ip_address,$jti,$exp;

    /**
     * Payload constructor.
     * @param int $user_id
     * @param string $ip_address
     * @param mixed $jti
     * @param int $expiry
     */
    public function __construct($user_id, $ip_address, $jti, $expiry)
    {
        $this->user_id = $user_id;
        $this->ip_address = $ip_address;
        $this->jti = $jti;
        $this->exp = $expiry;
    }

    /**
     * @param string $jwt
     * @return Payload|null
     */
    public static function loadPayloadWithJWT(string $jwt)
    {
        try {
            $decoded = JWT::decode($jwt, \Config\OAuth\General::JWT_SECRET_KEY, array('HS256'));
            if ($decoded) {
                $payload = new Payload($decoded->user_id,$decoded->ip_address, $decoded->jti, $decoded->exp);
                return $payload;
            }

        }catch (ExpiredException $e) {
            \Module\Exception\Log::i()->add(static::SOURCE_MODULE,'',$e->getMessage())->error();
        }catch (\Throwable $t)
        {
            \Module\Exception\Log::i()->add(static::SOURCE_MODULE,'',$t->getMessage())->error();
        }
        return null;
    }
}