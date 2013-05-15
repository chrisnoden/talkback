<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 * 
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests\Channel;

use Talkback\Channel\Console;

class ConsoleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Instantiate the Console Channel Object
     */
    public function testConsoleObject()
    {
        $obj = new Console();
        $this->assertInstanceOf('Talkback\Channel\Console', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelObject', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
        $this->assertEquals('Talkback\Channel\Console', $obj->__toString());
    }


    /**
     * Does the Object output correctly to the console
     */
    public function testConsoleOutput()
    {
        $obj = new Console();
        $this->expectOutputString("hello, this is a test log\n");
        $obj->write("hello, this is a test log");
    }

}