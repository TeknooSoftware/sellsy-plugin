<?php

namespace UniAlteri\Tests\Sellsy\Wordpress;

class WpSellsyTest extends \PHPUnit_Framework_TestCase
{
    public function testWpSellsyFile()
    {
        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        //To test closure
        prepareMock(
            'add_action',
            '*',
            function ($action, $arg) {
                if ($arg instanceof \Closure) {
                    $arg();
                }
            }
        );

        include dirname(dirname(__DIR__)).'/wp_sellsy.php';

        $this->assertTrue(in_array('get_option', $methodCalled));
        $this->assertTrue(in_array('register_setting', $methodCalled));
        $this->assertTrue(in_array('add_filter', $methodCalled));
        $this->assertTrue(in_array('register_deactivation_hook', $methodCalled));
        $this->assertTrue(in_array('add_action', $methodCalled));
        $this->assertTrue(in_array('add_settings_error', $methodCalled));
        $this->assertTrue(in_array('add_shortcode', $methodCalled));
        $this->assertTrue(in_array('register_widget', $methodCalled));

        $this->assertNotEmpty(
            $methodArgs
        );
    }
}