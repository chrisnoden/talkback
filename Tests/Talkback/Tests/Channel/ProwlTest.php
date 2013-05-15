<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests\Channel;

use Talkback\Channel\Prowl;

class ProwlTest extends \PHPUnit_Framework_TestCase
{

    private $_apiKey = 'a702e646f9398c250cb073b8565b8506af959da8';

    /**
     * Basic Growl Object instantiation
     */
    public function testProwlObject()
    {
        $obj = new Prowl('Synergy Test', $this->_apiKey);
        $this->assertInstanceOf('Talkback\Channel\Prowl', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelObject', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
        $this->assertEquals('Talkback\Channel\Prowl', $obj->__toString());
    }


    /**
     * App Name must be a string type
     */
    public function testAppNameWrongTypeException()
    {
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'Prowl appName must be a string, max 254 chars'
        );
        $obj = new Prowl(1, $this->_apiKey);
    }


    /**
     * App Name must be a string
     */
    public function testAppNameEmptyStringException()
    {
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'Prowl appName must be a string, max 254 chars'
        );
        $obj = new Prowl('', $this->_apiKey);
    }


    /**
     * App Name is limited to 50 chars
     */
    public function testAppNameTooLongException()
    {
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'Prowl appName must be a string, max 254 chars'
        );
        $obj = new Prowl('This name is aiming to be just a little bit longer than 254 characters in length, we already know it is a string but maybe if I am careful it can just be over the limit which is a very long limit indeed oh my goodness and still it goes on can you believe it', $this->_apiKey);
    }


    /**
     * If Growl is running then this should not throw any Exceptions
     */
    public function testMessageWrite()
    {
        $obj = new Prowl('Synergy Test', $this->_apiKey);
        $obj->setEventName('Synergy Test Event');
        $obj->write("Test Message");
    }

    /**
     * If Growl is running then this should not throw any Exceptions
     */
    public function testMessageWriteEventException()
    {
        $obj = new Prowl('Synergy Test', $this->_apiKey);
        $this->setExpectedException(
            'Talkback\Exception\ChannelTargetException', 'Prowl requires you set an eventName up to 1024 chars'
        );
        $obj->write("This should have failed");
    }

}