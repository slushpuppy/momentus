<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 20/3/2019
 * Time: 12:22 PM
 */

namespace Module\GeoIP;

require_once __DIR__.'/../../autoload.php';

use MaxMind\Db\Reader;

class Db
{
    const mmdbPath = __DIR__.'/dat/GeoLite2-City.mmdb';
    private static $_i;

    /**
     * @var Reader reader object
     */
    private $reader;
    private function __construct()
    {

    }

    public static function I() {
        if (self::$_i == NULL) {
            self::$_i = new Db();
            self::$_i->reader = new Reader(self::mmdbPath);
        }
        return self::$_i;
    }

    /**
     * @param string $ipAddress IPv4 or IPv6
     * @return IP
     */

    public function resolveIPAddress($ipAddress) {

        try {
            $location = $this->reader->get($ipAddress);
            $return = new IP();
            if (isset($location["country"]["names"]["en"],$location["country"]["iso_code"])) {

                $return->setCountry($location["country"]["names"]["en"]);
                $return->setCountryIso($location["country"]["iso_code"]);
            } else {
                return null;
            }
            if (isset($location["city"]["names"]["en"])) {
                $return->setCity($location["city"]["names"]["en"]);
            }

        } catch (\Exception $e) {

            return null;
        }
        return $return;
    }
    function __destruct()
    {
        $this->reader->close();
    }
}
class IP {
    private $country = "",$country_iso = "",$city = "";

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountryIso()
    {
        return $this->country_iso;
    }

    /**
     * @param string $country_iso
     */
    public function setCountryIso(string $country_iso)
    {
        $this->country_iso = $country_iso;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }
}