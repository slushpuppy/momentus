<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 7/8/2018
 * Time: 8:26 PM
 */

namespace Lib\Core\Controller;

use Lib\Core\Helper\Db\Conn;
use SQLBuilder\Universal\Query\SelectQuery;
use SQLBuilder\Driver\MySQLDriver;
use SQLBuilder\ArgumentArray;
use \Module\Exception\Controller as ControllerException;

abstract class AbstractNodeController extends AbstractController
{

    /**
     * @return AbstractController
     */
    abstract function getChild();

    public function serializeAll() {
        $return = [];
        $childNode = $return;
        $class = $this;
        do {
            $properties = \array_keys($class->getColumns());
            foreach ($properties as $prop) {
                $childNode[$prop] = $this->$prop;
            }


            $childNode = &$childNode["childNode"];
        }while ($class->getChild() != null);

     return $return;

    }
}