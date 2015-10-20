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
     * return OptionsBag|\PHPUnit_Framework_MockObject_MockObject.
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
                        Settings::CONSUMER_SECRET => 'd',
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

    public function testlistFieldsError()
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

        $fieldsList = $plugin->listFields();
        $fieldsList2 = $plugin->listFields();
        $this->assertEquals(
            array(
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
        foreach ($fieldsList as $code => $field) {
            $this->assertInstanceOf('UniAlteri\Sellsy\Wordpress\Form\Field', $field);
            $this->assertEquals($code, $field->getCode());
        }

        $this->assertEquals(array('add_settings_error'), array_values(array_diff($methodCalled, array('__'))));
    }

    public function testlistFields()
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
                        'isRequired' => 'Y',
                    ),
                    array(
                        'id' => 2,
                        'type' => 'text',
                        'name' => 'field2',
                        'code' => 'field2',
                        'description' => 'desc',
                        'defaultValue' => 'def',
                        'prefsList' => null,
                        'isRequired' => 'Y',
                    ),
                ),
            ),
        );

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getList'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getList')
            ->with(
                $this->equalTo(
                    array(
                        'search' => array(
                            'useOn' => (array) 'prospect',
                        ),
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

        $fieldsList = $plugin->listFields();
        $fieldsList2 = $plugin->listFields();
        $this->assertEquals(
            array(
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
                'field2',
            ),
            array_keys($fieldsList)
        );

        $this->assertSame($fieldsList, $fieldsList2);
        foreach ($fieldsList as $code => $field) {
            $this->assertInstanceOf('UniAlteri\Sellsy\Wordpress\Form\Field', $field);
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
                        'isRequired' => 'Y',
                    ),
                    array(
                        'id' => 2,
                        'type' => 'text',
                        'name' => 'field2',
                        'code' => 'field2',
                        'description' => 'desc',
                        'defaultValue' => 'def',
                        'prefsList' => null,
                        'isRequired' => 'Y',
                    ),
                ),
            ),
        );

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getList'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getList')
            ->with(
                $this->equalTo(
                    array(
                        'search' => array(
                            'useOn' => (array) 'prospect',
                        ),
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

        $fieldsList = $plugin->listRequiredCustomsFields();
        $this->assertEquals(
            array(
                '1',
                '2',
            ),
            array_keys($fieldsList)
        );

        foreach ($fieldsList as $code => $field) {
            $this->assertInstanceOf('UniAlteri\Sellsy\Wordpress\Form\Field', $field);
        }

        $this->assertEquals(array(), array_values(array_diff($methodCalled, array('__'))));
    }

    public function testListSelectedFieldsEmptyNoSelect()
    {
        $this->assertEquals(array(), $this->buildPlugin()->listSelectedFields());
    }

    public function testListSelectedFieldsEmptySelectCreationProspect()
    {
        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetExists')
            ->willReturnCallback(
                function ($name) {
                    if (Settings::OPPORTUNITY_CREATION == $name) {
                        return true;
                    }

                    if (Settings::FIELDS_SELECTED == $name) {
                        return true;
                    }

                    return false;
                }
            );

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    $map = array(
                        Settings::OPPORTUNITY_CREATION => 'prospectOnly',
                        Settings::FIELDS_SELECTED => array(),
                    );

                    if (isset($map[$name])) {
                        return $map[$name];
                    }

                    return;
                }
            );

        $this->assertEquals(array(), $this->buildPlugin()->listSelectedFields());
    }

    public function testListSelectedFieldsEmptySelectCreationOpp()
    {
        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetExists')
            ->willReturnCallback(
                function ($name) {
                    if (Settings::OPPORTUNITY_CREATION == $name) {
                        return true;
                    }

                    if (Settings::FIELDS_SELECTED == $name) {
                        return true;
                    }

                    return false;
                }
            );

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    $map = array(
                        Settings::OPPORTUNITY_CREATION => 'prospectOpportunity',
                        Settings::FIELDS_SELECTED => array(),
                    );

                    if (isset($map[$name])) {
                        return $map[$name];
                    }

                    return;
                }
            );

        $this->assertEquals(array(), $this->buildPlugin()->listSelectedFields());
    }

    public function testListSelectedFieldsSelectCreationProspect()
    {
        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetExists')
            ->willReturnCallback(
                function ($name) {
                    if (Settings::OPPORTUNITY_CREATION == $name) {
                        return true;
                    }

                    if (Settings::FIELDS_SELECTED == $name) {
                        return true;
                    }

                    return false;
                }
            );

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
                        'isRequired' => 'Y',
                    ),
                    array(
                        'id' => 2,
                        'type' => 'text',
                        'name' => 'field2',
                        'code' => 'field2',
                        'description' => 'desc',
                        'defaultValue' => 'def',
                        'prefsList' => null,
                        'isRequired' => 'Y',
                    ),
                ),
            ),
        );

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getList'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getList')
            ->with(
                $this->equalTo(
                    array(
                        'search' => array(
                            'useOn' => (array) 'prospect',
                        ),
                    )
                )
            )
            ->willReturn(json_decode(json_encode($listCustomFieldMock)));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('customFields')
            ->willReturn($customFieldMock);

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    $map = array(
                        Settings::OPPORTUNITY_CREATION => 'prospectOnly',
                        Settings::FIELDS_SELECTED => array('contactCivil', 'field1'),
                    );

                    if (isset($map[$name])) {
                        return $map[$name];
                    }

                    return;
                }
            );

        $fieldsList = $this->buildPlugin()->listSelectedFields();

        $this->assertEquals(
            array(
                'contactCivil',
                'field1',
            ),
            array_keys($fieldsList)
        );

        foreach ($fieldsList as $code => $field) {
            $this->assertInstanceOf('UniAlteri\Sellsy\Wordpress\Form\Field', $field);
            $this->assertEquals($code, $field->getCode());
        }
    }

    public function testListSelectedFieldsSelectCreationOpp()
    {
        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetExists')
            ->willReturnCallback(
                function ($name) {
                    if (Settings::OPPORTUNITY_CREATION == $name) {
                        return true;
                    }

                    if (Settings::FIELDS_SELECTED == $name) {
                        return true;
                    }

                    return false;
                }
            );

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
                        'isRequired' => 'Y',
                    ),
                    array(
                        'id' => 2,
                        'type' => 'text',
                        'name' => 'field2',
                        'code' => 'field2',
                        'description' => 'desc',
                        'defaultValue' => 'def',
                        'prefsList' => null,
                        'isRequired' => 'Y',
                    ),
                ),
            ),
        );

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getList'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getList')
            ->with(
                $this->equalTo(
                    array(
                        'search' => array(
                            'useOn' => (array) 'prospect',
                        ),
                    )
                )
            )
            ->willReturn(json_decode(json_encode($listCustomFieldMock)));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('customFields')
            ->willReturn($customFieldMock);

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    $map = array(
                        Settings::OPPORTUNITY_CREATION => 'prospectOpportunity',
                        Settings::FIELDS_SELECTED => array('contactCivil', 'field1'),
                    );

                    if (isset($map[$name])) {
                        return $map[$name];
                    }

                    return;
                }
            );

        $fieldsList = $this->buildPlugin()->listSelectedFields();

        $this->assertEquals(
            array(
                'contactCivil',
                'field1',
            ),
            array_keys($fieldsList)
        );

        foreach ($fieldsList as $code => $field) {
            $this->assertInstanceOf('UniAlteri\Sellsy\Wordpress\Form\Field', $field);
            $this->assertEquals($code, $field->getCode());
        }
    }

    public function testGetSourcesListEmpty()
    {
        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    $map = array(
                        Settings::OPPORTUNITY_SOURCE => '',
                    );

                    if (isset($map[$name])) {
                        return $map[$name];
                    }

                    return;
                }
            );

        $this->assertEquals(array(), $this->buildPlugin()->getSourcesList());
    }

    public function testGetSourcesList()
    {
        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    $map = array(
                        Settings::OPPORTUNITY_SOURCE => 'source1,source2',
                    );

                    if (isset($map[$name])) {
                        return $map[$name];
                    }

                    return;
                }
            );

        $this->assertEquals(array('source1', 'source2'), $this->buildPlugin()->getSourcesList());
    }

    public function testCheckOppListSources()
    {
        $sourceListMock = array(
            'response' => array(
                array(
                    'id' => 1,
                    'label' => 'label1',
                ),
                array(
                    'id' => 2,
                    'label' => 'label2',
                ),
            ),
        );

        $opportunitiesMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getSources'), array(), '', false);
        $opportunitiesMock->expects($this->any())
            ->method('getSources')
            ->willReturn(json_decode(json_encode($sourceListMock)));

        $this->buildClientMock()
            ->expects($this->any())
            ->method('opportunities')
            ->willReturn($opportunitiesMock);

        $this->assertEquals(
            array(
                'label1' => true,
                'label3' => false,
                'label2' => true,
            ),
            $this->buildPlugin()->checkOppListSources(
                array(
                    'label1',
                    'label3',
                    'label2',
                )
            )
        );
    }

    public function testCheckOppSourceException()
    {
        $opportunitiesMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getSources'), array(), '', false);
        $opportunitiesMock->expects($this->once())
            ->method('getSources')
            ->willThrowException(new \Exception('error'));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('opportunities')
            ->willReturn($opportunitiesMock);

        $this->assertFalse($this->buildPlugin()->checkOppSource('label2'));
    }

    public function testCheckOppSourceYes()
    {
        $sourceListMock = array(
            'response' => array(
                array(
                    'id' => 1,
                    'label' => 'label1',
                ),
                array(
                    'id' => 2,
                    'label' => 'label2',
                ),
            ),
        );

        $opportunitiesMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getSources'), array(), '', false);
        $opportunitiesMock->expects($this->once())
            ->method('getSources')
            ->willReturn(json_decode(json_encode($sourceListMock)));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('opportunities')
            ->willReturn($opportunitiesMock);

        $this->assertTrue($this->buildPlugin()->checkOppSource('label2'));
    }

    public function testCheckOppSourceNo()
    {
        $sourceListMock = array(
            'response' => array(
                array(
                    'id' => 1,
                    'label' => 'label1',
                ),
                array(
                    'id' => 2,
                    'label' => 'label2',
                ),
            ),
        );

        $opportunitiesMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getSources'), array(), '', false);
        $opportunitiesMock->expects($this->once())
            ->method('getSources')
            ->willReturn(json_decode(json_encode($sourceListMock)));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('opportunities')
            ->willReturn($opportunitiesMock);

        $this->assertFalse($this->buildPlugin()->checkOppSource('label3'));
    }

    public function testCreateOppSourceException()
    {
        $_POST = array('nonce' => 'nonceValue');

        $plugin = $this->buildPlugin();
        prepareMock('wp_verify_nonce', array('nonceValue', 'slswp_ajax_nonce'), false);

        global $methodCalled;
        $methodCalled = array();

        $plugin->createOppSource();

        $this->assertEquals(array('wp_verify_nonce', '__', 'wp_die'), $methodCalled);
    }

    public function testCreateOppSourceInvalid()
    {
        $_POST = array('nonce' => 'nonceValue');

        $plugin = $this->buildPlugin();
        prepareMock('wp_verify_nonce', array('nonceValue', 'slswp_ajax_nonce'), true);

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        $plugin->createOppSource();

        $this->assertEquals(array('wp_verify_nonce', 'wp_die'), $methodCalled);
        $this->assertEquals(
            array(
                array('nonceValue', 'slswp_ajax_nonce'),
                array('false'),
            ),
            $methodArgs
        );
    }

    public function testCreateOppSourceNoForm()
    {
        $_POST = array(
            'nonce' => 'nonceValue',
            'action' => 'sls_createOppSource',
            'param' => 'fooBar',
        );

        $plugin = $this->buildPlugin();
        prepareMock('wp_verify_nonce', array('nonceValue', 'slswp_ajax_nonce'), true);

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        $plugin->createOppSource();

        $this->assertEquals(array('wp_verify_nonce', 'wp_die'), $methodCalled);
        $this->assertEquals(
            array(
                array('nonceValue', 'slswp_ajax_nonce'),
                array('false'),
            ),
            $methodArgs
        );
    }

    public function testCreateOppSourceReturnFalse()
    {
        $_POST = array(
            'nonce' => 'nonceValue',
            'action' => 'sls_createOppSource',
            'param' => 'creerSource',
            'source' => 'source1',
        );

        $sourceListMock = array(
            'response' => false,
        );

        $opportunitiesMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('createSource'), array(), '', false);
        $opportunitiesMock->expects($this->any())
            ->method('createSource')
            ->with(
                $this->equalTo(
                    array('source' => array('label' => 'source1'))
                )
            )
            ->willReturn(json_decode(json_encode($sourceListMock)));

        $this->buildClientMock()
            ->expects($this->any())
            ->method('opportunities')
            ->willReturn($opportunitiesMock);

        $plugin = $this->buildPlugin();
        prepareMock('wp_verify_nonce', array('nonceValue', 'slswp_ajax_nonce'), true);

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        $plugin->createOppSource();

        $this->assertEquals(array('wp_verify_nonce', 'wp_die'), $methodCalled);
        $this->assertEquals(
            array(
                array('nonceValue', 'slswp_ajax_nonce'),
                array('false'),
            ),
            $methodArgs
        );
    }

    public function testCreateOppSourceReturnTrue()
    {
        $_POST = array(
            'nonce' => 'nonceValue',
            'action' => 'sls_createOppSource',
            'param' => 'creerSource',
            'source' => 'source1',
        );

        $sourceListMock = array(
            'response' => true,
        );

        $opportunitiesMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('createSource'), array(), '', false);
        $opportunitiesMock->expects($this->any())
            ->method('createSource')
            ->with(
                $this->equalTo(
                    array('source' => array('label' => 'source1'))
                )
            )
            ->willReturn(json_decode(json_encode($sourceListMock)));

        $this->buildClientMock()
            ->expects($this->any())
            ->method('opportunities')
            ->willReturn($opportunitiesMock);

        $plugin = $this->buildPlugin();
        prepareMock('wp_verify_nonce', array('nonceValue', 'slswp_ajax_nonce'), true);

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        $plugin->createOppSource();

        $this->assertEquals(array('wp_verify_nonce', 'wp_die'), $methodCalled);
        $this->assertEquals(
            array(
                array('nonceValue', 'slswp_ajax_nonce'),
                array('true'),
            ),
            $methodArgs
        );
    }

    public function testCreateOppSourceReturnException()
    {
        $_POST = array(
            'nonce' => 'nonceValue',
            'action' => 'sls_createOppSource',
            'param' => 'creerSource',
            'source' => 'source1',
        );

        $sourceListMock = array(
            'response' => true,
        );

        $opportunitiesMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('createSource'), array(), '', false);
        $opportunitiesMock->expects($this->any())
            ->method('createSource')
            ->willThrowException(new \Exception('message'));

        $this->buildClientMock()
            ->expects($this->any())
            ->method('opportunities')
            ->willReturn($opportunitiesMock);

        $plugin = $this->buildPlugin();
        prepareMock('wp_verify_nonce', array('nonceValue', 'slswp_ajax_nonce'), true);

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        $plugin->createOppSource();

        $this->assertEquals(array('wp_verify_nonce', 'wp_die'), $methodCalled);
        $this->assertEquals(
            array(
                array('nonceValue', 'slswp_ajax_nonce'),
                array('false'),
            ),
            $methodArgs
        );
    }

    public function testCreateProspect()
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
                        'isRequired' => 'Y',
                    ),
                    array(
                        'id' => 2,
                        'type' => 'text',
                        'name' => 'field2',
                        'code' => 'field2',
                        'description' => 'desc',
                        'defaultValue' => 'def',
                        'prefsList' => null,
                        'isRequired' => 'Y',
                    ),
                ),
            ),
        );

        $prospectMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('create'), array(), '', false);
        $prospectMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo(
                    array(
                        'third' => array(
                            'name' => 'fooBar',
                        ),
                        'contact' => array(
                            'name' => 'fooBar',
                        ),
                        'address' => array(
                            'part1' => 'street address',
                        ),
                    )
                )
            )
            ->willReturn(json_decode(json_encode(array('response' => 345))));

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getList', 'recordValues'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getList')
            ->with(
                $this->equalTo(
                    array(
                        'search' => array(
                            'useOn' => (array) 'prospect',
                        ),
                    )
                )
            )
            ->willReturn(json_decode(json_encode($listCustomFieldMock)));

        $customFieldMock->expects($this->never())
            ->method('recordValues');

        $this->buildClientMock()
            ->expects($this->atLeastOnce())
            ->method('customFields')
            ->willReturn($customFieldMock);

        $this->buildClientMock()
            ->expects($this->once())
            ->method('prospects')
            ->willReturn($prospectMock);

        $plugin = $this->buildPlugin();

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    if (Settings::MANDATORIES_FIELDS == $name) {
                        return array('contactName', 'field1');
                    }

                    if (Settings::FIELDS_SELECTED == $name) {
                        return array('contactName', 'field1', 'contactEmail');
                    }
                }
            );

        $formValues = array(
            'contactName' => 'fooBar',
            'addressPart1' => 'street address',
        );

        prepareMock('sanitize_text_field', '*', function ($args) { return $args;});

        $this->assertEquals(345, $plugin->createProspect($formValues, $bodyOutput));
    }

    public function testCreateProspectNonValid()
    {
        $listCustomFieldMock = array('response' => array('result' => array()));

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getList', 'recordValues'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getList')
            ->with(
                $this->equalTo(
                    array(
                        'search' => array(
                            'useOn' => (array) 'prospect',
                        ),
                    )
                )
            )
            ->willReturn(json_decode(json_encode($listCustomFieldMock)));

        $customFieldMock->expects($this->never())
            ->method('recordValues');

        $this->buildClientMock()
            ->expects($this->once())
            ->method('customFields')
            ->willReturn($customFieldMock);

        $this->buildClientMock()
            ->expects($this->never())
            ->method('prospects');

        $plugin = $this->buildPlugin();

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    if (Settings::MANDATORIES_FIELDS == $name) {
                        return array();
                    }

                    if (Settings::FIELDS_SELECTED == $name) {
                        return array('contactEmail');
                    }
                }
            );

        $formValues = array(
            'contactEmail' => 'fooBar',
        );

        prepareMock('sanitize_text_field', '*', function ($args) { return $args;});

        $this->assertEquals(array('contactEmail' => ''), $plugin->createProspect($formValues, $bodyOutput));
    }

    public function testCreateProspectException()
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
                        'isRequired' => 'Y',
                    ),
                    array(
                        'id' => 2,
                        'type' => 'text',
                        'name' => 'field2',
                        'code' => 'field2',
                        'description' => 'desc',
                        'defaultValue' => 'def',
                        'prefsList' => null,
                        'isRequired' => 'Y',
                    ),
                ),
            ),
        );

        $prospectMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('create'), array(), '', false);
        $prospectMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo(
                    array(
                        'third' => array(
                            'name' => 'fooBar',
                        ),
                        'contact' => array(
                            'name' => 'fooBar',
                        ),
                        'address' => array(
                            'part1' => 'street address',
                        ),
                    )
                )
            )
            ->willThrowException(new \Exception('message'));

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getList', 'recordValues'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getList')
            ->with(
                $this->equalTo(
                    array(
                        'search' => array(
                            'useOn' => (array) 'prospect',
                        ),
                    )
                )
            )
            ->willReturn(json_decode(json_encode($listCustomFieldMock)));

        $customFieldMock->expects($this->never())
            ->method('recordValues');

        $this->buildClientMock()
            ->expects($this->atLeastOnce())
            ->method('customFields')
            ->willReturn($customFieldMock);

        $this->buildClientMock()
            ->expects($this->once())
            ->method('prospects')
            ->willReturn($prospectMock);

        $plugin = $this->buildPlugin();

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    if (Settings::MANDATORIES_FIELDS == $name) {
                        return array('contactName', 'field1');
                    }

                    if (Settings::FIELDS_SELECTED == $name) {
                        return array('contactName', 'field1', 'contactEmail');
                    }
                }
            );

        $formValues = array(
            'contactName' => 'fooBar',
            'addressPart1' => 'street address',
        );

        prepareMock('sanitize_text_field', '*', function ($args) { return $args;});

        $this->assertEquals(array('message'), $plugin->createProspect($formValues, $bodyOutput));
    }

    public function testCreateProspectRunTimeException()
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
                        'isRequired' => 'Y',
                    ),
                    array(
                        'id' => 2,
                        'type' => 'text',
                        'name' => 'field2',
                        'code' => 'field2',
                        'description' => 'desc',
                        'defaultValue' => 'def',
                        'prefsList' => null,
                        'isRequired' => 'Y',
                    ),
                ),
            ),
        );

        $prospectMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('create'), array(), '', false);
        $prospectMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo(
                    array(
                        'third' => array(
                            'name' => 'fooBar',
                        ),
                        'contact' => array(
                            'name' => 'fooBar',
                        ),
                        'address' => array(
                            'part1' => 'street address',
                        ),
                    )
                )
            )
            ->willThrowException(new \RuntimeException('message'));

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getList', 'recordValues'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getList')
            ->with(
                $this->equalTo(
                    array(
                        'search' => array(
                            'useOn' => (array) 'prospect',
                        ),
                    )
                )
            )
            ->willReturn(json_decode(json_encode($listCustomFieldMock)));

        $customFieldMock->expects($this->never())
            ->method('recordValues');

        $this->buildClientMock()
            ->expects($this->atLeastOnce())
            ->method('customFields')
            ->willReturn($customFieldMock);

        $this->buildClientMock()
            ->expects($this->once())
            ->method('prospects')
            ->willReturn($prospectMock);

        $plugin = $this->buildPlugin();

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    if (Settings::MANDATORIES_FIELDS == $name) {
                        return array('contactName', 'field1');
                    }

                    if (Settings::FIELDS_SELECTED == $name) {
                        return array('contactName', 'field1', 'contactEmail');
                    }
                }
            );

        $formValues = array(
            'contactName' => 'fooBar',
            'addressPart1' => 'street address',
        );

        prepareMock('sanitize_text_field', '*', function ($args) { return $args;});

        $this->assertEquals(array('message'), $plugin->createProspect($formValues, $bodyOutput));
    }

    public function testCreateProspectCustom()
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
                        'isRequired' => 'Y',
                    ),
                    array(
                        'id' => 2,
                        'type' => 'text',
                        'name' => 'field2',
                        'code' => 'field2',
                        'description' => 'desc',
                        'defaultValue' => 'def',
                        'prefsList' => null,
                        'isRequired' => 'Y',
                    ),
                    array(
                        'id' => 3,
                        'type' => 'boolean',
                        'name' => 'field3',
                        'code' => 'field3',
                        'description' => 'desc',
                        'defaultValue' => 'N',
                        'prefsList' => null,
                        'isRequired' => 'Y',
                    ),
                ),
            ),
        );

        $prospectMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('create'), array(), '', false);
        $prospectMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo(
                    array(
                        'third' => array(
                            'name' => 'fooBar',
                        ),
                        'contact' => array(
                            'name' => 'fooBar',
                        ),
                        'address' => array(
                            'part1' => 'street address',
                        ),
                    )
                )
            )
            ->willReturn(json_decode(json_encode(array('response' => 345))));

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getList', 'recordValues'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getList')
            ->with(
                $this->equalTo(
                    array(
                        'search' => array(
                            'useOn' => (array) 'prospect',
                        ),
                    )
                )
            )
            ->willReturn(json_decode(json_encode($listCustomFieldMock)));

        $customFieldMock->expects($this->once())
            ->method('recordValues')
            ->willReturnCallback(
                function ($args) {
                    $this->assertEquals(
                        array(
                            'linkedtype' => 'prospect',
                            'linkedid' => 345,
                            'values' => array(
                                array(
                                    'cfid' => 3,
                                    'value' => 'N',
                                ),
                                array(
                                    'cfid' => 1,
                                    'value' => 'def',
                                ),
                                array(
                                    'cfid' => 2,
                                    'value' => 'def',
                                ),
                            ),
                        ),
                        $args
                    );
                }
            );

        $this->buildClientMock()
            ->expects($this->atLeastOnce())
            ->method('customFields')
            ->willReturn($customFieldMock);

        $this->buildClientMock()
            ->expects($this->once())
            ->method('prospects')
            ->willReturn($prospectMock);

        $plugin = $this->buildPlugin();

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    if (Settings::MANDATORIES_FIELDS == $name) {
                        return array('contactName', 'field1');
                    }

                    if (Settings::FIELDS_SELECTED == $name) {
                        return array('contactName', 'field1', 'contactEmail', 'field3');
                    }
                }
            );

        $formValues = array(
            'contactName' => 'fooBar',
            'addressPart1' => 'street address',
            'field3' => 'foo',
        );

        prepareMock('sanitize_text_field', '*', function ($args) { return $args;});

        $this->assertEquals(345, $plugin->createProspect($formValues, $bodyOutput));
    }

    public function testGetOpportunityCurrentIdent()
    {
        $listCustomFieldMock = array(
            'response' => 123,
        );

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getCurrentIdent'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getCurrentIdent')->willReturn(json_decode(json_encode($listCustomFieldMock)));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('opportunities')
            ->willReturn($customFieldMock);

        $this->assertEquals(123, $this->buildPlugin()->getOpportunityCurrentIdent());
    }

    public function testGetFunnelIdDefaultAsObject()
    {
        $listCustomFieldMock = array(
            'response' => array(
                array(
                    'id' => 456,
                    'name' => 'Bar',
                ),
                array(
                    'id' => 876,
                    'name' => 'foo',
                ),
                array(
                    'name' => 'default',
                    'id' => 123,
                ),
            ),
        );

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getFunnels'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getFunnels')->willReturn(json_decode(json_encode($listCustomFieldMock)));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('opportunities')
            ->willReturn($customFieldMock);

        $this->assertEquals(123, $this->buildPlugin()->getFunnelId());
    }

    public function testGetFunnelIdDefaultAsString()
    {
        $listCustomFieldMock = array(
            'response' => array(
                'bar' => 456,
                'foo' => 876,
                'defaultFunnel' => 123,
            ),
        );

        $customFieldMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getFunnels'), array(), '', false);
        $customFieldMock->expects($this->once())
            ->method('getFunnels')->willReturn(json_decode(json_encode($listCustomFieldMock)));

        $this->buildClientMock()
            ->expects($this->once())
            ->method('opportunities')
            ->willReturn($customFieldMock);

        $this->assertEquals(123, $this->buildPlugin()->getFunnelId());
    }

    public function testGetStepIdNull()
    {
        $this->assertNull($this->buildPlugin()->getStepId(null));
    }

    public function testGetStepId()
    {
        $opportunitiesMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getStepsForFunnel'), array(), '', false);
        $opportunitiesMock->expects($this->once())
            ->method('getStepsForFunnel')
            ->with(
                $this->equalTo(
                    array(
                        'funnelid' => 123,
                    )
                )
            )
            ->willReturn(
                json_decode(
                    json_encode(
                        array(
                            'response' => array(
                                array(
                                    'id' => 456,
                                ),
                                array(
                                    'id' => 678,
                                ),
                            ),
                        )
                    )
                )
            );

        $this->buildClientMock()
            ->expects($this->atLeastOnce())
            ->method('opportunities')
            ->willReturn($opportunitiesMock);

        $this->assertEquals(456, $this->buildPlugin()->getStepId(123));
    }

    public function testGetSourceId()
    {
        $opportunitiesMock = $this->getMock('UniAlteri\Sellsy\Client\Collection\Collection', array('getSources'), array(), '', false);
        $opportunitiesMock->expects($this->once())
            ->method('getSources')
            ->willReturn(
                json_decode(
                    json_encode(
                        array(
                            'response' => array(
                                array(
                                    'label' => '',
                                    'id' => 456,
                                ),
                                array(
                                    'label' => 'source1',
                                    'id' => 678,
                                ),
                                array(
                                    'label' => 'source2',
                                    'id' => 345,
                                ),
                                array(
                                    'label' => 'source3',
                                    'id' => 897,
                                ),
                            ),
                        )
                    )
                )
            );

        $this->buildClientMock()
            ->expects($this->atLeastOnce())
            ->method('opportunities')
            ->willReturn($opportunitiesMock);

        $this->assertEquals(345, $this->buildPlugin()->getSourceId('source2'));
    }

    public function testCreateOpportunity()
    {
        $opportunitiesMock = $this->getMock(
            'UniAlteri\Sellsy\Client\Collection\Collection',
            array('getCurrentIdent', 'getFunnels', 'getStepsForFunnel', 'getSources', 'create'),
            array(),
            '',
            false
        );
        $opportunitiesMock->expects($this->once())
            ->method('getCurrentIdent')->willReturn(
                json_decode(
                    json_encode(
                        array(
                            'response' => 123,
                        )
                    )
                )
            );

        $opportunitiesMock->expects($this->once())
            ->method('getFunnels')
            ->willReturn(
                json_decode(
                    json_encode(
                        array(
                            'response' => array(
                                'bar' => 456,
                                'foo' => 876,
                                'defaultFunnel' => 12356,
                            ),
                        )
                    )
                )
            );

        $opportunitiesMock->expects($this->once())
            ->method('getStepsForFunnel')
            ->with(
                $this->equalTo(
                    array(
                        'funnelid' => 12356,
                    )
                )
            )
            ->willReturn(
                json_decode(
                    json_encode(
                        array(
                            'response' => array(
                                array(
                                    'id' => 456,
                                ),
                                array(
                                    'id' => 678,
                                ),
                            ),
                        )
                    )
                )
            );

        $opportunitiesMock->expects($this->once())
            ->method('getSources')
            ->willReturn(
                json_decode(
                    json_encode(
                        array(
                            'response' => array(
                                array(
                                    'label' => '',
                                    'id' => 456,
                                ),
                                array(
                                    'label' => 'source1',
                                    'id' => 678,
                                ),
                                array(
                                    'label' => 'source2',
                                    'id' => 345,
                                ),
                                array(
                                    'label' => 'source3',
                                    'id' => 897,
                                ),
                            ),
                        )
                    )
                )
            );

        $opportunitiesMock->expects($this->once())
            ->method('create')
            ->willReturnCallback(
                function ($args) {
                    unset($args['opportunity']['dueDate']);

                    $this->assertEquals(
                        array(
                            'opportunity' => array(
                                'linkedtype' => 'prospect',
                                'linkedid' => 1000,
                                'ident' => 123,
                                'sourceid' => 345,
                                'name' => __('Contact from', 'wpsellsy').' source2',
                                'funnelid' => 12356,
                                'stepid' => 456,
                                'brief' => 'Note',
                            ),
                        ),
                        $args
                    );

                    return json_decode(
                        json_encode(
                            array('response' => 45600)
                        )
                    );
                }
            );

        $this->buildClientMock()
            ->expects($this->atLeastOnce())
            ->method('opportunities')
            ->willReturn($opportunitiesMock);

        $this->assertEquals(
            45600,
            $this->buildPlugin()->createOpportunity(
                1000,
                'source2',
                'Note'
            )
        );
    }

    public function testSendMail()
    {
        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    $map = array(
                        Settings::SUBMIT_NOTIFICATION => 'notification@email',
                        Settings::FORM_NAME => 'form name',
                    );

                    if (isset($map[$name])) {
                        return $map[$name];
                    }

                    return '';
                }
            );

        $bodyValue = 'fooBar';
        $_SERVER['SERVER_NAME'] = 'www.foo.bar';
        \PHPMailer::$Result = true;
        $this->assertTrue($this->buildPlugin()->sendMail($bodyValue));
        $this->assertEquals('sellsy-form@foo.bar', \PHPMailer::$From);
        $this->assertEquals(array('notification@email'), \PHPMailer::$Addresses);
        $this->assertEquals($bodyValue, \PHPMailer::$Msg);
    }

    public function testSendMailEmpty()
    {
        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    $map = array(
                        Settings::SUBMIT_NOTIFICATION => 'notification@email',
                        Settings::FORM_NAME => 'form name',
                    );

                    if (isset($map[$name])) {
                        return $map[$name];
                    }

                    return '';
                }
            );

        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetExists')
            ->willReturn(true);

        $bodyValue = 'fooBar';
        $_SERVER['SERVER_NAME'] = 'www.foo.bar';
        \PHPMailer::$Result = true;
        $this->assertTrue($this->buildPlugin()->sendMail($bodyValue));
        $this->assertEquals('sellsy-form@foo.bar', \PHPMailer::$From);
        $this->assertEquals(array('notification@email'), \PHPMailer::$Addresses);
        $this->assertEquals($bodyValue, \PHPMailer::$Msg);
    }

    public function testSendMailError()
    {
        $this->buildOptionsMock()
            ->expects($this->any())
            ->method('offsetGet')
            ->willReturnCallback(
                function ($name) {
                    $map = array(
                        Settings::SUBMIT_NOTIFICATION => 'notification@email',
                        Settings::FORM_NAME => 'form name',
                    );

                    if (isset($map[$name])) {
                        return $map[$name];
                    }

                    return '';
                }
            );

        $bodyValue = 'fooBar';
        $_SERVER['SERVER_NAME'] = 'www.foo.bar';
        \PHPMailer::$Result = false;
        $this->assertFalse($this->buildPlugin()->sendMail($bodyValue));
        $this->assertEquals('sellsy-form@foo.bar', \PHPMailer::$From);
        $this->assertEquals(array('notification@email'), \PHPMailer::$Addresses);
        $this->assertEquals($bodyValue, \PHPMailer::$Msg);
    }

    public function testCheckCUrlExtensions()
    {
        $this->assertEquals(in_array('curl', get_loaded_extensions()), $this->buildPlugin()->checkCUrlExtensions());
    }
}
