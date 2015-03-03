<?php

namespace UniAlteri\Tests\Sellsy\Wordpress\Form;

use UniAlteri\Sellsy\Wordpress\Form\CustomField;

class CustomFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testGetId()
    {
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertEquals(123, $customFieldObject->getId());
    }

    public function testGetType()
    {
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertEquals('typeValue', $customFieldObject->getType());
    }

    public function testGetName()
    {
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertEquals('nameValue', $customFieldObject->getName());
    }

    public function testGetCode()
    {
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertEquals('codeValue', $customFieldObject->getCode());
    }

    public function testGetDescription()
    {
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertEquals('descValue', $customFieldObject->getDescription());
    }

    public function testGetDefaultValueForBooleanDefined()
    {
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, 'boolean');
        $this->assertEquals('defaultValue', $customFieldObject->getDefaultValue());
    }

    public function testGetDefaultValueForBooleanUnDefined()
    {
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', null, array(), false, 'boolean');
        $this->assertEquals('N', $customFieldObject->getDefaultValue());
    }

    public function testGetDefaultValue()
    {
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, 'boolean');
        $this->assertEquals('defaultValue', $customFieldObject->getDefaultValue());
    }

    public function testGetDefaultValueOptions()
    {
        $options = array(
            (object) array(
                'id' => 1,
                'value' => 'val',
                'isDefault' => 'N',
                'rank' => 1
            ),
            (object) array(
                'id' => 2,
                'value' => 'defaultOptionValue',
                'isDefault' => 'Y',
                'rank' => 2
            ),
            (object) array(
                'id' => 3,
                'value' => 'val',
                'isDefault' => 'N',
                'rank' => 3
            )
        );
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', $options, false, 'boolean');
        $this->assertEquals('defaultOptionValue', $customFieldObject->getDefaultValue());
    }

    public function testGetOptions()
    {
        $options = array(
            (object) array(
                'id' => 1,
                'value' => 'val',
                'isDefault' => 'N',
                'rank' => 1
            ),
            (object) array(
                'id' => 2,
                'value' => 'defaultOptionValue',
                'isDefault' => 'Y',
                'rank' => 3
            ),
            (object) array(
                'id' => 3,
                'value' => 'val',
                'isDefault' => 'N',
                'rank' => 2
            )
        );
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', $options, false, 'boolean');
        $this->assertEquals(
            array(
                1 => array(
                    'id' => 1,
                    'value' => 'val'
                ),
                2 => array(
                    'id' => 3,
                    'value' => 'val'
                ),
                3 => array(
                    'id' => 2,
                    'value' => 'defaultOptionValue'
                )
            ),
            $customFieldObject->getOptions()
        );
    }

    public function testIsCustomField()
    {
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), true, false);
        $this->assertTrue($customFieldObject->isCustomField());
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertFalse($customFieldObject->isCustomField());
    }

    public function testIsRequiredField()
    {
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertFalse($customFieldObject->isRequiredField());
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, 'boolean');
        $this->assertTrue($customFieldObject->isRequiredField());
    }

    public function testToString()
    {
        $customFieldObject = new CustomField(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertEquals('nameValue', (string) $customFieldObject);
    }
}