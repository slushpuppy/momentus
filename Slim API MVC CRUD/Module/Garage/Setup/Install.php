<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/11/2018
 * Time: 8:57 PM
 */

namespace Module\Garage\Setup;

use Config\File;
use Lib\Core\Helper\Db\Conn;
use Lib\Core\InstallInterface;
use Lib\Core\StringHandler;
use Module\Garage\Service\Service;
use Module\Garage\Service\ServiceDocument;
use Module\Garage\Service\Type;

class Install implements InstallInterface {


    function add()
    {
        $conn = \Lib\Core\Helper\Db\Conn::i()->get();
        $parts = \file_get_contents(__DIR__.'/Motorcycle Parts List.csv');
        $parts = explode("\r\n",$parts);

        $family_con = $conn->prepare('insert into motorcycle_part_family(name) VALUES (?) on duplicate key update name=name');
        $comp_con = $conn->prepare('insert into motorcycle_part(family_id,name) VALUES((select id from motorcycle_part_family where motorcycle_part_family.name=?),?) on duplicate key update name=VALUES(name),family_id=VALUES(family_id)');
        foreach($parts as $part) {
            $arr = \array_filter(explode(",",trim($part)));
            if (count($arr) == 2)
            {
                $family = StringHandler::unicodeTrim($arr[0]);
                $component = StringHandler::unicodeTrim($arr[1]);
                $family_con->bind_param('s',$family);
                $family_con->execute();

                $comp_con->bind_param('ss',$family,$component);
                $comp_con->execute();
            }

        }
        $con = $conn->prepare("INSERT IGNORE INTO sys_string_index(`name`) VALUES(?)");
        $arr = [
            Type::ACCIDENT,
            Type::BREAKDOWN,
            Type::FULL,
            Type::MOD,
            TYpe::PREVENTIVE,
            Type::VALVE,
            Type::GENERAL,
            Type::TUNEUP,
            Type::CHECKUP,
            ServiceDocument::TYPE_ODOMETRY,
            ServiceDocument::TYPE_RECEIPT,
            ServiceDocument::TYPE_SERVICE_PHOTO
        ];
        foreach ($arr as $a) {
            $con->bind_param('s',$a);
            $con->execute();
        }
    }

    function remove()
    {
        // TODO: Implement remove() method.
    }
}