<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 21/8/2018
 * Time: 10:57 PM
 */


namespace Lib\Core\Helper\Http;


class Json {
    private
    static $_i;

    private function __construct()
    {

    }

    public
    static function i()
    {
        if (self::$_i == NULL) {
            self::$_i = new self;
        }
        return self::$_i;
    }

    /**
     * @param $field
     * @param $value
     */
    public function addHeader($field, $value) {
        header($field . ": ".$value);
    }



    /**
     * @param object|array|\stdClass $output
     * @return void
     */
    public function send($output) {
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($output);
        exit;
    }

}