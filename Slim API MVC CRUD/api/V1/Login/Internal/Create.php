<?php


namespace api\V1\Login\Internal;


use api\V1\Login\Internal\Model\RequestEmail;
use api\V1\Login\Internal\Model\ResponseVerificationChannel;
use api\V1\Login\Internal\Model\VerificationChannel;
use Api\V1\Model\Response;
use Lib\Mail\Mail;
use Lib\Mail\Message;
use Module\OAuth\Scope;
use Module\User\Login\Internal;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Create extends \Api\V1\AbstractRestController
{
    private static $_i;

    protected static $permissionScope = null;
    protected const SOURCE_MODULE = "api\\V1\\Login\Internal\\Create";

    private function __construct()
    {
    }


    public static function i()
    {
        if (self::$_i == NULL)
        {
            self::$_i = new self;
        }
        return self::$_i;
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $res = new ResponseVerificationChannel();
        $req = new RequestEmail();
        if ($req->isValid($_GET))
        {
            $clean_email = filter_var($req->email,FILTER_SANITIZE_EMAIL);

            if ($req->email == $clean_email && filter_var($req->email,FILTER_VALIDATE_EMAIL)){
                $msg = new Message();

                $jwt = Internal::createJWT($clean_email);
                $msg->subject = "Your Account Confirmation Email";
                $msg->body = '<a href="https://rust.bike/handler/oauth/internal/connect.php?verify='.$jwt.'">Verify</a> your account';
                $msg->altBody = 'Copy the following link: https://rust.bike/handler/oauth/internal/connect.php?verify='.$jwt;

                Mail::I()->setTo($req->email)->send($msg);
                $verificationChannel = new VerificationChannel();
                $verificationChannel->channel = md5($jwt);
                $res->data = $verificationChannel;
                $res->status = Response::STATUS_OK;
            } else {
                $res->error = "Invalid email";
                $res->setCode("INVALID_EMAIL");
                $res->status = Response::STATUS_ERROR;

            }


        }
        return $response->withJson($res);

    }

    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

    }

    public function put(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement put() method.
    }


    public function patch(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO: Implement delete() method.
    }
}