<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 22/9/2018
 * Time: 7:31 PM
 */

namespace Lib\Core\Helper\Http;


class Request
{

    /**
     * Check if all properties of class exists and loads all properties of class from source
     * @param array $source POST/GET parameters
     * @return bool
     */
    public function isValid(array $source) {
        $var = \array_keys(\get_class_vars(\get_class($this)));
        //error_log(print_r($var,TRUE),0);
        if (\count(\array_intersect_key(\array_flip($var), $source)) === \count($var)) {
            foreach ($var as $i) {
                $this->$i = $source[$i];
            }
            return true;
        }
        return false;
    }

    public function setPropertiesIfExists(object $obj) {

        foreach ($this as $prop => $val)
        {
            if (!\is_null($val)) {
                try
                {
                    $obj->$prop = $val;
                }catch(\Exception $e){}
            }
            //\error_log($this->$prop,0);
            //if (isset($this->$prop))
            //$obj->$prop = $this->$prop;
        }
    }
}