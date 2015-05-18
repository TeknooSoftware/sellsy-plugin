<?php
/**
 * Sellsy Wordpress plugin.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     0.8.0
 */

namespace UniAlteri\Tests\Sellsy\Wordpress\Form;

use UniAlteri\Sellsy\Wordpress\Form\Field;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testGetId()
    {
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertEquals(123, $customFieldObject->getId());
    }

    public function testGetType()
    {
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertEquals('typeValue', $customFieldObject->getType());
    }

    public function testGetName()
    {
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertEquals('nameValue', $customFieldObject->getName());
    }

    public function testGetCode()
    {
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertEquals('codeValue', $customFieldObject->getCode());
    }

    public function testGetDescription()
    {
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertEquals('descValue', $customFieldObject->getDescription());
    }

    public function testGetDefaultValueForBooleanDefined()
    {
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, 'boolean');
        $this->assertEquals('defaultValue', $customFieldObject->getDefaultValue());
    }

    public function testGetDefaultValueForBooleanUnDefined()
    {
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', null, array(), false, 'boolean');
        $this->assertEquals('N', $customFieldObject->getDefaultValue());
    }

    public function testGetDefaultValue()
    {
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, 'boolean');
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
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', $options, false, 'boolean');
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
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', $options, false, 'boolean');
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

    public function testIsField()
    {
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), true, false);
        $this->assertTrue($customFieldObject->isCustomField());
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertFalse($customFieldObject->isCustomField());
    }

    public function testIsRequiredField()
    {
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertFalse($customFieldObject->isRequiredField());
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, 'boolean');
        $this->assertTrue($customFieldObject->isRequiredField());
    }

    public function testToString()
    {
        $customFieldObject = new Field(123, 'typeValue', 'nameValue', 'codeValue', 'descValue', 'defaultValue', array(), false, false);
        $this->assertEquals('nameValue', (string) $customFieldObject);
    }
}