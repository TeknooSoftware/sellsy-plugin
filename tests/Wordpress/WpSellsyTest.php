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

        include dirname(dirname(__DIR__)).'/wp_sellsy.php';

        $this->assertEquals(
            array(),
            $methodCalled
        );

        $this->assertEquals(
            array(),
            $methodArgs
        );
    }
}