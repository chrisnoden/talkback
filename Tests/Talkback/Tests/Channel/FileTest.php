<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 * 
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests\Channel;

use Talkback\Channel\File;

class FileTest extends \PHPUnit_Framework_TestCase
{

    private $_testFileName;
    private $_invalidFileName;


    public function setUp() {
        $this->_testFileName = '/private/tmp/test.log';
        $this->_invalidFileName = DIRECTORY_SEPARATOR . 'invalidpath' . DIRECTORY_SEPARATOR . 'file.log';
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
     * Instantiate the Console Channel Object
     */
    public function testConsoleObject()
    {
        $obj = new File();
        $this->assertInstanceOf('Talkback\Channel\File', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelObject', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
        $this->assertEquals('Talkback\Channel\File', $obj->__toString());
    }


    /**
     * writing messages to the file
     */
    public function testWrite()
    {
        $obj = new File();
        $obj->setFilename($this->_testFileName);
        $obj->write("Test message");
        $this->assertFileExists($this->_testFileName);
        $this->assertStringEqualsFile($this->_testFileName, date("Y/m/d H:i:s") . " Test message\n");
    }


    /**
     * invalid filename
     */
    public function testInvalidFilename()
    {
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'filename must be an absolute filename in a writeable directory'
        );
        $obj = new File();
        $obj->setFilename($this->_invalidFileName);
    }


    /**
     * @depends testInvalidFilename
     */
    public function testTimestamp()
    {
        $obj = new File();
        $obj->setFilename($this->_testFileName);
        $obj
            ->addTimestamp()
            ->setFieldDelimiter(' ')
            ->write("Test message 1");
        $obj
            ->addTimestamp()
            ->setFieldDelimiter(':')
            ->write("Test message 2");
        $expected = sprintf("%s Test message 1\n%s:Test message 2\n", date("Y/m/d H:i:s"), date("Y/m/d H:i:s"));
        $this->assertStringEqualsFile($this->_testFileName, $expected);
    }


}