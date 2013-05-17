<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 * 
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests\Channel;

use Talkback\Channel\ChannelFactory;
use Talkback\Channel\Html;
use Talkback\Logger;
use Talkback\Router;

class ChannelFactoryTest extends \PHPUnit_Framework_TestCase
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


    public function tearDown()
    {
        if (file_exists($this->_testFileName)) {
            unlink($this->_testFileName);
        }
        if (file_exists($this->_invalidFileName)) {
            unlink($this->_invalidFileName);
        }
    }


    /**
     * Tests the File Channel
     * Ensures the target File does not exist until we first write to it
     * Then asserts that the contents of the log file are what we expect
     */
    public function testFileChannel()
    {
        $obj = ChannelFactory::File($this->_testFileName);
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
    public function testFileChannelEmptyNameException()
    {
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'filename must be an absolute filename in a writeable directory'
        );
        $obj = ChannelFactory::File('');
    }


    /**
     * Trying to instantiate a new File channel without a filename
     * or with an invalid filename should cause an Exception to be thrown
     */
    public function testFileChannelInvalidNameException()
    {
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'filename must be an absolute filename in a writeable directory'
        );
        $obj = ChannelFactory::File($this->_invalidFileName);
    }


    /**
     * Basic assertions of a new Growl Channel object
     */
    public function testGrowlChannel()
    {
        $obj = ChannelFactory::Growl('testapp');
        $this->assertInstanceOf('Talkback\Channel\Growl', $obj);
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
        $obj = ChannelFactory::Prowl('My Test App', 'testapikey');
        $this->assertInstanceOf('Talkback\Channel\Prowl', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelObject', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
    }
}
