<?php

namespace Tests\Lib\Form;

use Lib\Core\System\DbString;
use Lib\Form\Field;
use Lib\Form\Value\Integer;
use Lib\Form\Value\Json;
use PHPUnit\Framework\TestCase;
use Tests\Init;

class FieldTest extends TestCase
{
    public function testCreate() {

        $db1 = new DbString();
        $db1->name = 'age';
        $db1->save();

        $db1 = new DbString();
        $db1->name = 'age1';
        $db1->save();
        $db1 = new DbString();
        $db1->name = FieldTestGroup::getFormGroupKey();
        $db1->save();
        $form = new FieldTestGroup();
        $form->type = Json::class;
        $form->field_key = 'age';
        $form->save();

        $form1 = new FieldTestGroup();
        $form1->type = Json::class;
        $form1->field_key = 'age1';
        $form1->save();

        Integer::createWithUserId($form->id(),Init::$testUser1->id(),1);

        Json::createWithUserId($form1->id(),Init::$testUser1->id(),['test' => 'test']);

        $newValues = FieldTestGroup::loadWithFieldKey('age');
        foreach($newValues as $val) {
            $value = $val->getValue(Init::$testUser1->id());

            $this->assertEquals($value->form_field_id,$form->id());
            $this->assertEquals($value->data,1);
            $this->assertEquals($value->user_id,Init::$testUser1->id());
        }

        $newValues = FieldTestGroup::loadWithFieldKey('age1');
        foreach($newValues as $val) {
            $value = $val->getValue(Init::$testUser1->id());
            //$this->assertEquals($value->data)
        }
    }
}
class FieldTestGroup extends Field {

    /**
     * @return string
     */
    public static function getFormGroupKey()
    {
       return 'test';
    }
}
