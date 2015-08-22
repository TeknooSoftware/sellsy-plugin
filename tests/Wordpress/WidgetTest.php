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

use UniAlteri\Sellsy\Wordpress\Widget;

class WidgetTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $widget = new Widget('idBase', 'name', array('foo' => 'bar'), array('bar' => 'foo'));
        $this->assertEquals('idBase', $widget->getIdBase());
        $this->assertEquals('name', $widget->getName());
        $this->assertEquals(array('foo' => 'bar'), $widget->getWidgetOptions());
        $this->assertEquals(array('bar' => 'foo'), $widget->getControlOptions());
    }

    public function testWidgetEmpty()
    {
        $widget = new Widget();

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        ob_start();
        $widget->widget(array(), array());
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('', $output);
        $this->assertEquals(array(), $methodCalled);
    }

    public function testWidget()
    {
        $widget = new Widget();

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        prepareMock(
            'apply_filters',
            array('widget_title', 'titleValue'),
            function ($title, $value) {
                return strtoupper($value);
            }
        );

        prepareMock(
            'do_shortcode',
            array('[wpsellsy]'),
            function ($code) {
                return 'fooBarHtml';
            }
        );

        ob_start();
        $widget->widget(
            array(
                'before_widget' => 'beforeValue',
                'before_title' => 'beforeTitleValue',
                'after_title' => 'afterTitleValue',
                'after_widget' => 'afterValue',
            ),
            array(
                'title' => 'titleValue',
                'text' => 'fooBar',
            )
        );
        $output = ob_get_contents();
        ob_end_clean();

        $excepted = 'beforeValuebeforeTitleValueTITLEVALUEafterTitleValue<div>    fooBar</div>afterValue';
        $output = trim(str_replace(array('\r', '\n', PHP_EOL), '', $output));
        $this->assertEquals($excepted, $output);
        $this->assertEquals(array('apply_filters', 'do_shortcode'), $methodCalled);
        $this->assertEquals(
            array(
                array('widget_title', 'titleValue'),
                array('[wpsellsy]'),
            ),
            $methodArgs
        );
    }

    public function testUpdate()
    {
        $widget = new Widget();
        $oldInstance = array(
            'title' => 'fooBarTitle',
            'text' => 'fooBarText',
            'otherValue' => 'fooBar',
        );
        $newInstance = array(
            'title' => 'newTitle',
            'text' => 'newText',
            'otherValue' => 'barFoo',
        );

        $newInstance = $widget->update($newInstance, $oldInstance);
        $this->assertEquals(
            array(
                'title' => 'fooBarTitle',
                'text' => 'fooBarText',
                'otherValue' => 'fooBar',
            ),
            $oldInstance
        );
        $this->assertEquals(
            array(
                'title' => 'fooBarTitle',
                'text' => 'fooBarText',
                'otherValue' => 'barFoo',
            ),
            $newInstance
        );
    }

    public function testForm()
    {
        $widget = new Widget();

        global $methodCalled;
        global $methodArgs;
        $methodCalled = array();
        $methodArgs = array();

        prepareMock(
            'esc_attr',
            '*',
            function ($value) {
                return strtolower($value);
            }
        );

        $instance = array(
            'title' => 'fooBarTitle',
            'text' => 'fooBarText',
            'otherValue' => 'fooBar',
        );

        ob_start();
        $widget->form($instance);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(array('esc_attr', 'esc_attr', '_e', '_e'), $methodCalled);
        $exceptedOutput = <<<EOF
<p>
    <label for="title">
            </label>
    <input class="widefat" id="title" name="title" type="text" value="foobartitle" />
</p>
<p>
    <label for="text">
            </label>
    <input class="widefat" id="text" name="text" type="text" value="foobartext" />
</p>

EOF;

        $this->assertEquals($exceptedOutput, $output);
    }
}
