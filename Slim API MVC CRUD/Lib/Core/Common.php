<?php


namespace Lib\Core;


class Common
{
    public static function copyObjectProperties(object $src,object $dest) {
        $properties = \get_class_vars(\get_class($dest));

        foreach($properties as $name => $value) {
            if (isset($src->$name)) {
                $dest->$name = $src->$name;
            }
        }
    }

    public static function copyArrayToObjectProperties(array $src,object $dest) {
        $properties = \get_class_vars(\get_class($dest));
        foreach($properties as $name => $value) {
            if (isset($src[$name])) {
                $dest->$name = $src[$name];
            }
        }
    }
}