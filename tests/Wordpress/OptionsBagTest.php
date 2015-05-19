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

namespace UniAlteri\Tests\Sellsy\Wordpress;

use UniAlteri\Sellsy\Wordpress\Form\Settings;
use UniAlteri\Sellsy\Wordpress\OptionsBag;

class OptionsBagTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        global $methodCalled;
        global $methodArgs;
        global $methodMocks;
        $methodCalled = array();
        $methodArgs = array();
        $methodMocks = array();
    }

    /**
     * @return OptionsBag
     */
    protected function buildObject()
    {
        return new OptionsBag();
    }

    public function testRegisterHooks()
    {
        global $methodCalled;
        global $methodArgs;

        $object = $this->buildObject();
        $object->registerHooks();
        $this->assertEquals(
            $methodCalled,
            array(
                'register_setting',
                'add_filter',
            )
        );
        $this->assertEquals(
            $methodArgs,
            array(
                array(OptionsBag::WORDPRESS_SETTINGS_NAME, OptionsBag::WORDPRESS_SETTINGS_NAME, array($object, 'sanitize')),
                array(OptionsBag::WORDPRESS_VALIDATE_FILTER, array($object, 'validate'), 10, 1),
            )
        );
    }

    public function testReloadEmpty()
    {
        global $methodCalled;
        global $methodArgs;

        $object = $this->buildObject();
        prepareMock('get_option', '*', function ($name, $default) { return array();});
        $this->assertEquals(array(), $object->reload()->toArray());

        $this->assertEquals(
            array(
                'get_option',
            ),
            $methodCalled
        );

        $this->assertEquals(
            array(
                array(OptionsBag::WORDPRESS_SETTINGS_NAME, null),
            ),
            $methodArgs
        );
    }

    public function testReload()
    {
        global $methodCalled;
        global $methodArgs;

        $object = $this->buildObject();
        prepareMock('get_option', '*', function ($name, $default) { return array('foo' => 'bar');});

        $this->assertEquals(array('foo' => 'bar'), $object->reload()->toArray());

        $this->assertEquals(
            array(
                'get_option',
            ),
            $methodCalled
        );

        $this->assertEquals(
            array(
                array(OptionsBag::WORDPRESS_SETTINGS_NAME, null),
            ),
            $methodArgs
        );
    }

    public function testSave()
    {
        global $methodCalled;
        global $methodArgs;

        $object = $this->buildObject();
        prepareMock('get_option', '*', function ($name, $default) { return array('foo' => 'bar');});

        $object->reload()->save();

        $this->assertEquals(
            array(
                'get_option',
                'update_option',
            ),
            $methodCalled
        );

        $this->assertEquals(
            array(
                array(OptionsBag::WORDPRESS_SETTINGS_NAME, null),
                array(OptionsBag::WORDPRESS_SETTINGS_NAME, array('foo' => 'bar')),
            ),
            $methodArgs
        );
    }

    public function testIsDefined()
    {
        $object = $this->buildObject();
        prepareMock('get_option', '*', function ($name, $default) { return array('foo' => 'bar');});

        $this->assertFalse($object->isDefined());
        $object->reload();
        $this->assertTrue($object->isDefined());
    }

    public function testOffsetExists()
    {
        $object = $this->buildObject();
        prepareMock('get_option', '*', function ($name, $default) { return array('foo' => 'bar');});

        $this->assertFalse($object->isDefined());
        $this->assertFalse($object->offsetExists(new \stdClass()));
        $this->assertFalse(isset($object['bar']));
        $this->assertTrue(isset($object['foo']));
        $this->assertTrue($object->isDefined());
    }

    public function testOffsetGet()
    {
        $object = $this->buildObject();
        prepareMock('get_option', '*', function ($name, $default) { return array('foo' => 'bar');});

        $this->assertFalse($object->isDefined());
        $this->assertFalse($object->offsetGet(new \stdClass()));
        $this->assertNull($object['bar']);
        $this->assertEquals('bar', $object['foo']);
        $this->assertTrue($object->isDefined());
    }

    public function testOffsetSet()
    {
        $object = $this->buildObject();
        prepareMock('get_option', '*', function ($name, $default) { return array('foo' => 'bar');});

        $this->assertFalse($object->isDefined());
        $object->offsetSet(new \stdClass(), 'foo');
        $object['bar'] = 'foo';
        $this->assertTrue($object->isDefined());
        $this->assertEquals(array('bar' => 'foo', 'foo' => 'bar'), $object->toArray());
    }

    public function testOffsetUnset()
    {
        $object = $this->buildObject();
        prepareMock('get_option', '*', function ($name, $default) { return array('foo' => 'bar');});

        $object->offsetUnset(new \stdClass(), 'foo');
        unset($object['foo']);
        $this->assertEquals(array(), $object->toArray());
    }

    public function testValidateError()
    {
        global $methodCalled;
        global $methodArgs;

        prepareMock('__', '*', function ($text, $tag) {return $text; });

        $object = $this->buildObject();
        $object->validate(
            array(
                Settings::CONSUMER_TOKEN => '',
                Settings::CONSUMER_SECRET => '',
                Settings::ACCESS_TOKEN => '',
                Settings::ACCESS_SECRET => '',
                Settings::SUBMIT_NOTIFICATION => '',
                Settings::OPPORTUNITY_CREATION => 'prospectOpportunity',
                Settings::DISPLAY_FORM_NAME => '',
            )
        );

        $this->assertEquals(
            array(
                '__',
                'add_settings_error',
                '__',
                'add_settings_error',
                '__',
                'add_settings_error',
                '__',
                'add_settings_error',
                '__',
                'add_settings_error',
                '__',
                'add_settings_error',
                'sanitize_text_field',
                '__',
                'add_settings_error',
            ),
            $methodCalled
        );

        $this->assertEquals(
            array(
                0 => array(
                        0 => 'The Consumer Token is missing or invalid, please check it.',
                        1 => 'wpsellsy',
                    ),
                1 => array(
                        0 => 'wpsellsy_options',
                        1 => 'consumerToken',
                        2 => 'The Consumer Token is missing or invalid, please check it.',
                        3 => 'error',
                    ),
                2 => array(
                        0 => 'The Consumer Secret is missing or invalid, please check it.',
                        1 => 'wpsellsy',
                    ),
                3 => array(
                        0 => 'wpsellsy_options',
                        1 => 'consumerSecret',
                        2 => 'The Consumer Secret is missing or invalid, please check it.',
                        3 => 'error',
                    ),
                4 => array(
                        0 => 'The User Token is missing or invalid, please check it',
                        1 => 'wpsellsy',
                    ),
                5 => array(
                        0 => 'wpsellsy_options',
                        1 => 'accessToken',
                        2 => 'The User Token is missing or invalid, please check it',
                        3 => 'error',
                    ),
                6 => array(
                        0 => 'The User Secret is missing or invalid, please check it.',
                        1 => 'wpsellsy',
                    ),
                7 => array(
                        0 => 'wpsellsy_options',
                        1 => 'accessSecret',
                        2 => 'The User Secret is missing or invalid, please check it.',
                        3 => 'error',
                    ),
                8 => array(
                        0 => 'Your email adress is missing or invalid, please check it to receive notifications. Prospects will be created anyway.',
                        1 => 'wpsellsy',
                    ),
                9 => array(
                        0 => 'wpsellsy_options',
                        1 => 'submitNotification',
                        2 => 'Your email adress is missing or invalid, please check it to receive notifications. Prospects will be created anyway.',
                        3 => 'error',
                    ),
                10 => array(
                        0 => 'You must select the source used to create opportunities.',
                        1 => 'wpsellsy',
                    ),
                11 => array(
                        0 => 'wpsellsy_options',
                        1 => 'opportunitySource',
                        2 => 'You must select the source used to create opportunities.',
                        3 => 'error',
                    ),
                12 => array(
                        0 => 'prospectOpportunity',
                    ),
                13 => array(
                        0 => 'You must input the form\'s title.',
                        1 => 'wpsellsy',
                    ),
                14 => array(
                        0 => 'wpsellsy_options',
                        1 => 'formName',
                        2 => 'You must input the form\'s title.',
                        3 => 'error',
                    ),
            ),
            $methodArgs
        );
    }

    public function testValidate()
    {
        global $methodCalled;
        global $methodArgs;

        prepareMock('__', '*', function ($text, $tag) {return $text; });
        prepareMock('is_email', '*', function ($text) {return true;});

        $object = $this->buildObject();
        $object->validate(
            array(
                Settings::CONSUMER_TOKEN => 'azertyuiopazertyuiopazertyuiopazertyuiop',
                Settings::CONSUMER_SECRET => 'azertyuiopazertyuiopazertyuiopazertyuiop',
                Settings::ACCESS_TOKEN => 'azertyuiopazertyuiopazertyuiopazertyuiop',
                Settings::ACCESS_SECRET => 'azertyuiopazertyuiopazertyuiopazertyuiop',
                Settings::SUBMIT_NOTIFICATION => 'contact@uni-alteri.com',
                Settings::OPPORTUNITY_CREATION => 'prospectOpportunity',
                Settings::DISPLAY_FORM_NAME => '1',
                Settings::OPPORTUNITY_SOURCE => 'fooBar',
                Settings::FORM_NAME => 'fooBar',
            )
        );

        $this->assertEquals(
            array(
                0 => 'sanitize_text_field',
                1 => 'sanitize_text_field',
                2 => 'sanitize_text_field',
                3 => 'sanitize_text_field',
                4 => 'is_email',
                5 => 'sanitize_text_field',
                6 => 'sanitize_text_field',
            ),
            $methodCalled
        );

        $this->assertEquals(
            array(
            0 => array(
                    0 => 'azertyuiopazertyuiopazertyuiopazertyuiop',
                ),
            1 => array(
                    0 => 'azertyuiopazertyuiopazertyuiopazertyuiop',
                ),
            2 => array(
                    0 => 'azertyuiopazertyuiopazertyuiopazertyuiop',
                ),
            3 => array(
                    0 => 'azertyuiopazertyuiopazertyuiopazertyuiop',
                ),
            4 => array(
                    0 => 'contact@uni-alteri.com',
                ),
            5 => array(
                    0 => 'contact@uni-alteri.com',
                ),
            6 => array(
                    0 => 'prospectOpportunity',
                ),
            ),
            $methodArgs
        );
    }

    public function testSanitize()
    {
        $values = array(
            'val1' => array(
                'val2' => '<p>\\"fooBar</p>',
            ),
            Settings::MESSAGE_SENT => '<p>test</p>',
            Settings::MESSAGE_ERROR => '<p>test</p>',
            Settings::FORM_CUSTOM_HEADER => '<p>test</p>',
            Settings::FORM_CUSTOM_FOOTER => '<p>test</p>',
            'val2' => '<p>test</p>',
        );

        prepareMock('apply_filters', '*', function ($filter, $values) {return $values; });
        $final = $this->buildObject()->sanitize($values);

        $this->assertEquals(
            array(
                'val1' => array(
                    'val2' => '"fooBar',
                ),
                Settings::MESSAGE_SENT => '<p>test</p>',
                Settings::MESSAGE_ERROR => '<p>test</p>',
                Settings::FORM_CUSTOM_HEADER => '<p>test</p>',
                Settings::FORM_CUSTOM_FOOTER => '<p>test</p>',
                'val2' => 'test',
            ),
            $final
        );
    }
}
