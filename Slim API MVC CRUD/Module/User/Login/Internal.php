<?php


namespace Module\User\Login;


use Api\V1\Oauth\Authenticate;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Lib\Core\Http;
use Module\OAuth\JWT\Payload;
use UnexpectedValueException;

class Internal
{
    /**
     * @param string $email
     * @return string
     */
    public static function createJWT(string $email) {
        $time = time()+ \Config\Login\Internal::TOKEN_VALIDITY_SEC;
        $payload = new VerificationPayload($email,Http::getIPAddress(),$time);

        try{
            $jwt = JWT::encode($payload, \Config\Login\Internal::TOKEN_SECRET_KEY);
            return $jwt;
        }catch (UnexpectedValueException $e) {
            \Module\Exception\Log::i()->add(static::SOURCE_MODULE,'',$e->getMessage())->error();
            return '';
        }
    }

    /**
     * @param string $jwt
     * @return VerificationPayload|null
     */
    public static function verifyJWT(string $jwt) {
        try {
            $decoded = JWT::decode($jwt, \Config\Login\Internal::TOKEN_SECRET_KEY, array('HS256'));
            if ($decoded) {
                $payload = new VerificationPayload($decoded->email,$decoded->ip_address,time());
                return $payload;
            }

        }catch (ExpiredException $e) {
        }catch (\Throwable $t)
        {
            \Module\Exception\Log::i()->add(static::SOURCE_MODULE,'',$t->getMessage())->error();
        }catch (\Exception $t)
        {
            \Module\Exception\Log::i()->add(static::SOURCE_MODULE,'',$t->getMessage())->error();
        }

        return null;
    }
}
class VerificationPayload {
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $ip_address;
    /**
     * @var int
     */
    public $exp;

    /**
     * VerificationPayload constructor.
     * @param string $email
     * @param string $ip_address
     * @param int $time
     */
    public function __construct(string $email, string $ip_address, int $exp)
    {
        $this->email = $email;
        $this->ip_address = $ip_address;
        $this->exp = $exp;
    }
}