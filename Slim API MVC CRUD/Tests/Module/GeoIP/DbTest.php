<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 20/3/2019
 * Time: 1:50 PM
 */

namespace GeoIP;

use PHPUnit\Framework\TestCase;

class DbTest extends TestCase
{

    public function testResolveIPv6Address()
    {
        $ipv6 = \Module\GeoIP\Db::I()->resolveIPAddress("2001:e68:540a:99e2:2934:8c32:4c7:e3e");

        $this->assertSame($ipv6->getCountry(),"Malaysia");
        $this->assertSame($ipv6->getCountryIso(),"MY");
        $this->assertIsString($ipv6->getCity());
    }
    public function testResolveIPv4Address()
    {
        $ipv4 = \Module\GeoIP\Db::I()->resolveIPAddress("8.8.8.8");

        $this->assertSame($ipv4->getCountry(),"United States");
        $this->assertSame($ipv4->getCountryIso(),"US");
        $this->assertIsString($ipv4->getCity());
    }
    public function testInvalidAddress()
    {
        $ip = \Module\GeoIP\Db::I()->resolveIPAddress("8.8.888.8");

        $this->assertNull($ip,"Invalid IP");
    }
}
