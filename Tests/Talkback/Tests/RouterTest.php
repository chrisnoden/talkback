<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests\Talkback;


use Psr\Log\LogLevel;
use Talkback\Channel\ChannelLauncher;
use Talkback\Channel\Html;
use Talkback\Logger;
use Talkback\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{

    private $_testFileName;
    private $_invalidFileName;


    public function setUp() {
        $this->_testFileName = '/private/tmp/test.log';
        $this->_invalidFileName = DIRECTORY_SEPARATOR . 'invalidpath' . DIRECTORY_SEPARATOR . 'file.log';

        if (file_exists($this->_testFileName)) {
            unlink($this->_testFileName);
        }
        if (file_exists($this->_invalidFileName)) {
            unlink($this->_invalidFileName);
        }
    }


    /**
     * Test our Router class can be instantiated properly
     */
    public function testRouterObjectInstantiation()
    {
        $obj = new Router();
        $this->assertInstanceOf('Talkback\Router', $obj);
    }


    /**
     * The Logger class should be a singleton
     * and should return a \Psr\Log\AbstractLogger inherited object
     */
    public function testLoggerSingleton()
    {
        $this->assertInstanceOf('Talkback\Singleton', Logger::getInstance());
        $obj = Logger::getLogger('test logger');
        $this->assertInstanceOf('\Psr\Log\AbstractLogger', $obj);
    }


    /**
     * Test our main method of created a new Talkback service
     * ie - by using a Static method call to the Logger class
     */
    public function testStaticBinding()
    {
        $obj = Logger::getLogger('test logger');
        $this->assertInstanceOf('Talkback\Router', $obj);
        $this->assertEquals('test logger', $obj->getName());

        // Use the same logger name and get the same Router object
        $obj2 = Logger::getLogger('test logger');
        $this->assertInstanceOf('Talkback\Router', $obj2);
        $this->assertEquals('test logger', $obj2->getName());

        // Use a different logger name and get a new Router object
        $obj3 = Logger::getLogger('test logger new');
        $this->assertInstanceOf('Talkback\Router', $obj3);
        $this->assertNotEquals('test logger', $obj3->getName());

        // Change the name of one object
        $obj2->setName("new logger name");
        $this->assertEquals('new logger name', $obj->getName());
        $this->assertEquals('new logger name', $obj2->getName());
        $this->assertEquals('test logger new', $obj3->getName());
    }


    /**
     * This chains after the above test to check the returned logger
     * still has the new name - it persisted
     */
    public function testLoggerStaticTest()
    {
        $obj = Logger::getLogger('test logger');
        $this->assertEquals('new logger name', $obj->getName());
    }


    /**
     * Tests the very basic assertion that an ERROR should be written
     * out to the client with just a newline char appended
     */
    public function testLoggerConsoleOutput()
    {
        $this->expectOutputString("hello, this is a test log\n");
        $obj = Logger::getLogger('test logger');
        $obj->log("hello, this is a test log", LogLevel::ERROR);
    }


    /**
     * Tests the File Channel
     * Ensures the target File does not exist until we first write to it
     * Then asserts that the contents of the log file are what we expect
     */
    public function testFileChannel()
    {
        $obj = ChannelLauncher::File($this->_testFileName);
        $this->assertInstanceOf('Talkback\Channel\File', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelObject', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
        $this->assertFileNotExists($this->_testFileName);
        $obj->write("Test");
        $this->assertFileExists($this->_testFileName);
        $this->assertStringEqualsFile($this->_testFileName, "Test\n");
    }


    /**
     * Trying to instantiate a new File channel without a filename
     * or with an invalid filename should cause an Exception to be thrown
     */
    public function testFileChannelException()
    {
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'filename must be an absolute filename in a writeable directory'
        );
        $obj = ChannelLauncher::File('');
        $obj = ChannelLauncher::File($this->_invalidFileName);
    }


    /**
     * Basic assertions of a new Growl Channel object
     */
    public function testGrowlChannel()
    {
        $obj = ChannelLauncher::Growl('testapp');
        $this->assertInstanceOf('Talkback\Channel\Growl', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelObject', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
    }


    /**
     * Basic assertions of a new Syslog Channel object
     */
    public function testSyslogChannel()
    {
        $obj = ChannelLauncher::Syslog('testapp');
        $this->assertInstanceOf('Talkback\Channel\Syslog', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelObject', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
    }


    /**
     * Basic assertions of a new Html Channel object
     */
    public function testHtmlChannel()
    {
        $obj = new Html();
        $this->assertInstanceOf('Talkback\Channel\Html', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelObject', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
        $obj->setBasicClient();
        $this->expectOutputString("Test\n");
        $obj->write('Test');
        $obj->addField('tag');
        $obj->setFieldValues(array('tag' => 'mytag'));
        $this->expectOutputString("Test\nmytag Test 2\n");
        $obj->write('Test 2');
    }


    /**
     * Basic assertions of a new Prowl Channel object
     */
    public function testCommsProwl()
    {
        $obj = ChannelLauncher::Prowl('My Test App', 'testapikey');
        $this->assertInstanceOf('Talkback\Channel\Prowl', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelObject', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
    }


    public function tearDown()
    {
        if (file_exists($this->_testFileName)) {
            unlink($this->_testFileName);
        }
        if (file_exists($this->_invalidFileName)) {
            unlink($this->_invalidFileName);
        }
    }


}
