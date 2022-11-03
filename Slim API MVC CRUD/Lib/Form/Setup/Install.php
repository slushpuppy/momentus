<?php


namespace Lib\Form\Setup;


use Lib\Core\Helper\Db\Conn;
use Lib\Core\InstallInterface;
use \Lib\Form\Value\Integer;
use \Lib\Form\Value\Json;
use \Lib\Form\Value\Number;
use \Lib\Form\Value\StringValue;

class Install implements InstallInterface {


    function add()
    {
        $mysqli = Conn::i()->get();
        $stmt = $mysqli->prepare("INSERT IGNORE INTO sys_string_index(`name`) 
VALUES(?)
        ");

        $classes = [
            Integer::class,
            Json::class,
            Number::class,
            StringValue::class
        ];
        foreach($classes as $class) {
            $stmt->bind_param('s',$class);
            $stmt->execute();
        }
    }

    function remove()
    {
        // TODO: Implement remove() method.
    }
}