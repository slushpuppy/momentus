<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 7/8/2018
 * Time: 8:26 PM
 */

namespace Lib\Core\Controller;

use Config\File;
use Exception\NotImplemented;
use FileSystem\FileInterface;
use Lib\Core\Helper\Db\Conn;
use Lib\Core\Helper\Db\Map\Column;
use Lib\Core\System\DbString;
use SQLBuilder\Universal\Query\DeleteQuery;
use SQLBuilder\Universal\Query\SelectQuery;
use SQLBuilder\Driver\MySQLDriver;
use SQLBuilder\ArgumentArray;
use \Module\Exception\Controller as ControllerException;
use SQLBuilder\Universal\Query\UpdateQuery;

abstract class AbstractController
{
    public const TABLE_NAME = '';
    /**
     * @var \Lib\Core\Helper\Db\Map\Column[] $column_map array
     */
    protected static $column_map = array();
    protected $_joinColumn = [];
    private $_data = NULL;
    protected const ID_COLUMN = '';

    protected const SOURCE_MODULE='';

    protected const CLASS_HANDLER_FIELD='';

    protected const HIDE_COLUMN="";


    /**
     * @return void
     */

    /**
     * @param $array
     */

    public function __construct()
    {
        $this->_init();
    }

    /**
     * @return array
     */
    public function getJoinColumns() {
        return $this->_joinColumn;
    }

    /**
     * @param array $array
     */
    public function _loadData(array $array)
    {

        foreach($array as $key => $val) {
            $index = -1;
            if (($pos = \strpos($key,"[")) !== FALSE) {

                $index = \substr($key,$pos + 1,-1);
                $key = \substr($key,0,$pos);

                //$this->_data
            }
            if (\array_key_exists($key,$this->_data)) {
                if ($index == -1)
                $this->_data[$key] = $val;
                else $this->_data[$key][$index] = $val;
            }

        }
    }

    /**
     * @return array
     */
    public function _getData() {
        return $this->_data;
    }

    /**
     * @param string $query
     * @param string $types
     * @param mixed ...$param
     * @return int
     * @throws \Module\Exception\MySQL
     */
    public function _selectQueryRowCheck(string $query, string $types, ...$param) {
        $conn = Conn::i()->get();
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types,...$param);
        $stmt->execute();
        return $stmt->num_rows;
    }

    /**
     * Load with ID specified in ID_COLUMN
     * @param int $id
     * @return $this|null
     * @throws \Module\Exception\MySQL
     */
    public static function loadWithId(int $id) {
        return static::_load(static::TABLE_NAME.'.'.static::ID_COLUMN.'=?','i',[$id]);
    }

    /**
     * @return \Lib\Core\Helper\Db\Map\Column[]
     */
    abstract protected static function getColumns();

    /**
     * @param string $where
     * @param string $paramTypes
     * @param array $param
     * @return array
     * @throws \Module\Exception\MySQL
     */
    protected static function _loadAll(string $where, string $paramTypes, array $param) {
        $query = new SelectQuery;
        $columns = [];
        $jColumns = [];

        if (static::HIDE_COLUMN != "") {
            $where = "(".$where.") AND ".static::HIDE_COLUMN."=0";
        }

        foreach (static::getColumns() as $col) {
            if ($col->join == null)
            {
                $columns[] = $col->getNameForSelect();
            }

            while ($col->join != null) {
                $jcol = $col->join;
                if ($jcol->name != "") {
                    $columns[] = $jcol->getNameForSelect();

                }
                if ($jcol->name == "" || $jcol->join != null)
                {
                    $columns[] = $col->getNameForSelect();
                }
                $query->join($jcol->table,$jcol->table_alias,'left')->on(
                      $col->getTableOnAlias().$col->name."=".$jcol->getTableOnAlias().$col->join_id_column
                );
                foreach ($jcol->join_select_columns as $jscol)
                {
                    $jscol_name = $jscol;
                    if ($jcol->join_select_column_aliases[$jscol]) {
                        $jscol_name  = $jcol->join_select_column_aliases[$jscol];
                        $columns[] = $jcol->getTableOnAlias().$jscol . ' as '.$jscol_name ;
                    }
                    else {
                        $columns[] = $jcol->getTableOnAlias().$jscol_name;
                    }
                    $jColumns[] = $jscol_name;
                }
                $col = $jcol;
            }
        }
        if (static::ID_COLUMN != '') $columns[] = static::TABLE_NAME.'.'.static::ID_COLUMN;
        $query->select($columns)
            ->from(static::TABLE_NAME);
        $query = $query->toSql(new MySQLDriver,new ArgumentArray);
        $query .= ' where '.$where;
        //var_dump($query);var_dump($paramTypes);var_dump($param);
        $db = Conn::i()->get();
        $stmt = $db->prepare($query);
        $stmt->bind_param_array($paramTypes,$param);

        $out = [];
        $stmt->stmt_bind_assoc($out);
        $stmt->execute();
        $return = [];
        $obj = NULL;
        while ($stmt->fetch())
        {
            if (static::CLASS_HANDLER_FIELD != '' && isset($out[static::CLASS_HANDLER_FIELD])) {
                $class = $out[static::CLASS_HANDLER_FIELD];
            } else {
                $class = get_called_class();
            }

            /** @var AbstractController $obj */
            $obj = new $class();
            $obj->_joinColumn = $jColumns;

            $obj->_loadData($out);
            $return[] = $obj;
        }
        return $return;
    }

    /**
     * @param string $where - a.id=2 or a.name LIKE 'example'
     * @param string $paramTypes
     * @param array $param
     * @return null|$this
     * @throws \Module\Exception\MySQL
     */
    protected static function _load(string $where,string $paramTypes,array $param)
    {
        $return = static::_loadAll($where,$paramTypes,$param);
        if (count($return) > 0) return $return[0];
        return null;
    }

    /**
     *
     */
    public function id()
    {
        if (array_key_exists(static::ID_COLUMN,$this->_data))
        {
            return $this->_data[static::ID_COLUMN];
        }
        throw new ControllerException(
            ControllerException::ID_NOT_EXIST
        );
    }
    public function __get($name)
    {
        $this->_init();

        if (!array_key_exists($name,$this->_data) && !\array_key_exists($name,$this->_joinColumn))
        {
            throw new \OutOfRangeException('Column '.$name.' does not exist');
        }
        return $this->_data[$name];
    }

    public function __isset($name) {
        return array_key_exists($name,$this->_data) || \array_key_exists($name,$this->_joinColumn);
    }


    private function _init() {
        if ($this->_data == NULL) {
            foreach (static::getColumns() as $col) {
                if ($col->join == NULL)
                {
                    $this->_data[$col->getAlias()] = null;
                }

                while ($col->join != NULL) {
                    $jCol = $col->join;

                    if ($jCol->name != '') {
                        $this->_data[$jCol->getAlias()] = null;
                    }
                    if ($jCol->name == '' || $jCol->join != null)
                    {
                        $this->_data[$col->getAlias()] = null;
                    }
                     foreach ($jCol->join_select_columns as $jscol) {
                         $jscol_name = $jscol;
                         if ($jCol->join_select_column_aliases[$jscol]) {
                             $jscol_name  = $jCol->join_select_column_aliases[$jscol];
                         }
                         $this->_joinColumn[$jscol_name] = null;
                         $this->_data[$jscol_name] = null;
                     }
                    $col = $jCol;
                }

            }
            if (static::ID_COLUMN != '') $this->_data[static::ID_COLUMN] = null;
        }
    }
    public function __set($name, $value)
    {
        $this->_init();

        if (!array_key_exists($name,$this->_data) && !\array_key_exists($name,$this->_joinColumn))
        {
            throw new \OutOfRangeException('Column '.$name.' does not exist');
        }
        $this->_data[$name] = $value;
    }

    /**
     * @param bool $updateIfFound
     * @throws \Module\Exception\MySQL
     * @return void
     */
    public function save(bool $updateIfFound = true)
    {
        $db = Conn::i()->get();

        $columns = [];
        $values = [];
        $placeholder = [];
        $types = '';
        $update = '';
        foreach (static::getColumns() as $col) {
                if ($col->join != null) {
                    $jcol = $col->join;

                    if ($jcol->name == "" || $jcol->join != "") {
                        $placeholder[] = $col->param_function;
                        $types .= $col->type;
                        $colValue = $this->_data[$col->getAlias()];
                    }
                    else {
                        $placeholder[] = '(select '.$col->join_id_column.' from '.$jcol->table.' where '.$jcol->name .' '.$jcol->equal_operator.' '.$jcol->param_function.')';
                        $types .= $jcol->type;
                        $colValue = $this->_data[$jcol->getAlias()];

                    }

                } else {
                    $placeholder[] = $col->param_function;
                    $types .= $col->type;
                    $colValue = $this->_data[$col->getAlias()];
                }
                $columns[] = "`".$col->name."`";
            if (\is_array($colValue)) array_push($values,...$colValue);
            else $values[] = $colValue;
            $update .= $col->name.'=VALUES('.$col->name.'),';
        }
            if (static::ID_COLUMN != '' && isset($this->_data["id"]) && $this->_data["id"] > 0) {
                $columns[] = static::ID_COLUMN;
                $values[] = $this->id();
                $placeholder[] = '?';
                $types .= 'i';
            }

        $update = \substr($update,0,-1);
        $sql = 'INSERT INTO '.static::TABLE_NAME.' ('.implode(',',$columns).') VALUES ('.implode(',',$placeholder).')';

        if ($updateIfFound) {
            $sql .= ' ON DUPLICATE KEY UPDATE '.$update;
            if (static::ID_COLUMN != '') $sql .= ','.static::ID_COLUMN.'=LAST_INSERT_ID('.static::ID_COLUMN.')';
        }
         //var_dump($sql);var_dump($types);var_dump($values);
        //\error_log($sql,0);\error_log(print_r($values,TRUE),0);
        $stmt = $db->prepare($sql);
        $stmt->bind_param_array($types, $values);
        $stmt->execute();
        if ($stmt->insert_id != '') $this->_data[static::ID_COLUMN]  = $stmt->insert_id;


    }

    public function delete()
    {
        $this->_hideDelete(false);
    }

    public function hide() {
        if (static::HIDE_COLUMN != '')
        {
            $this->_hideDelete(true,false);
        } else {
            throw new NotImplemented();
        }
    }
    protected function _hideDelete($hide = false,$unhide = false)
    {
        $db = Conn::i()->get();
        $columns = [];
        $values = [];
        $placeholder = [];
        $types = '';
        foreach (static::getColumns() as $col) {
            if ($col->join != null) {
                $jcol = $col->join;

                if ($jcol->name == "" || $jcol->join != "") {
                    $placeholder[] = $col->param_function;
                    $types .= $col->type;
                    $colValue = $this->_data[$col->getAlias()];
                }
                else {
                    $placeholder[] = '(select '.$col->join_id_column.' from '.$jcol->table.' where '.$jcol->name .' '.$jcol->equal_operator.' '.$jcol->param_function.')';
                    $types .= $jcol->type;

                    $colValue = $this->_data[$jcol->getAlias()];
                }

            } else {
                $placeholder[] = $col->param_function;
                $types .= $col->type;
                $colValue = $this->_data[$col->getAlias()];
            }
            $columns[] = "`".$col->name."`";
            if (\is_array($colValue)) array_push($values,...$colValue);
            else $values[] = $colValue;
        }
        if (static::ID_COLUMN != '' && isset($this->_data["id"]) && $this->_data["id"] > 0) {
            $columns[] = static::ID_COLUMN;
            $values[] = $this->id();
            $placeholder[] = '?';
            $types .= 'i';
        }
        $where = '';
        foreach ($columns as $index => $col ) {
            if (empty($values[$index])) {
                $where .= '('.$col . '='. $placeholder[$index].' OR '.$col . ' IS NULL) AND ';
            } else {
                $where .= $col . '='. $placeholder[$index].' AND ';
            }

        }
        $where = substr($where,0,-5);
        if ($hide) {
            if ($unhide == true) $unhide = 0;
            else $unhide = 1;

            $sql = 'update '.static::TABLE_NAME.' set '.static::HIDE_COLUMN.'='.$unhide.' where '.$where;
            //error_log($sql,0);
            //error_log(print_r($values,TRUE),0);
        } else {
            $sql = 'delete from '.static::TABLE_NAME.' where '.$where;
        }
        //\var_dump($sql);\var_dump($types);\var_dump($values);
        $stmt = $db->prepare($sql);
        $stmt->bind_param_array($types, $values);
        $stmt->execute();
    }

    /**
     * Copy all database fields to parameter object
     * @param $object
     */
    public function copyColumnFieldsTo(&$object) {
        foreach($this->_data as $name => $value) {
            $object->$name = $value;
        }
    }

    /**
     * @param string $table_alias
     * @return Column
     */
    public static function getDbColumnJoin(string $table_alias='') {
        $columns = static::getColumns();
        //$first = array_shift($columns);
        $column = new Column(STATIC::TABLE_NAME);
        $column->setTableAlias($table_alias);
        foreach ($columns as $name => $col) {
            $column->addSelectColumnAlias($col->name,((STATIC::TABLE_NAME) ? : STATIC::TABLE_NAME).'_'.$col->name);
        }
        return $column;
    }
}