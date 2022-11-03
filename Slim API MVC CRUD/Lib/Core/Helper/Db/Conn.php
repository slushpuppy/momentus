<?php
namespace Lib\Core\Helper\Db;

class Conn {

    private static $_i;
    private $conn;

    private function __construct()
    {
        /* activate reporting */
        $driver = new \mysqli_driver();
        $driver->report_mode = \MYSQLI_REPORT_ERROR;

        $this->conn = new mysqli(
            \Config\Db::HOST,
            \Config\Db::USER,
            \Config\Db::PASS,
            \Config\Db::NAME,
            \Config\Db::PORT,
            false
        );
        // check if a connection established
        if( $this->conn->connect_error ) {
            throw new \mysqli_sql_exception($this->conn->connect_error , $this->conn->connect_errno);
        }
    }

    /**
     * @return \Lib\Core\Helper\Db\mysqli
     */
    public function get() {
        return $this->conn;
    }

    public static function i()
    {
        if (self::$_i == NULL) {
            self::$_i = new self;
        }
        return self::$_i;
    }

    /**
     * @param $array
     * @return string
     */
    public static function buildUpdateCols($array) {
		$col = \array_keys($array);
		\array_walk($col, function(&$value, $key) { $value .= '=?'; } );
		return \implode(',',$col);
	}

    /**
     * @param $array
     * @return string
     */
    public static function buildParamType($array) {
		$return = '';
		foreach($array as $val) {
			if (\ctype_digit($val)) $return .= 'i';
			else if (\is_numeric($val)) $return .= 'd';
			else $return .= 's';
		}
		return $return;
	}
}