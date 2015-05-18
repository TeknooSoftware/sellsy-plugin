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

namespace UniAlteri\Sellsy\Wordpress\Type;

use UniAlteri\Sellsy\Wordpress\Form\Field;

/**
 * Class Prospect.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Prospect implements TypeInterface
{
    /**
     * Correspondence between fields in form and sellsy api.
     */
    protected $fieldsCorrespondence = array(
        'thirdName' => array('third.name','contact.name'),
        'thirdType' => 'third.type',
        'thirdEmail' => array('third.email','contact.email'),
        'thirdTel' => array('third.tel','contact.tel'),
        'thirdFax' => array('third.fax','contact.fax'),
        'thirdMobile' => array('third.mobile','contact.mobile'),
        'thirdWeb' => 'third.web',
        'thirdSiret' => 'third.siret',
        'thirdVat' => 'third.vat',
        'thirdRcs' => 'third.rcs',
        'thirdApenaf' => 'third.apenaf',
        'thirdCapital' => 'third.capital',
        'thirdStickyNote' => 'third.stickyNote',
        'contactCivil' => 'contact.civil',
        'contactName' => array('third.name','contact.name'),
        'contactForename' => 'contact.forename',
        'contactEmail' => array('third.email','contact.email'),
        'contactTel' => array('third.tel','contact.tel'),
        'contactFax' => array('third.fax','contact.fax'),
        'contactMobile' => array('third.mobile','contact.mobile'),
        'contactPosition' => 'contact.position',
        'addressName' => 'address.name',
        'addressPart1' => 'address.part1',
        'addressPart2' => 'address.part2',
        'addressZip' => 'address.zip',
        'addressTown' => 'address.town',
        'addressCountrycode' => 'address.countrycode',
    );

    /**
     * Correspondence between fields and field value.
     */
    protected $fieldsName = array();

    /**
     * Constructor, To define the list of standard fields available in Sellsy.
     */
    public function __construct()
    {
        $this->fieldsName = array(
            'thirdName' => array(
                'type' => 'text',
                'name' => __('Last name or Company name', 'wpsellsy'),
                'code' => 'thirdName',
                'description' => __('Last name or Company name', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'thirdEmail' => array(
                'type' => 'text',
                'name' => __('Company email', 'wpsellsy'),
                'code' => 'thirdEmail',
                'description' => __('Company email', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'thirdTel' => array(
                'type' => 'text',
                'name' => __('Company phone', 'wpsellsy'),
                'code' => 'thirdTel',
                'description' => __('Company phone', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'thirdMobile' => array(
                'type' => 'text',
                'name' => __('Company mobile', 'wpsellsy'),
                'code' => 'thirdMobile',
                'description' => __('Company mobile', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'thirdWeb' => array(
                'type' => 'text',
                'name' => __('Website', 'wpsellsy'),
                'code' => 'thirdWeb',
                'description' => __('Site web', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'contactCivil' => array(
                'type' => 'radio',
                'name' => __('Civility', 'wpsellsy'),
                'code' => 'contactCivil',
                'description' => __('Civility', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => (object) array(
                    (object) array(
                        'id' => __('woman', 'wpsellsy'),
                        'value' => __('Mrs', 'wpsellsy'),
                        'rank' => 0,
                    ),
                    (object) array(
                        'id' => __('man', 'wpsellsy'),
                        'value' => __('Mr', 'wpsellsy'),
                        'rank' => 1,
                    ),
                ),
            ),
            'contactName' => array(
                'type' => 'text',
                'name' => __('Last name', 'wpsellsy'),
                'code' => 'contactName',
                'description' => __('Last name', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'contactForename' => array(
                'type' => 'text',
                'name' => __('First name', 'wpsellsy'),
                'code' => 'contactForename',
                'description' => __('First name', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'contactEmail' => array(
                'type' => 'text',
                'name' => __('Email', 'wpsellsy'),
                'code' => 'contactEmail',
                'description' => __('Email', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'contactTel' => array(
                'type' => 'text',
                'name' => __('Phone', 'wpsellsy'),
                'code' => 'contactTel',
                'description' => __('Phone', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'contactMobile' => array(
                'type' => 'text',
                'name' => __('Mobile', 'wpsellsy'),
                'code' => 'contactMobile',
                'description' => __('Mobile', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'addressName' => array(
                'type' => 'text',
                'name' => __('Address 1', 'wpsellsy'),
                'code' => 'addressName',
                'description' => __('Adress 1', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'addressPart1' => array(
                'type' => 'text',
                'name' => __('Adress 2', 'wpsellsy'),
                'code' => 'addressPart1',
                'description' => __('Adress 2', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'addressPart2' => array(
                'type' => 'text',
                'name' => __('Adress 3', 'wpsellsy'),
                'code' => 'addressPart2',
                'description' => __('Adress 3', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'addressZip' => array(
                'type' => 'text',
                'name' => __('Zipcode', 'wpsellsy'),
                'code' => 'addressZip',
                'description' => __('Zipcode', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'addressTown' => array(
                'type' => 'text',
                'name' => __('City', 'wpsellsy'),
                'code' => 'addressTown',
                'description' => __('City', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
            'addressCountrycode' => array(
                'type' => 'text',
                'name' => __('Country', 'wpsellsy'),
                'code' => 'addressCountrycode',
                'description' => __('Country', 'wpsellsy'),
                'defaultValue' => '',
                'prefsList' => null,
            ),
        );
    }

    /**
     * Get the list of standards fields defined for this type by Sellsy.
     *
     * @return Field[]
     */
    public function getStandardFields()
    {
        //Add default prospect fields as fields
        $final = array();
        foreach ($this->fieldsName as $fieldName => $fieldParams) {
            $final[$fieldName] = new Field(
                $fieldName,
                $fieldParams['type'],
                $fieldParams['name'],
                $fieldParams['code'],
                $fieldParams['description'],
                $fieldParams['defaultValue'],
                $fieldParams['prefsList'],
                false
            );
        }

        return $final;
    }

    /**
     * To populate this type from an array of values with the correspondence
     * table defined for this type.
     *
     * @param string $fieldName
     * @param mixed  $value
     * @param array  $finalSource
     */
    public function populateParams($fieldName, &$value, &$finalSource)
    {
        //If there are a correspondence
        if (isset($this->fieldsCorrespondence[$fieldName])) {
            foreach ((array) $this->fieldsCorrespondence[$fieldName] as $apiPath) {
                //Populate for each path defined for this field the final source
                $path = explode('.', $apiPath);

                if (count($path) == 2) {
                    $finalSource[$path[0]][$path[1]] = $value;
                }
            }
        }
    }

    /**
     * To check the validity of a value for a destination field.
     *
     * @param string $fieldName
     * @param mixed  $value
     * @param array  $mandatoriesFields
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function validateField($fieldName, &$value, $mandatoriesFields = array())
    {
        switch ($fieldName) {
            case 'thirdName':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check the company name', 'wpsellsy'));
                }
                break;
            case 'thirdEmail':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL) || (isset($mandatoriesFields[$fieldName]) && empty($value))) {
                    throw new \Exception(__('Please check the company email', 'wpsellsy'));
                }
                break;
            case 'thirdTel':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check the company phone', 'wpsellsy'));
                }
                break;
            case 'thirdMobile':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check the company mobile', 'wpsellsy'));
                }
                break;
            case 'thirdWeb':
                if (!filter_var($value, FILTER_VALIDATE_URL) || (isset($mandatoriesFields[$fieldName]) && empty($value))) {
                    throw new \Exception(__('Please check the website url', 'wpsellsy'));
                }
                break;
            case 'contactCivil':
                $value = strtolower($value);
                switch ($value) {
                    case 'madame':
                        $value = 'woman';
                        break;
                    case 'monsieur':
                        $value = 'man';
                        break;
                }
                break;
            case 'contactName':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check your last name', 'wpsellsy'));
                }
                break;
            case 'contactForename':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check your first name', 'wpsellsy'));
                }
                break;
            case 'contactEmail':
                if ((isset($mandatoriesFields[$fieldName]) && empty($value)) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception(__('Please check your email', 'wpsellsy'));
                }
                break;
            case 'contactTel':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check your phone number', 'wpsellsy'));
                }
                break;
            case 'contactMobile':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check your mobile number', 'wpsellsy'));
                }
                break;
            case 'addressName':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check your address 1', 'wpsellsy'));
                }
                break;
            case 'addressPart1':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check your address 2', 'wpsellsy'));
                }
                break;
            case 'addressPart2':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check your address 3', 'wpsellsy'));
                }
                break;
            case 'addressZip':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check your zipcode', 'wpsellsy'));
                }
                break;
            case 'addressTown':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check your city name', 'wpsellsy'));
                }
                break;
            case 'addressCountrycode':
                if (isset($mandatoriesFields[$fieldName]) && empty($value)) {
                    throw new \Exception(__('Please check your country name', 'wpsellsy'));
                }
                break;
        }

        //No error, sanitaize inputs
        $value = \sanitize_text_field(stripslashes($value));
    }
}
