<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 * 
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests\Channel;

use Talkback\Channel\Html;

class HtmlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Basic Object instantiation
     */
    public function testHtmlObject()
    {
        $obj = new Html();
        $this->assertInstanceOf('Talkback\Channel\Html', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelAbstract', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
        $this->assertEquals('Talkback\Channel\Html', $obj->__toString());
    }


    /**
     * HTML Output should be correctly formatted
     */
    public function testWriteOutput()
    {
        $expected = file_get_contents(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'HtmlTestOutput.html');
        $expected = preg_replace('/datetime/', date('Y/m/d H:i:s'), $expected);

        $this->expectOutputString($expected);
        $obj = new Html();
        $obj
            ->addTimestamp()
            ->write("test message");
        $obj->write("test message 2");
        $obj->flush();
    }


}