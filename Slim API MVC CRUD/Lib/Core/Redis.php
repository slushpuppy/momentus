<?php


namespace Lib\Core;


class Redis
{
    private static $_i;
    private $client;

    private function __construct() {
        $this->client = new \Redis();
        $this->client->connect(\Config\Redis::HOST,\Config\Redis::PORT);
    }

    public static function i() {
        if (self::$_i == NULL) {
            self::$_i = new self;

            return self::$_i;
        } else return self::$_i;
    }

    /**
     * @param string $key
     * @param string $value
     * @throws \RedisException
     */
    public function set(string $key,string $value) {
        if (!$this->client->set($key, $value)) {
            throw new \RedisException($this->client->getLastError());
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @param int $ttl
     * @throws \RedisException
     */
    public function setEx(string $key,string $value,int $ttl) {
        if (!$this->client->setex($key, $value,$ttl)) {
            throw new \RedisException($this->client->getLastError());
        }
    }

    public function setExEncrypted(string $key,string $value,int $ttl) {

        if (!$this->client->setex($key, $this->my_encrypt($value),$ttl)) {
            throw new \RedisException($this->client->getLastError());
        }
    }

    /**
     * @param string $key
     * @return bool|string
     */
    public function get(string $key) {
        return $this->client->get($key);
    }

    /**
     * @param string $key
     * @return string
     */
    public function getEncrypted(string $key) {
        return $this->my_decrypt($this->client->get($key));
    }

    /**
     * @param $data
     * @param $key
     * @return string
     */
    private function my_encrypt($data) {
        // Remove the base64 encoding from our key
        $encryption_key = \Config\Redis::SECRET_ENCRYPT_KEY;
        // Generate an initialization vector
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
        // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * @param $data
     * @param $key
     * @return string
     */
    private function my_decrypt($data) {
        $encryption_key = \Config\Redis::SECRET_ENCRYPT_KEY;
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }
}