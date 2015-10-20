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

namespace UniAlteri\Tests\Sellsy\Wordpress\Form;

use UniAlteri\Sellsy\Wordpress\Form\Front;
use UniAlteri\Sellsy\Wordpress\Form\Settings;
use UniAlteri\Sellsy\Wordpress\OptionsBag;
use UniAlteri\Sellsy\Wordpress\Plugin;

class FrontTest extends \PHPUnit_Framework_TestCase
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
     * return OptionsBag|\PHPUnit_Framework_MockObject_MockObject.
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
     * @return Front
     */
    protected function buildObject()
    {
        return new Front($this->buildPluginMock(), $this->buildOptionsMock());
    }

    public function testAddCSSAdmin()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //admin
        prepareMock('is_admin', array(), true);

        $front->addCSS();

        $exceptedMethods = array(
            'is_admin',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testAddCSSNotAdmin()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('plugins_url', array('/css/wp_sellsy.css', SELLSY_WP_PATH_FILE), 'fooBar');

        $front->addCSS();

        $exceptedMethods = array(
            'is_admin',
            'plugins_url',
            'wp_register_style',
            'wp_enqueue_style',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array('/css/wp_sellsy.css', SELLSY_WP_PATH_FILE),
            array(
                'wpsellsystyles',
                'fooBar',
                array(),
                '1.0',
                'screen',
            ),
            array('wpsellsystyles'),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testValidateFormAdmin()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //admin
        prepareMock('is_admin', array(), true);

        $fields = array();
        $front->validateForm($fields, array(), 123);

        $exceptedMethods = array(
            'is_admin',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testValidateFormGoodNonceNotFormRequest()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);

        $fields = array();
        $front->validateForm(
            $fields,
            array(),
            123,
            array('slswp_nonce_verify_page' => 123)
        );

        $exceptedMethods = array(
            'is_admin',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testValidateFormNotNonceGoodFormRequest()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);

        $fields = array();
        $front->validateForm(
            $fields,
            array(),
            123,
            array('send_wp_sellsy' => 123)
        );

        $exceptedMethods = array(
            'is_admin',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testValidateFormGoodNonceGoodFormRequestNoFormId()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), true);

        $fields = array();
        $front->validateForm(
            $fields, array(),
            123,
            array(
                'send_wp_sellsy' => 321,
                'slswp_nonce_verify_page' => 123,
            )
        );

        $exceptedMethods = array(
            'is_admin',
            'wp_verify_nonce',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array(123, 'slswp_nonce_field'),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testValidateFormGoodNonceGoodFormRequestBadFormId()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), true);

        $fields = array();
        $front->validateForm(
            $fields,
            array(),
            123,
            array(
                'send_wp_sellsy' => 321,
                'slswp_nonce_verify_page' => 123,
                'formId' => 333,
            )
        );

        $exceptedMethods = array(
            'is_admin',
            'wp_verify_nonce',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array(123, 'slswp_nonce_field'),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testValidateFormGoodNonceGoodFormRequestNoProspectId()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), true);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createProspect')
            ->with(
                $this->equalTo(
                    array(
                        'field1' => 'foo',
                        'field2' => 'bar',
                    )
                )
            )
            ->willReturn('error');

        $fields = array_flip(array('field1', 'field2'));
        $this->assertEquals(
            'error',
            $front->validateForm(
                $fields,
                array(),
                123,
                array(
                    'send_wp_sellsy' => 321,
                    'slswp_nonce_verify_page' => 123,
                    'formId' => 123,
                    'field1' => 'foo',
                    'field3' => 'hello',
                    'field2' => 'bar',
                )
            )
        );

        $exceptedMethods = array(
            'is_admin',
            'wp_verify_nonce',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array(123, 'slswp_nonce_field'),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testValidateFormNoOpportunity()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), true);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createProspect')
            ->with(
                $this->equalTo(
                    array(
                        'field1' => 'foo',
                        'field2' => 'bar',
                    )
                )
            )
            ->willReturn(123);

        $fields = array_flip(array('field1', 'field2'));
        $this->assertTrue(
            $front->validateForm(
                $fields,
                array(),
                123,
                array(
                    'send_wp_sellsy' => 321,
                    'slswp_nonce_verify_page' => 123,
                    'formId' => 123,
                    'field1' => 'foo',
                    'field3' => 'hello',
                    'field2' => 'bar',
                )
            )
        );

        $exceptedMethods = array(
            'is_admin',
            'wp_verify_nonce',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array(123, 'slswp_nonce_field'),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testValidateFormOpportunityBadSource()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), true);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createProspect')
            ->with(
                $this->equalTo(
                    array(
                        'field1' => 'foo',
                        'field2' => 'bar',
                    )
                )
            )
            ->willReturn(123);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('getSourcesList')
            ->willReturn(array('source1', 'source2'));

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createOpportunity')
            ->with($this->equalTo(123), $this->equalTo('source1'));

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->with($this->equalTo(Settings::OPPORTUNITY_CREATION))
            ->willReturn('prospectOpportunity');

        $fields = array_flip(array('field1', 'field2'));
        $this->assertTrue(
            $front->validateForm(
                $fields,
                array('source' => 'source4'),
                123,
                array(
                    'send_wp_sellsy' => 321,
                    'slswp_nonce_verify_page' => 123,
                    'formId' => 123,
                    'field1' => 'foo',
                    'field3' => 'hello',
                    'field2' => 'bar',
                )
            )
        );

        $exceptedMethods = array(
            'is_admin',
            'wp_verify_nonce',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array(123, 'slswp_nonce_field'),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testValidateFormOpportunityNoSource()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), true);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createProspect')
            ->with(
                $this->equalTo(
                    array(
                        'field1' => 'foo',
                        'field2' => 'bar',
                    )
                )
            )
            ->willReturn(123);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('getSourcesList')
            ->willReturn(array('source1', 'source2'));

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createOpportunity')
            ->with($this->equalTo(123), $this->equalTo('source1'));

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->with($this->equalTo(Settings::OPPORTUNITY_CREATION))
            ->willReturn('prospectOpportunity');

        $fields = array_flip(array('field1', 'field2'));
        $this->assertTrue(
            $front->validateForm(
                $fields,
                array(),
                123,
                array(
                    'send_wp_sellsy' => 321,
                    'slswp_nonce_verify_page' => 123,
                    'formId' => 123,
                    'field1' => 'foo',
                    'field3' => 'hello',
                    'field2' => 'bar',
                )
            )
        );

        $exceptedMethods = array(
            'is_admin',
            'wp_verify_nonce',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array(123, 'slswp_nonce_field'),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testValidateFormOpportunity()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), true);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createProspect')
            ->with(
                $this->equalTo(
                    array(
                        'field1' => 'foo',
                        'field2' => 'bar',
                    )
                )
            )
            ->willReturn(123);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('getSourcesList')
            ->willReturn(array('source1', 'source2'));

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createOpportunity')
            ->with($this->equalTo(123), $this->equalTo('source2'));

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->with($this->equalTo(Settings::OPPORTUNITY_CREATION))
            ->willReturn('prospectOpportunity');

        $fields = array_flip(array('field1', 'field2'));
        $this->assertTrue(
            $front->validateForm(
                $fields,
                array('source' => 'source2'),
                123,
                array(
                    'send_wp_sellsy' => 321,
                    'slswp_nonce_verify_page' => 123,
                    'formId' => 123,
                    'field1' => 'foo',
                    'field3' => 'hello',
                    'field2' => 'bar',
                )
            )
        );

        $exceptedMethods = array(
            'is_admin',
            'wp_verify_nonce',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array(123, 'slswp_nonce_field'),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testValidateFormMailNoBody()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), true);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createProspect')
            ->with(
                $this->equalTo(
                    array(
                        'field1' => 'foo',
                        'field2' => 'bar',
                    )
                )
            )
            ->willReturn(123);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('getSourcesList')
            ->willReturn(array('source1', 'source2'));

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createOpportunity')
            ->with($this->equalTo(123), $this->equalTo('source2'));

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->withConsecutive(
                array(Settings::OPPORTUNITY_CREATION),
                array(Settings::SUBMIT_NOTIFICATION)
            )
            ->willReturn(true);

        $this->buildPluginMock()
            ->expects($this->never())
            ->method('sendMail');

        $fields = array_flip(array('field1', 'field2'));
        $this->assertTrue(
            $front->validateForm(
                $fields,
                array('source' => 'source2'),
                123,
                array(
                    'send_wp_sellsy' => 321,
                    'slswp_nonce_verify_page' => 123,
                    'formId' => 123,
                    'field1' => 'foo',
                    'field3' => 'hello',
                    'field2' => 'bar',
                )
            )
        );

        $exceptedMethods = array(
            'is_admin',
            'wp_verify_nonce',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array(123, 'slswp_nonce_field'),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testValidateFormMailBody()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), true);

        $bodyExcepted = 'fooBar';

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createProspect')
            ->with(
                $this->equalTo(
                    array(
                        'field1' => 'foo',
                        'field2' => 'bar',
                    )
                )
            )
            ->willReturnCallback(
                function ($values, &$body) use ($bodyExcepted) {
                    $body = $bodyExcepted;

                    return 123;
                }
            );

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('getSourcesList')
            ->willReturn(array('source1', 'source2'));

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createOpportunity')
            ->with($this->equalTo(123), $this->equalTo('source2'));

        $this->buildOptionsMock()
            ->expects($this->exactly(1))
            ->method('offsetExists')
            ->withConsecutive(
                array(Settings::SUBMIT_NOTIFICATION)
            )
            ->willReturn(true);

        $this->buildOptionsMock()
            ->expects($this->exactly(2))
            ->method('offsetGet')
            ->withConsecutive(
                array(Settings::OPPORTUNITY_CREATION),
                array(Settings::SUBMIT_NOTIFICATION)
            )
            ->willReturn(true);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('sendMail')
            ->with(
                $this->equalTo($bodyExcepted)
            );

        $fields = array_flip(array('field1', 'field2'));
        $this->assertTrue(
            $front->validateForm(
                $fields,
                array('source' => 'source2'),
                123,
                array(
                    'send_wp_sellsy' => 321,
                    'slswp_nonce_verify_page' => 123,
                    'formId' => 123,
                    'field1' => 'foo',
                    'field3' => 'hello',
                    'field2' => 'bar',
                )
            )
        );

        $exceptedMethods = array(
            'is_admin',
            'wp_verify_nonce',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array(123, 'slswp_nonce_field'),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testShortcodeNoFormId()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), false);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('listSelectedFields')
            ->willReturn(array('field1', 'field2'));

        $this->buildOptionsMock()
            ->expects($this->atLeastOnce())
            ->method('offsetGet');

        ob_start();
        $front->shortcode(array('source' => 'source2'));
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertNotFalse(strpos($result, 'formId" value="wpSellsyForm1'));
    }

    public function testShortcodeFormId()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), false);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('listSelectedFields')
            ->willReturn(array('field1', 'field2'));

        $this->buildOptionsMock()
            ->expects($this->atLeastOnce())
            ->method('offsetGet');

        ob_start();
        $front->shortcode(array('formId' => 'formName'));
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertNotFalse(strpos($result, 'formId" value="formName'));
    }

    public function testShortcodeOk()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), true);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('listSelectedFields')
            ->willReturn(array('field1', 'field2'));

        $this->buildOptionsMock()
            ->expects($this->atLeastOnce())
            ->method('offsetGet');

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createProspect')
            ->willReturn(123);

        $_POST = array('formId' => 'formName','send_wp_sellsy' => '123','slswp_nonce_verify_page' => 123);
        ob_start();
        $front->shortcode(array('formId' => 'formName'));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertNotEmpty($output);
    }

    public function testShortcodeNOk()
    {
        $front = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);
        prepareMock('wp_verify_nonce', array(123, 'slswp_nonce_field'), true);

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('listSelectedFields')
            ->willReturn(array('field1', 'field2'));

        $this->buildOptionsMock()
            ->expects($this->atLeastOnce())
            ->method('offsetGet');

        $this->buildPluginMock()
            ->expects($this->once())
            ->method('createProspect')
            ->willReturn(array('foo' => 'bar'));

        $_POST = array('formId' => 'formName','send_wp_sellsy' => '123','slswp_nonce_verify_page' => 123);
        ob_start();
        $front->shortcode(array('formId' => 'formName'));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertNotEmpty($output);
    }
}
