<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/7/2018
 * Time: 6:24 PM
 */

namespace Lib\Core\Helper\Http;


class Scraper {
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
     * @param string $url URL of page to scrape
     * @return string response body of page
     */
    public function fetch(string $url) {
        return \file_get_contents($url);
    }
}