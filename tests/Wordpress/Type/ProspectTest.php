<?php

namespace UniAlteri\Tests\Sellsy\Wordpress\Type;

use UniAlteri\Sellsy\Wordpress\Type\Prospect;

class ProspectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Prospect
     */
    protected function buildObject()
    {
        return new Prospect();
    }

    public function testGetStandardFields()
    {
        $prospect = $this->buildObject();
        $fields = $prospect->getStandardFields();

        $keys = array(
            0 => 'thirdName',
            1 => 'thirdEmail',
            2 => 'thirdTel',
            3 => 'thirdMobile',
            4 => 'thirdWeb',
            5 => 'contactCivil',
            6 => 'contactName',
            7 => 'contactForename',
            8 => 'contactEmail',
            9 => 'contactTel',
            10 => 'contactMobile',
            11 => 'addressName',
            12 => 'addressPart1',
            13 => 'addressPart2',
            14 => 'addressZip',
            15 => 'addressTown',
            16 => 'addressCountrycode',
        );

        $this->assertEquals($keys, array_keys($fields));
        foreach ($fields as $name=>$field) {
            $this->assertInstanceOf('UniAlteri\Sellsy\Wordpress\Form\CustomField', $field);
            $this->assertEquals($name, $field->getCode());
        }
    }

    public function testPopulateParamsNotCorrespondence()
    {
        $finalSource = array();
        $value = 'test';
        $this->buildObject()->populateParams('fooBar', $value, $finalSource);
        $this->assertEmpty($finalSource);
    }

    public function testPopulateParamsCorrespondence()
    {
        $finalSource = array();
        $value = 'test';
        $this->buildObject()->populateParams('thirdFax', $value, $finalSource);
        $this->assertEquals(array('third'=>array('fax'=>'test'),'contact'=>array('fax'=>'test')), $finalSource);
    }
}