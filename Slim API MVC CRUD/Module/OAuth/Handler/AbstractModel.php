<?php
/**
 * Created by PhpStorm.
 * Account: Luke
 * Date: 20/8/2018
 * Time: 10:46 PM
 */

namespace Module\OAuth\Handler;


use Config\Db;
use Lib\Core\Helper\Db\Conn;
use Lib\Core\Helper\Db\Map\Column as Column;


abstract class AbstractModel extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'user_auth';

    protected const METHOD = '';
    protected const ID_COLUMN = 'id';

    protected static $_column_cache = NULL;
    protected $data_field_cache = NULL;


    /**
     * Set field for authentication data, e.g. facebook userId or special information to uniquely identify the user. DO NOT SAVE OAUTH TOKENS IN HERE.
     * Create a respective setDataField() in child class
     * @param object $json - Usually data object
     * @return void
     */
    public function setDataField(IData $json) {
        $this->data = \json_encode($json);
    }

    /**
     * Get decoded json string as array
     * Create a respective getDataField() in child class
     * @return object
     */
    abstract public function getDataField() : IData;

    protected static function getColumns() {
        if (static::$_column_cache == NULL) {
            static::$_column_cache = [
                new Column(static::TABLE_NAME,'user_id', 'i'),
                (new Column(static::TABLE_NAME,'auth_method_id', 'i'))->join('id',(new Column('auth_method','name','s'))->setAlias('auth_method_id')),
                new Column(static::TABLE_NAME,'data', 's'),
                new Column(static::TABLE_NAME,'data_search_field', 's'),
            ];
        }
        return static::$_column_cache;
    }

}
