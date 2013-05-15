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
        $this->assertInstanceOf('Talkback\Channel\ChannelObject', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
        $this->assertEquals('Talkback\Channel\Html', $obj->__toString());
    }


    public function testWriteOutput()
    {
        $obj = new Html();
        $this->expectOutputString("\n\n<hr/>\n\n<table style='font-size: small;' border='1' cellspacing='0' cellpadding='2'><thead style='font-weight: bold;'><td valign=\"top\">timestamp</td><td valign=\"top\">message</td>\n<tr><td valign=\"top\">".date("Y/m/d H:i:s")."</td><td valign=\"top\"><pre>test message</pre></td></tr>\n</table>\n\n\n");
        $obj
            ->addTimestamp()
            ->write("test message");
        $obj->flush();
    }


}