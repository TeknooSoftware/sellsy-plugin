<?php

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
     * @return Admin
     */
    protected function buildObject()
    {
        return new Admin($this->buildPluginMock(), $this->buildOptionsMock());
    }

    public function testAddJS()
    {
        $admin = $this->buildObject();

        global $methodCalled;
        $methodCalled = array();
        global $methodArgs;
        $methodArgs = array();

        $admin->addJS();

        $exceptedMethods = array (
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

        $exceptedArgs = array (
            array (
                '/js/jquery-ui.min.js',
                '/home/richard/Prog/uni_alteri/wordpress/sellsy-plugin/wp_sellsy.php',
            ),
            array (
                'jqueryui',
                NULL,
                array (
                    'jquery',
                ),
                '1.0',
                1,
            ),
            array (
                '/js/ui.multiselect.js',
                '/home/richard/Prog/uni_alteri/wordpress/sellsy-plugin/wp_sellsy.php',
            ),
            array (
                'uimultiselect',
                NULL,
                array (
                    'jquery',
                    'jqueryui',
                ),
                '1.0',
                1,
            ),
            array (
                '/js/wp_sellsy.js',
                '/home/richard/Prog/uni_alteri/wordpress/sellsy-plugin/wp_sellsy.php',
            ),
            array (
                'wpsellsyjscsource',
                NULL,
                array (
                    0 => 'jquery',
                    1 => 'uimultiselect',
                ),
                '1.0',
                1,
            ),
            array (
                'admin-ajax.php',
            ),
            array (
                'slswp_ajax_nonce',
            ),
            array (
                'wpsellsyjscsource',
                'ajax_var',
                array (
                    'url' => NULL,
                    'nonce' => NULL,
                ),
            ),
        );
        $this->assertEquals($exceptedArgs, $methodArgs);
    }
}
