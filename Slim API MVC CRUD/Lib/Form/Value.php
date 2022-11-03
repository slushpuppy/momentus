<?php


namespace Lib\Form;


use Lib\Core\Helper\Db\Map\Column;

/**
 * Class Value
 * @package Lib\Form
 * @property string $data
 * @property int $user_id
 * @property int $form_field_id
 */
abstract class Value extends \Lib\Core\Controller\AbstractController
{
    public const TABLE_NAME = 'form_value';
    protected const ID_COLUMN = '';
    protected const SOURCE_MODULE = 'Lib\Form\Value';
    /**
     * @var Column[]
     */
    protected static $_column_cache = NULL;
    protected static $_column_data_index = 1;


    public static function loadWithFormFieldId(int $formFieldId,int $userId) {
        return static::_load('form_field_id=? and user_id=?','ii',[$formFieldId,$userId]);
    }
    /**
     *
     * @return \Lib\Core\Helper\Db\Map\Column[]
     */
    public static function getColumns()
    {
        if (static::$_column_cache == NULL)
        {
            static::$_column_cache = [
                (new Column(static::TABLE_NAME, 'form_field_id','i')),
                (new Column(static::TABLE_NAME, 'data','b')),
                (new Column(static::TABLE_NAME, 'user_id','i')),


            ];
        }
        return static::$_column_cache;
    }

    /**
     * @param int $form_field_id
     * @param int $userId
     * @param mixed $data
     * @return Value
     */
    public static function createWithUserId(int  $form_field_id, int $userId,$data) {
        $obj = new static();
        $obj->form_field_id = $form_field_id;
        $obj->user_id = $userId;
        $obj->data = $data;
        $obj->save();
        return $obj;
    }

    /**
     * @return bool
     */
    public abstract function isDataValid();

    public function preSave() {

    }

    /**
     * @param bool $updateIfFound
     * @throws \Module\Exception\MySQL
     */
    public function save(bool $updateIfFound = true)
    {
        if ($this->isDataValid()) {
            $this->preSave();
            parent::save($updateIfFound); // TODO: Change the autogenerated stub
        }
        else {
            throw new \Module\Exception\InvalidData();
        }
    }
}

