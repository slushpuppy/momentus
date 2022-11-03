<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 29/7/2018
 * Time: 5:22 PM
 */

namespace Lib\Core\Helper\Db;


class mysqli extends \mysqli
{
    const SOURCE_MODULE = 'Lib\Core\Helper\Db\mysqli';
    public function prepare($query) {

        $stmt = new mysqli_stmt($this, $query);
        if ($this->error != "") {
           // if ( \defined("DEVELOPMENT_MODE")) echo $query;
            \Module\Exception\Log::i()->add(static::SOURCE_MODULE,$query,$this->error)->error();
           // var_dump($query);
            throw new \Module\Exception\MySQL($this);

        }
        return $stmt;
    }
}