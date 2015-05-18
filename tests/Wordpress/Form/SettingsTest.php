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

use UniAlteri\Sellsy\Wordpress\Form\Settings;
use UniAlteri\Sellsy\Wordpress\OptionsBag;
use UniAlteri\Sellsy\Wordpress\Plugin;

class SettingsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Plugin
     */
    protected $pluginMock;

    /**
     * @var OptionsBag
     */
    protected $optionsMock;

    /**
     * @return Plugin|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildPluginMock()
    {
        if (!$this->pluginMock instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->pluginMock = $this->getMock(
                'UniAlteri\Sellsy\Wordpress\Plugin',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->pluginMock;
    }

    /**
     * return OptionsBag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildOptionsMock()
    {
        if (!$this->optionsMock instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->optionsMock = $this->getMock(
                'UniAlteri\Sellsy\Wordpress\OptionsBag',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->optionsMock;
    }

    /**
     * @return Settings
     */
    protected function buildObject()
    {
        return new Settings($this->buildPluginMock(), $this->buildOptionsMock());
    }

    /**
     * @return Settings
     */
    protected function prepareObject(&$field1, &$field2, &$field3)
    {
        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('isDefined')
            ->willReturn(false);

        $field1 = $this->getMock('UniAlteri\Sellsy\Wordpress\Form\Field', array(), array(), '', false);
        $field1->expects($this->any())->method('getName')->willReturn('field1');

        $field2 = $this->getMock('UniAlteri\Sellsy\Wordpress\Form\Field', array(), array(), '', false);
        $field2->expects($this->any())->method('getName')->willReturn('field2');

        $field3 = $this->getMock('UniAlteri\Sellsy\Wordpress\Form\Field', array(), array(), '', false);
        $field3->expects($this->any())->method('getName')->willReturn('field3');

        $fieldsList = array(
            'field3' => $field3,
            'field1' => $field1
        );

        $fieldsCustomList = array(
            'field1' => $field1,
            'field2' => $field2,
            'field3' => $field3
        );

        $this->buildPluginMock()
            ->expects($this->any())
            ->method('listSelectedFields')
            ->willReturn($fieldsList);

        $this->buildPluginMock()
            ->expects($this->any())
            ->method('listFields')
            ->with($this->equalTo('prospect'))
            ->willReturn($fieldsCustomList);

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetExists')
            ->willReturnCallback(
                function ($name) {
                    if ('WPIjsValid' == $name) {
                        return true;
                    }
                }
            );

        prepareMock('__', '*', function($text, $tag) {return $text; });

        return $this->buildObject();
    }

    public function testInitialisation()
    {
        $this->prepareObject($field1, $field2, $field3);
    }

    public function testInitialisationDefault()
    {
        $this->prepareObject($field1, $field2, $field3)->initialize(false);
    }

    public function testGetSections()
    {
        $object = $this->prepareObject($field1, $field2, $field3);

        $this->assertEquals(
            array(
                'sellsy_connexion'	=> 'Connection to Sellsy Account',
                'sellsy_options' => 'Plugin options',
                'sellsy_display' => 'Display options',
                'sellsy_notification' => 'Notification',
                'sellsy_frontValid' => 'Frontside validation',
                'sellsy_Champs' => 'Fields selection'
            ),
            $object->getSections()
        );
    }

    public function testGetSettings()
    {
        $object = $this->prepareObject($field1, $field2, $field3);
        $result = $object->getSettings();

        $this->assertEquals(
            array(
                /* Section Connexion Sellsy */
                Settings::CONSUMER_TOKEN => array(
                    'title' => 'Consumer Token',
                    'desc' => '',
                    'std' => '',
                    'type' => 'text',
                    'section' => 'sellsy_connexion',
                    'originalKey' => 'WPIconsumer_token', //To be compliant with official Sellsy plugin
                ),
                Settings::CONSUMER_SECRET => array(
                    'title' => 'Consumer Secret',
                    'desc' => '',
                    'std' => '',
                    'type' => 'text',
                    'section' => 'sellsy_connexion',
                    'originalKey' => 'WPIconsumer_secret', //To be compliant with official Sellsy plugin
                ),
                Settings::ACCESS_TOKEN => array(
                    'title' => 'User Token',
                    'desc' => '',
                    'std' => '',
                    'type' => 'text',
                    'section' => 'sellsy_connexion',
                    'originalKey' => 'WPIutilisateur_token', //To be compliant with official Sellsy plugin
                ),
                Settings::ACCESS_SECRET => array(
                    'title' => 'User Secret',
                    'desc' => '',
                    'std' => '',
                    'type' => 'text',
                    'section' => 'sellsy_connexion',
                    'originalKey' => 'WPIutilisateur_secret', //To be compliant with official Sellsy plugin
                ),
                /* Section Options du plugin */
                Settings::OPPORTUNITY_CREATION => array(
                    'title' => 'Create',
                    'desc' => '',
                    'type' => 'radio',
                    'std' => '',
                    'section' => 'sellsy_options',
                    'choices' => array(
                        'prospectOnly' => 'Only a lead',
                        'prospectOpportunity' => 'A lead with its opportunity'
                    ),
                    'originalKey' => 'WPIcreer_prospopp', //To be compliant with official Sellsy plugin
                ),
                Settings::OPPORTUNITY_SOURCE => array(
                    'title' => 'Opportunity source names',
                    'desc' => 'You must define this parameter if you must create an opportunity. The source must exist on your <a href="https://www.sellsy.com/?_f=prospection_prefs&action=sources" target="_blank">Sellsy.com</a> account. Several sources can be defined, splited by a comma.' ,
                    'std' => '',
                    'type' => 'text',
                    'section' => 'sellsy_options',
                    'originalKey' => 'WPInom_opp_source', //To be compliant with official Sellsy plugin
                ),
                Settings::FORM_NAME => array(
                    'title' => 'Form name',
                    'desc' => '',
                    'std' => '',
                    'type' => 'text',
                    'section' => 'sellsy_display',
                    'originalKey' => 'WPInom_form', //To be compliant with official Sellsy plugin
                ),
                Settings::DISPLAY_FORM_NAME => array(
                    'title' => 'Display the form name',
                    'desc' => 'To display the name of the form in your page and article.',
                    'type' => 'radio',
                    'std' => '',
                    'section' => 'sellsy_display',
                    'choices' => array(
                        'displayTitle' => 'Oui',
                        'none' => 'Non'
                    ),
                    'originalKey' => 'WPIaff_form', //To be compliant with official Sellsy plugin
                ),
                Settings::SPLIT_COLUMNS => array(
                    'title' => 'Split fields in several columns',
                    'desc' => 'To dispatch fields in several columns. By default, all fields are displayed in a column',
                    'type' => 'text',
                    'std' => '1',
                    'section' => 'sellsy_display',
                    'originalKey' => 'WPIaff_form', //To be compliant with official Sellsy plugin
                ),
                Settings::COLUMNS_CLASS => array(
                    'title' => 'Column HTML classess',
                    'desc' => 'To define the class to use in your HTML column.',
                    'type' => 'text',
                    'std' => '',
                    'section' => 'sellsy_display',
                    'originalKey' => 'WPIaff_form', //To be compliant with official Sellsy plugin
                ),
                Settings::FORM_CUSTOM_HEADER => array(
                    'title' => 'HTML form header',
                    'desc' => 'HTML code to print before the form',
                    'type' => 'textarea',
                    'std' => '',
                    'section' => 'sellsy_display',
                    'originalKey' => null, //To be compliant with official Sellsy plugin
                ),
                Settings::FORM_CUSTOM_FOOTER => array(
                    'title' => 'HTML form footer',
                    'desc' => 'HTML code to print after the form',
                    'type' => 'textarea',
                    'std' => '',
                    'section' => 'sellsy_display',
                    'originalKey' => null, //To be compliant with official Sellsy plugin
                ),
                Settings::MESSAGE_SENT => array(
                    'title' => 'Confirmation message',
                    'desc' => 'To define the message to display when the lead has been created',
                    'type' => 'textarea',
                    'std' =>  'Thanks, your message has been sent.',
                    'section' => 'sellsy_display',
                    'originalKey' => null, //To be compliant with official Sellsy plugin
                ),
                Settings::MESSAGE_ERROR => array(
                    'title' => 'Error message',
                    'desc' => 'To define the message to display when an error has been encounted',
                    'type' => 'textarea',
                    'std' =>  'Your message has not been sent, please check these following fields :',
                    'section' => 'sellsy_display',
                    'originalKey' => null, //To be compliant with official Sellsy plugin
                ),
                Settings::SUBMIT_NOTIFICATION => array(
                    'title' => 'Send a notification by email',
                    'desc' => '',
                    'std' => '',
                    'type' => 'text',
                    'section' => 'sellsy_notification',
                    'originalKey' => 'WPIenvoyer_copie', //To be compliant with official Sellsy plugin
                ),
                Settings::FROM_NOTIFICATION => array(
                    'title' => 'Email sender',
                    'desc' => '',
                    'std' => '',
                    'type' => 'text',
                    'section' => 'sellsy_notification',
                    'originalKey' => null, //Not present in official plugin
                ),
                /* Section Activer validation Client */
                Settings::ENABLE_HTML_CHECK => array(
                    'title' => 'Enable HTML5 validation',
                    'desc' => 'Enable frontside validation build on HTML5 capacity (required, email).',
                    'type' => 'radio',
                    'std' => '',
                    'section' => 'sellsy_frontValid',
                    'choices' => array(
                        'enableJsValidation' => 'Oui',
                        'disableJsValidation' => 'Non'
                    ),
                    'originalKey' => 'WPIjsValid', //To be compliant with official Sellsy plugin
                ),
                /* Section Champs */
                Settings::FIELDS_SELECTED => array(
                    'title' => 'Fields',
                    'desc' => 'Select and sort fields to display in the form',
                    'type' => 'multiselect',
                    'std' => '',
                    'section' => 'sellsy_Champs',
                    'choices' => array('field1'=>$field1, 'field2'=>$field2, 'field3'=>$field3),
                    'originalKey' => null, //Not present in official plugin
                ),
                Settings::MANDATORIES_FIELDS => array(
                    'title' => 'Mandatories Fields',
                    'desc' => 'Select mandatories fields in fhe form',
                    'type' => 'multiselect',
                    'std' => '',
                    'section' => 'sellsy_Champs',
                    'choices' => array('field3'=>'field3', 'field1'=>'field1'),
                    'originalKey' => null //Not present in official plugin
                )
            ),
            $result
        );
    }

    public function testBuildFormsNoDisplaySectionCallback()
    {
        $obj = $this->prepareObject($field1, $field2, $field3);
        $obj->buildForms(null, 'str_replace');
        global $methodCalled;
        $this->assertFalse(in_array('add_settings_section', $methodCalled));
    }

    public function testBuildFormsNoDisplayFieldsCallback()
    {
        $obj = $this->prepareObject($field1, $field2, $field3);
        $obj->buildForms('str_replace', null);
        global $methodCalled;
        $this->assertFalse(in_array('add_settings_section', $methodCalled));
    }

    public function testBuildForms()
    {
        $obj = $this->prepareObject($field1, $field2, $field3);
        $obj->buildForms('str_replace', 'str_replace');
        global $methodCalled;
        $this->assertEquals(6, count(array_keys($methodCalled, 'add_settings_section')));
        $this->assertEquals(19, count(array_keys($methodCalled, 'wp_parse_args')));
        $this->assertEquals(19, count(array_keys($methodCalled, 'add_settings_field')));
    }
}