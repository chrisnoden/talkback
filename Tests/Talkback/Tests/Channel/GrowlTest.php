<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests\Channel;

use Talkback\Channel\Growl;

class GrowlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Basic Growl Object instantiation
     */
    public function testGrowlObject()
    {
        $obj = new Growl('Synergy Test');
        $this->assertInstanceOf('Talkback\Channel\Growl', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelAbstract', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
        $this->assertEquals('Talkback\Channel\Growl', $obj->__toString());
    }


    /**
     * App Name must be a string type
     */
    public function testAppNameWrongTypeException()
    {
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException',
            'Growl appName must be a string, max 50 chars'
        );
        /** @noinspection PhpUnusedLocalVariableInspection */
        $obj = new Growl(1);
    }


    /**
     * App Name must be a string
     */
    public function testAppNameEmptyStringException()
    {
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException',
            'Growl appName must be a string, max 50 chars'
        );
        /** @noinspection PhpUnusedLocalVariableInspection */
        $obj = new Growl('');
    }


    /**
     * App Name is limited to 50 chars
     */
    public function testAppNameTooLongException()
    {
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException',
            'Growl appName must be a string, max 50 chars'
        );
        /** @noinspection PhpUnusedLocalVariableInspection */
        $obj = new Growl('This name is a little greater than 50 characters in length');
    }


    /**
     * If Growl is running then this should not throw any Exceptions
     */
    public function testMessageWrite()
    {
        $obj = new Growl('Synergy Test');
        $obj->write("Test Message");
    }


}