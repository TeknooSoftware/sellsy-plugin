<?php

namespace UniAlteri\Tests\Sellsy\Wordpress;

use UniAlteri\Sellsy\Wordpress\Widget;

class WidgetTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $widget = new Widget('idBase', 'name', array('foo'=>'bar'), array('bar'=>'foo'));
        $this->assertEquals('idBase', $widget->getIdBase());
        $this->assertEquals('name', $widget->getName());
        $this->assertEquals(array('foo'=>'bar'), $widget->getWidgetOptions());
        $this->assertEquals(array('bar'=>'foo'), $widget->getControlOptions());
    }

    public function testWidgetEmpty()
    {
        $widget = new Widget();
        global $definedVar;
        $definedVar = array();

        $widget->widget(array(), array('text'=>'fooBar'));
        $this->assertEmpty($definedVar);
    }

    public function testUpdate()
    {

    }

    public function testForm()
    {

    }
}