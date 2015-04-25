<?php

namespace UniAlteri\Tests\Sellsy\Wordpress;

use UniAlteri\Sellsy\Client\Client;
use UniAlteri\Sellsy\Wordpress\Form\Settings;
use UniAlteri\Sellsy\Wordpress\OptionsBag;
use UniAlteri\Sellsy\Wordpress\Plugin;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OptionsBag
     */
    protected $optionsMock;

    /**
     * @var Client
     */
    protected $sellsyClientMock;

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
     * return OptionsBag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildClientMock()
    {
        if (!$this->sellsyClientMock instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->sellsyClientMock = $this->getMock(
                'UniAlteri\Sellsy\Client\Client',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->sellsyClientMock;
    }

    /**
     * @return Plugin
     */
    protected function buildPlugin()
    {
        return new Plugin($this->buildClientMock(), $this->buildOptionsMock());
    }

    public function testCreation()
    {
        $this->buildClientMock()
            ->expects($this->once())
            ->method('setApiUrl')
            ->with($this->equalTo(SELLSY_WP_API_URL));

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->withConsecutive(
                array(Settings::ACCESS_TOKEN),
                array(Settings::ACCESS_SECRET),
                array(Settings::CONSUMER_TOKEN),
                array(Settings::CONSUMER_SECRET)
            )
            ->willReturnCallback(
                function ($name) {
                    $map = array(
                        Settings::ACCESS_TOKEN => 'a',
                        Settings::ACCESS_SECRET => 'b',
                        Settings::CONSUMER_TOKEN => 'c',
                        Settings::CONSUMER_SECRET => 'd'
                    );
                    return $map[$name];
                }
            );

        $this->buildClientMock()
            ->expects($this->once())
            ->method('setOAuthAccessToken')
            ->with($this->equalTo('a'));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('setOAuthAccessTokenSecret')
            ->with($this->equalTo('b'));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('setOAuthConsumerKey')
            ->with($this->equalTo('c'));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('setOAuthConsumerSecret')
            ->with($this->equalTo('d'));

        $this->buildPlugin();
    }

    public function testLoadTranslation()
    {
        $plugin = $this->buildPlugin();

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        $this->assertSame($plugin, $plugin->loadTranslation());
        $this->assertEquals(array('load_plugin_textdomain'), $methodCalled);
        $this->assertEquals(array(array('wpsellsy', true, SELLSY_WP_PATH_LANG)), $methodArgs);
    }

    public function testDisablePlugin()
    {
        $plugin = $this->buildPlugin();

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        $this->assertSame($plugin, $plugin->disablePlugin());
        $this->assertEquals(array('delete_option'), $methodCalled);
        $this->assertEquals(array(array(OptionsBag::WORDPRESS_SETTINGS_NAME)), $methodArgs);
    }

    public function testGetOptionsBag()
    {
        $plugin = $this->buildPlugin();
        $this->assertSame($this->buildOptionsMock(), $plugin->getOptionsBag());
    }

    public function testGetSellsyClient()
    {
        $plugin = $this->buildPlugin();
        $this->assertSame($this->buildClientMock(), $plugin->getSellsyClient());
    }

    public function testCheckSellsyCredentialsException()
    {
        $this->buildClientMock()
            ->expects($this->once())
            ->method('getInfos')
            ->willThrowException(new \Exception('Error'));

        $this->assertFalse($this->buildPlugin()->checkSellsyCredentials());
    }

    public function testCheckSellsyCredentialsNo()
    {
        $this->buildClientMock()
            ->expects($this->once())
            ->method('getInfos')
            ->willReturn(false);

        $this->assertFalse($this->buildPlugin()->checkSellsyCredentials());
    }

    public function testCheckSellsyCredentialsYes()
    {
        $this->buildClientMock()
            ->expects($this->once())
            ->method('getInfos')
            ->willReturn(true);

        $this->assertTrue($this->buildPlugin()->checkSellsyCredentials());
    }

    public function testListCustomFieldsError()
    {
        $this->buildClientMock()
            ->expects($this->once())
            ->method('customFields')
            ->willThrowException(new \Exception('Error'));

        $plugin = $this->buildPlugin();

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        $fieldsList = $plugin->listCustomFields();
        $fieldsList2 = $plugin->listCustomFields();
        $this->assertEquals(
            array (
                'thirdName',
                'thirdEmail',
                'thirdTel',
                'thirdMobile',
                'thirdWeb',
                'contactCivil',
                'contactName',
                'contactForename',
                'contactEmail',
                'contactTel',
                'contactMobile',
                'addressName',
                'addressPart1',
                'addressPart2',
                'addressZip',
                'addressTown',
                'addressCountrycode',
            ),
            array_keys($fieldsList)
        );

        $this->assertSame($fieldsList, $fieldsList2);
        foreach ($fieldsList as $code=>$field) {
            $this->assertInstanceOf('UniAlteri\Sellsy\Wordpress\Form\CustomField', $field);
            $this->assertEquals($code, $field->getCode());
        }

        $this->assertEquals(array('add_settings_error'), array_values(array_diff($methodCalled, array('__'))));
    }

    public function testListCustomFields()
    {
        $listCustomFieldMock = array(
            'response' => array(
                'result' => array(
                    array(
                        'id' => 1,
                        'type' => 'text',
                        'name' => 'field1',
                        'code' => 'field1',
                        'description' => 'desc',
                        'defaultValue' => 'def',
                        'prefsList' => null,
                        'isRequired' => 'Y'
                    ),
                    array(
                        'id' => 2,
                        'type' => 'text',
                        'name' => 'field2',
                        'code' => 'field2',
                        'description' => 'desc',
                        'defaultValue' => 'def',
                        'prefsList' => null,
                        'isRequired' => 'Y'
                    )
                )
            )
        );

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getList'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getList')
            ->with(
                $this->equalTo(
                    array(
                        'search' => array(
                            'useOn' => (array) 'prospect'
                        )
                    )
                )
            )
            ->willReturn(json_decode(json_encode($listCustomFieldMock)));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('customFields')
            ->willReturn($customFieldMock);

        $plugin = $this->buildPlugin();

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        $fieldsList = $plugin->listCustomFields();
        $fieldsList2 = $plugin->listCustomFields();
        $this->assertEquals(
            array (
                'thirdName',
                'thirdEmail',
                'thirdTel',
                'thirdMobile',
                'thirdWeb',
                'contactCivil',
                'contactName',
                'contactForename',
                'contactEmail',
                'contactTel',
                'contactMobile',
                'addressName',
                'addressPart1',
                'addressPart2',
                'addressZip',
                'addressTown',
                'addressCountrycode',
                'field1',
                'field2'
            ),
            array_keys($fieldsList)
        );

        $this->assertSame($fieldsList, $fieldsList2);
        foreach ($fieldsList as $code=>$field) {
            $this->assertInstanceOf('UniAlteri\Sellsy\Wordpress\Form\CustomField', $field);
            $this->assertEquals($code, $field->getCode());
        }

        $this->assertEquals(array(), array_values(array_diff($methodCalled, array('__'))));
    }

    public function testListRequiredCustomFields()
    {
        $listCustomFieldMock = array(
            'response' => array(
                'result' => array(
                    array(
                        'id' => 1,
                        'type' => 'text',
                        'name' => 'field1',
                        'code' => 'field1',
                        'description' => 'desc',
                        'defaultValue' => 'def',
                        'prefsList' => null,
                        'isRequired' => 'Y'
                    ),
                    array(
                        'id' => 2,
                        'type' => 'text',
                        'name' => 'field2',
                        'code' => 'field2',
                        'description' => 'desc',
                        'defaultValue' => 'def',
                        'prefsList' => null,
                        'isRequired' => 'Y'
                    )
                )
            )
        );

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getList'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getList')
            ->with(
                $this->equalTo(
                    array(
                        'search' => array(
                            'useOn' => (array) 'prospect'
                        )
                    )
                )
            )
            ->willReturn(json_decode(json_encode($listCustomFieldMock)));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('customFields')
            ->willReturn($customFieldMock);

        $plugin = $this->buildPlugin();

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        $fieldsList = $plugin->listRequiredCustomFields();
        $this->assertEquals(
            array (
                '1',
                '2'
            ),
            array_keys($fieldsList)
        );

        foreach ($fieldsList as $code=>$field) {
            $this->assertInstanceOf('UniAlteri\Sellsy\Wordpress\Form\CustomField', $field);
        }

        $this->assertEquals(array(), array_values(array_diff($methodCalled, array('__'))));
    }

    public function testCheckCUrlExtensions()
    {

    }
}