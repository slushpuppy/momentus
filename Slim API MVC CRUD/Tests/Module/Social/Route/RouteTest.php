<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 4/4/2019
 * Time: 12:31 PM
 */

namespace Module\Social\Route;

use PHPUnit\Framework\TestCase;
use Tests\Init;

class RouteTest extends TestCase
{

    public function testCreateFromMember()
    {
        $route1 = Route::createFromMember(Init::$testUser1,"My Test Route",300);
        $route = Route::loadWithId($route1->id());
        //var_dump($route);var_dump($route1);
        $this->assertEquals($route->duration_mins,300);
        $this->assertEquals($route->author_display_name,Init::$testUser1->display_name);
        $this->assertEquals($route->author_avatar,Init::$testUser1->avatar_media_path);
        $this->assertEquals($route->title, "My Test Route");
        $this->assertEquals($route->author_user_id,Init::$testUser1->id());
    }


    /**
     * @depends testCreateFromMember
     */
    public function testLoadAllWithUserIdDesc()
    {
        for ($i =0; $i < 5;$i++)
        {
            $route1 = Route::createFromMember(Init::$testUser1,Init::generateRandomString(10),300);
            sleep(1);
        }
        $routes = Route::loadAllWithUserIdDesc(Init::$testUser1->id());
        $this->assertLessThan($routes[0]->id(),$routes[1]->id());
        foreach ($routes as $route) {
            $this->assertEquals(Init::$testUser1->id(),$route->author_user_id);
            $this->assertEquals(Init::$testUser1->avatar_media_path,$route->author_avatar);
            $this->assertEquals($route->duration_mins,300);
        }

    }

    public function testGetColumns()
    {
        $this->expectNotToPerformAssertions();
    }
}
