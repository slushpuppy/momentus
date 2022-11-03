<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 5/9/2018
 * Time: 1:37 PM
 */

namespace Lib\Core\Helper\Db\Map;


use Lib\Core\Controller\AbstractController;

class Column
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var AbstractController
     */
    public $column_class_handler;
    /**
     * @var string
     */
    public $type;
    /**
     * @var Column
     */
    public $join;

    /**
     * @var int max length of column
     */
    public $max_length;
    /*
     * $var $column Join for JOIN operations
     */
    public $join_id_column = '';
    /**
     * @var string prepared function operand
     */
    public $param_function;
    /**
     * @var string
     */
    public $select_function;


    /**
     * @var string operator for equal comparison
     */
    public $equal_operator;

    /**
     * @var array additional columns during select operations
     */
    public $join_select_columns;

    /**
     * @var array aliases for join select columns
     */
    public $join_select_column_aliases;
    /**
     * @var string Column Alias
     */
    public $alias;
    /**
     * @var string Column table
     */
    public $table;
    /**
     * @var string Table alias
     */
    public $table_alias;
    /**
     * Column constructor.
     * @param $name
     * @param $type
     * @param $join
     * @param $param_function
     * @param $select_function
     */
    public function __construct(string $table, string $name = '',string $type  = '',string $param_function = '?',string $select_function = '',string $equal_operator = '')
    {
        $this->name = $name;
        $this->table = $table;
        $this->type = $type;
        $this->param_function = $param_function;
        $this->select_function = $select_function;
        if ($equal_operator == '')
        $this->equal_operator = static::_getEqualOperator($this->type,$this->param_function);
        else $this->equal_operator = $equal_operator;
        $this->join_select_columns = [];
    }

    public static function withController(AbstractController $obj,string $name,string $type,string $param_function = '?',string $select_function = '',string $equal_operator = '') {
        return new self($obj::TABLE_NAME,$name,$type,$param_function,$select_function,$equal_operator);
    }

    public function join(string $join_id_column,Column $join) {
        $this->join = $join;
        $this->join_id_column = $join_id_column;
        return $this;
    }


    /**
     * @param string $alias
     * @return $this
     */
    public function setTableAlias(string $alias) {
        $this->table_alias = $alias;
        return $this;
    }

    /**
     * Returns aliased name for column name
     * @return string
     */
    public function getAlias() {
        return ($this->alias != null) ? $this->alias : $this->name;
    }

    public function getNameForSelect() {
        if ($this->select_function != "")
        {
            return $this->select_function;
        }
        $nameWithTable = $this->getTableOnAlias().$this->name;
        if ($this->alias != "")
        {
            $col = $nameWithTable . ' AS '.$this->alias;
        } else {
            $col = $nameWithTable;
        }

        return $col;
    }

    public function getTableOnAlias() {
        return (($this->table_alias != null) ? $this->table_alias : $this->table).'.';
    }

    /**
     * Alias for column name
     * @param string $alias
     * @return $this
     */
    public function setAlias(string $alias) {
        $this->alias = $alias;
        return $this;
    }

    public static function _getEqualOperator(string $type,string $param_function) {
        if ($param_function == '?') {
            if ($type == 's') return 'LIKE';
            if ($type == 'd' || $type == 'i') return '=';
        }

    }

    /**
     * @param string $str
     * @return $this
     */
    public function addSelectColumn(string $str) {
        $this->join_select_columns[] = $str;
        return $this;
    }
    /**
     * @param string $str
     * @return $this
     */
    public function addSelectColumnAlias(string $str,string $alias) {
        $this->addSelectColumn($str);
        $this->join_select_column_aliases[$str] = $alias;
        return $this;
    }
    /**
     * @param array $arr
     * @return $this
     */
    public function setSelectColumns(array $arr) {
        $this->join_select_columns = $arr;
        return $this;
    }


    public function setClassHandler(AbstractController $obj) {
        $this->column_class_handler = $obj;
        return $this;
    }

    public static function getSysTextColumn() {
        return (new Column('sys_string_index','name','s'));
    }

    /**
     * @param int $length
     * @return $this
     */
    public function setMaxLength(int $length) {
        $this->max_length = $length;
        return $this;
    }

}
