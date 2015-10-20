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
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     0.8.0
 */

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
        foreach ($fields as $name => $field) {
            $this->assertInstanceOf('UniAlteri\Sellsy\Wordpress\Form\Field', $field);
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
        $this->assertEquals(array('third' => array('fax' => 'test'), 'contact' => array('fax' => 'test')), $finalSource);
    }

    public function testValidateFieldThirdNameNonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('thirdName', $value, array());
    }

    public function testValidateFieldThirdEmailMissingNonMandatory()
    {
        $value = 'contact@unialteri.com';
        $this->buildObject()->validateField('thirdEmail', $value, array());
    }

    public function testValidateFieldThirdTelNonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('thirdTel', $value, array());
    }

    public function testValidateFieldThirdMobileNonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('thirdMobile', $value, array());
    }

    public function testValidateFieldThirdWebNonMandatory()
    {
        $value = 'http://free.fr';
        $this->buildObject()->validateField('thirdWeb', $value, array());
    }

    public function testValidateFieldContactNameNonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('contactName', $value, array());
    }

    public function testValidateFieldContactForenameNonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('contactForename', $value, array());
    }

    public function testValidateFieldContactEmailNonMandatory()
    {
        $value = 'contact@uni-alteri.com';
        $this->buildObject()->validateField('contactEmail', $value, array());
    }

    public function testValidateFieldContactTelNonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('contactTel', $value, array());
    }

    public function testValidateFieldContactMobileNonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('contactMobile', $value, array());
    }

    public function testValidateFieldAddressNameNonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('addressName', $value, array());
    }

    public function testValidateFieldAddressPart1NonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('addressPart1', $value, array());
    }

    public function testValidateFieldAddressPart2NonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('addressPart2', $value, array());
    }

    public function testValidateFieldAddressZipNonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('addressZip', $value, array());
    }

    public function testValidateFieldAddressTownNonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('addressTown', $value, array());
    }

    public function testValidateFieldAddressCountrycodeNonMandatory()
    {
        $value = null;
        $this->buildObject()->validateField('addressCountrycode', $value, array());
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldThirdName()
    {
        $value = null;
        $this->buildObject()->validateField('thirdName', $value, array('thirdName' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldThirdEmailMissing()
    {
        $value = null;
        $this->buildObject()->validateField('thirdEmail', $value, array('thirdEmail' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldThirdEmailFilterVar()
    {
        $value = 'badEmail';
        $this->buildObject()->validateField('thirdEmail', $value, array('thirdEmail' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldThirdTel()
    {
        $value = null;
        $this->buildObject()->validateField('thirdTel', $value, array('thirdTel' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldThirdMobile()
    {
        $value = null;
        $this->buildObject()->validateField('thirdMobile', $value, array('thirdMobile' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldThirdWeb()
    {
        $value = null;
        $this->buildObject()->validateField('thirdWeb', $value, array('thirdWeb' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldThirdWebBadUrl()
    {
        $value = 'dssdqsd#';
        $this->buildObject()->validateField('thirdWeb', $value, array('thirdWeb' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldContactName()
    {
        $value = null;
        $this->buildObject()->validateField('contactName', $value, array('contactName' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldContactForename()
    {
        $value = null;
        $this->buildObject()->validateField('contactForename', $value, array('contactForename' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldContactEmail()
    {
        $value = null;
        $this->buildObject()->validateField('contactEmail', $value, array('contactEmail' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldContactEmailBadEmail()
    {
        $value = 'badEmail';
        $this->buildObject()->validateField('contactEmail', $value, array('contactEmail' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldContactTel()
    {
        $value = null;
        $this->buildObject()->validateField('contactTel', $value, array('contactTel' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldContactMobile()
    {
        $value = null;
        $this->buildObject()->validateField('contactMobile', $value, array('contactMobile' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldAddressName()
    {
        $value = null;
        $this->buildObject()->validateField('addressName', $value, array('addressName' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldAddressPart1()
    {
        $value = null;
        $this->buildObject()->validateField('addressPart1', $value, array('addressPart1' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldAddressPart2()
    {
        $value = null;
        $this->buildObject()->validateField('addressPart2', $value, array('addressPart2' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldAddressZip()
    {
        $value = null;
        $this->buildObject()->validateField('addressZip', $value, array('addressZip' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldAddressTown()
    {
        $value = null;
        $this->buildObject()->validateField('addressTown', $value, array('addressTown' => true));
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateFieldAddressCountrycode()
    {
        $value = null;
        $this->buildObject()->validateField('addressCountrycode', $value, array('addressCountrycode' => true));
    }

    public function testValidateField()
    {
        $value = 'value\"test';
        prepareMock('sanitize_text_field', '*', function ($text) {return $text; });
        $this->buildObject()->validateField('addressCountrycode', $value, array('addressCountrycode' => true));
        $this->assertEquals(
            'value"test',
            $value
        );
    }

    public function testValidateFieldNonMandatory()
    {
        $value = 'value\"test';
        prepareMock('sanitize_text_field', '*', function ($text) {return $text; });
        $this->buildObject()->validateField('addressCountrycode', $value, array());
        $this->assertEquals(
            'value"test',
            $value
        );
    }

    public function testValidateFieldContactCivil()
    {
        $value = 'valUe\"test';
        prepareMock('sanitize_text_field', '*', function ($text) {return $text; });
        $this->buildObject()->validateField('contactCivil', $value, array('contactCivil' => true));
        $this->assertEquals(
            'value"test',
            $value
        );
    }

    public function testValidateFieldContactCivilWoman()
    {
        $value = 'madame';
        prepareMock('sanitize_text_field', '*', function ($text) {return $text; });
        $this->buildObject()->validateField('contactCivil', $value, array('contactCivil' => true));
        $this->assertEquals(
            'woman',
            $value
        );
    }

    public function testValidateFieldContactCivilMan()
    {
        $value = 'monsieur';
        prepareMock('sanitize_text_field', '*', function ($text) {return $text; });
        $this->buildObject()->validateField('contactCivil', $value, array('contactCivil' => true));
        $this->assertEquals(
            'man',
            $value
        );
    }
}
