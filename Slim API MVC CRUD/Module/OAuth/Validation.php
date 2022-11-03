<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 7/8/2018
 * Time: 8:21 PM
 */

namespace Module\OAuth;


class Validation
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

    /**
     * Verifies auth token to scope permission
     * @param string $token
     * @param array $scope
     * @return false|\Module\User\Account
     */
    public function verify(string $token, array $scopes) {
        $token = AccessTokenTest::loadWithToken($token);
        $db_scopes = AccessTokenScopeTest::loadByTokenId( $token->id());
        if ($db_scopes != null) {
            if (!\is_array($db_scopes)) $db_scopes = [$db_scopes];
            $res = \array_diff($scopes,$db_scopes);
            if (count($res) != count($scopes)) {
                return \Module\User\Account::loadWithId($token->user_id);
            }
        }
    }
}