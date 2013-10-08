<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests;

use Talkback\Object;

class ObjectTest extends \PHPUnit_Framework_TestCase
{

    public function testObject()
    {
        $obj = new Object();
        $this->assertInstanceOf('Talkback\Object', $obj);
    }


    public function testObjectReturnValue()
    {
        $obj = new Object();
        $this->assertEquals('Talkback\Object', $obj->__toString());
    }

}