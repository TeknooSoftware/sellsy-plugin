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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
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

        include dirname(dirname(__DIR__)).'/sellsy-plugin.php';

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
