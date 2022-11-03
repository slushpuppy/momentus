<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 31/7/2018
 * Time: 11:06 PM
 */

namespace Module\Exception;


/**
 * Class Log
 * @package Module\Exception
 */
class Log
{
    private static $_i;
    private $line;
    private function __construct()
    {
    }

    public static function i()
    {
        if (self::$_i == NULL) {
            self::$_i = new self;
        }
        return self::$_i;
    }

    /**
     * @param string $source - Class/Module name of caller
     * @param string $code - Logging code
     * @param string $message - Log message
     * @return $this
     */
    public function add(string $source, string $code, string $message) {
        $date = new \DateTime();
        $result = $date->format('Y-m-d H:i:s');
        $this->line = sprintf("[%s] %s %s - %s\r\n",$result,$source,$code,$message);
        return $this;
    }

    /**
     * Log Exception
     * @param \Exception $e
     */
    public function addException(\Exception $e) {
        $this->add(sprintf("%s [%d]",$e->getFile(),$e->getLine()),$e->getTraceAsString(),$e->getMessage());
        return $this;
    }
    /**
     * @uses Writes to general log file
     */
    public function general() {
        $this->write(\Config\Log::GENERAL_FILE);
    }

    /**
     * @uses Writes to Warninf log file
     */
    public function warning() {
        $this->write(\Config\Log::WARNING_FILE);
    }
    /**
     * @uses Writes to Error log file
     */
    public function error() {
        $this->write(\Config\Log::ERROR_FILE);
    }

    /**
     * @param $filename - Accepts consts from \Config\Log
     */
    private function write($filename) {
        \file_put_contents(\Config\Log::PATH . $filename,
            $this->line."\r\n"
            , FILE_APPEND);
    }
}