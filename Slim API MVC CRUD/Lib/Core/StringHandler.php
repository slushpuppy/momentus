<?php


namespace Lib\Core;


class StringHandler
{
    public static function unicodeTrim(string $str) {
        return preg_replace(
            '/
    ^
    [\pZ\p{Cc}\x{feff}]+
    |
    [\pZ\p{Cc}\x{feff}]+$
   /ux',
            '',
            $str
        );
    }
}