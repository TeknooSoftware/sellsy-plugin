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

use UniAlteri\Sellsy\Wordpress\Form\Admin;
use UniAlteri\Sellsy\Wordpress\OptionsBag;
use UniAlteri\Sellsy\Wordpress\Plugin;

class AdminTest extends \PHPUnit_Framework_TestCase
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
     * @return Admin
     */
    protected function buildObject()
    {
        return new Admin($this->buildPluginMock(), $this->buildOptionsMock());
    }

    public function testAddJSAdminNotAdmin()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);

        $admin->addJS();

        $exceptedMethods = array(
            'is_admin',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testAddJSAdmin()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //admin
        prepareMock('is_admin', array(), true);

        $admin->addJS();

        $exceptedMethods = array(
            'is_admin',
            'plugins_url',
            'wp_enqueue_script',
            'plugins_url',
            'wp_enqueue_script',
            'plugins_url',
            'wp_enqueue_script',
            'admin_url',
            'wp_create_nonce',
            'wp_localize_script',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array(
                '/js/jquery-ui.min.js',
                SELLSY_WP_PATH.'/sellsy-plugin.php',
            ),
            array(
                'jqueryui',
                null,
                array(
                    'jquery',
                ),
                '1.0',
                1,
            ),
            array(
                '/js/ui.multiselect.js',
                SELLSY_WP_PATH.'/sellsy-plugin.php',
            ),
            array(
                'uimultiselect',
                null,
                array(
                    'jquery',
                    'jqueryui',
                ),
                '1.0',
                1,
            ),
            array(
                '/js/wp_sellsy.js',
                SELLSY_WP_PATH.'/sellsy-plugin.php',
            ),
            array(
                'wpsellsyjscsource',
                null,
                array(
                    0 => 'jquery',
                    1 => 'uimultiselect',
                ),
                '1.0',
                1,
            ),
            array(
                'admin-ajax.php',
            ),
            array(
                'slswp_ajax_nonce',
            ),
            array(
                'wpsellsyjscsource',
                'ajax_var',
                array(
                    'url' => null,
                    'nonce' => null,
                ),
            ),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testAddCSSNotAdmin()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);

        $admin->addCSS('hook');

        $exceptedMethods = array(
            'is_admin',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testAddCSSAdminBadHook()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), true);

        $admin->addCSS('hook');

        $exceptedMethods = array(
            'is_admin',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testAddCSSAdminGoodHook()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //admin
        prepareMock('is_admin', array(), true);

        $admin->addCSS('toplevel_page_slswp-admPage');

        $exceptedMethods = array(
            'is_admin',
            'plugins_url',
            'wp_register_style',
            'wp_enqueue_style',
            'plugins_url',
            'wp_register_style',
            'wp_enqueue_style',
            'plugins_url',
            'wp_register_style',
            'wp_enqueue_style',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array(
                '/css/wp_sellsy_admin.css',
                SELLSY_WP_PATH.'/sellsy-plugin.php',
            ),
            array(
                'wpsellsystylesadmin',
                null,
                array(),
                '1.0',
                'screen',
            ),
            array(
                'wpsellsystylesadmin',
            ),
            array(
                '/css/jquery-ui.min.css',
                SELLSY_WP_PATH.'/sellsy-plugin.php',
            ),
            array(
                'jqueryuicss',
                null,
                array(),
                '1.0',
                'screen',
            ),
            array(
                'jqueryuicss',
            ),
            array(
                '/css/ui.multiselect.css',
                SELLSY_WP_PATH.'/sellsy-plugin.php',
            ),
            array(
                'multiselect',
                null,
                array(
                    'jqueryuicss',
                ),
                '1.0',
                'screen',
            ),
            array(
                'multiselect',
            ),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testAddMenuNotAdmin()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);

        $admin->addMenu();

        $exceptedMethods = array(
            'is_admin',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testAddMenuAdmin()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //admin
        prepareMock('is_admin', array(), true);
        //plugin
        prepareMock('plugins_url', array('/img/sellsy_15.png', SELLSY_WP_PATH_FILE), 'fooBar');

        $admin->addMenu();

        $exceptedMethods = array(
            'is_admin',
            'plugins_url',
            'add_menu_page',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array(
                '/img/sellsy_15.png',
                SELLSY_WP_PATH_FILE,
            ),
            array(
                'WP Sellsy',
                'WP Sellsy',
                'manage_options',
                'slswp-admPage',
                array($admin, 'page'),
                'fooBar',
            ),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testPageNotAdmin()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);

        ob_start();
        $admin->page();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEmpty($content);

        $exceptedMethods = array(
            'is_admin',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testPageAdminNotRight()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //admin
        prepareMock('is_admin', array(), true);
        prepareMock('current_user_can', array('manage_options'), false);

        ob_start();
        $admin->page();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEmpty($content);

        $exceptedMethods = array(
            'is_admin',
            'current_user_can',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array('manage_options'),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testPageAdminRight()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //admin
        prepareMock('is_admin', array(), true);
        prepareMock('current_user_can', array('manage_options'), true);

        ob_start();
        $admin->page();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertNotEmpty($content);

        $exceptedMethods = array(
            'is_admin',
            'current_user_can',
        );
        array_splice($methodCalled, 2);
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
            array('manage_options'),
        );
        array_splice($methodArgs, 2);
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testDisplaySettingsNotAdmin()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), false);

        ob_start();
        $admin->displaySettings(array());
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEmpty($content);

        $exceptedMethods = array(
            'is_admin',
        );
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testDisplaySettingsAdmin()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), true);

        ob_start();
        $admin->displaySettings(
            array(
                'id' => 'idVal',
                'class' => 'classVal',
                'type' => 'typeVal',
                'std' => 'stdVal',
                'desc' => 'descVal',
            )
        );
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertNotEmpty($content);

        $exceptedMethods = array(
            'is_admin',
        );
        array_splice($methodCalled, 1);
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
        );
        array_splice($methodArgs, 1);
        $this->assertEquals($exceptedArgs, $methodArgs);
    }

    public function testDisplaySettingsAdminCheckbox()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        //No admin
        prepareMock('is_admin', array(), true);

        ob_start();
        $admin->displaySettings(
            array(
                'id' => 'idVal',
                'class' => 'classVal',
                'type' => 'checkbox',
                'std' => 'stdVal',
                'desc' => 'descVal',
                'choices' => array('val1', 'val2'),
            )
        );
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertNotEmpty($content);

        $exceptedMethods = array(
            'is_admin',
        );
        array_splice($methodCalled, 1);
        $this->assertEquals($exceptedMethods, $methodCalled);

        $exceptedArgs = array(
            array(),
        );
        array_splice($methodArgs, 1);
        $this->assertEquals($exceptedArgs, $methodArgs);
    }
}
