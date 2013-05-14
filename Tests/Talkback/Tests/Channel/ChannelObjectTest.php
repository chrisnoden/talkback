<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 * 
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests\Channel;

use Psr\Log\LogLevel;
use Talkback\Channel\ChannelObject;

class ChannelObjectTest extends \PHPUnit_Framework_TestCase
{


    /**
     * Test the basic instantiation
     */
    public function testObjectInstantiation()
    {
        $obj = new ChannelObject();
        $this->assertInstanceOf('Talkback\Channel\ChannelObject', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelInterface', $obj);
    }


    /**
     * @depends testObjectInstantiation
     */
    public function testSetLevelWrongConstant()
    {
        $obj = new ChannelObject();
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'setLevel($level) must be set with a \Psr\Log\LogLevel const value'
        );
        $obj->setLevel(E_USER_ERROR);
    }


    /**
     * @depends testObjectInstantiation
     */
    public function testSetLevelBool()
    {
        $obj = new ChannelObject();
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'setLevel($level) must be set with a \Psr\Log\LogLevel const value'
        );
        $obj->setLevel(true);
    }


    /**
     * @depends testObjectInstantiation
     */
    public function testSetLevelInt()
    {
        $obj = new ChannelObject();
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'setLevel($level) must be set with a \Psr\Log\LogLevel const value'
        );
        $obj->setLevel(1);
    }


    /**
     * @depends testObjectInstantiation
     */
    public function testSetLevelString()
    {
        $obj = new ChannelObject();
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'setLevel($level) must be set with a \Psr\Log\LogLevel const value'
        );
        $obj->setLevel("string");
    }


    /**
     * @depends testObjectInstantiation
     */
    public function testSetLevelValid()
    {
        $obj = new ChannelObject();
        $obj->setLevel(LogLevel::ALERT);
    }


    /**
     * @depends testObjectInstantiation
     */
    public function testAddFieldNonString()
    {
        $obj = new ChannelObject();
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'field name must be a string'
        );
        $obj->addField(123456);
    }


    /**
     * @depends testObjectInstantiation
     */
    public function testAddFieldValid()
    {
        $obj = new ChannelObject();
        $retObj = $obj->addField('testfield');
        $this->assertEquals($obj, $retObj);
        $this->assertArrayHasKey('testfield', $obj->getFields());
    }

}
