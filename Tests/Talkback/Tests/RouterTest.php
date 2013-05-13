<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests\Talkback;


use Talkback\Logger;
use Talkback\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{

    private $_testFileName;
    private $_invalidFileName;


    public function setUp() {
        $this->_testFileName = '/private/tmp/test.log';
        $this->_invalidFileName = DIRECTORY_SEPARATOR . 'invalidpath' . DIRECTORY_SEPARATOR . 'file.log';
    }

    public function testRouterObjectInstantiation()
    {
        $obj = new Router();
        $this->assertInstanceOf('Talkback\Router', $obj);
        $this->assertInstanceOf('Psr\Log\AbstractLogger', $obj);
        $this->assertInstanceOf('Psr\Log\LoggerInterface', $obj);
    }

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

//    public function testCommsFile()
//    {
//        $obj = Comms::File($this->_testFileName);
//        $this->assertInstanceOf('Comms_File', $obj);
//        $this->assertInstanceOf('Comms_Base', $obj);
//        $this->assertInstanceOf('Object', $obj);
//        $this->assertFileNotExists($this->_testFileName);
//        $obj->write("Test");
//        $this->assertFileExists($this->_testFileName);
//        $this->assertStringEqualsFile($this->_testFileName, "Test\n");
//    }
//
//    public function testCommsFileException()
//    {
//        $this->setExpectedException(
//            'SalInvalidArgumentException', 'filename must be an absolute filename in a writeable directory'
//        );
//        $obj = Comms::File('');
//        $obj = Comms::File($this->_invalidFileName);
//    }
//
//    public function testCommsGrowl()
//    {
//        $obj = Comms::Growl('testapp');
//        $this->assertInstanceOf('Comms_Growl', $obj);
//        $this->assertInstanceOf('Comms_Base', $obj);
//        $this->assertInstanceOf('Object', $obj);
//    }
//
//    public function testCommsHTML()
//    {
//        $obj = new Comms_HTML();
//        $this->assertInstanceOf('Comms_HTML', $obj);
//        $this->assertInstanceOf('Comms_Base', $obj);
//        $this->assertInstanceOf('Object', $obj);
//        $obj->setBasicClient();
//        $this->expectOutputString("Test\n");
//        $obj->write('Test');
//        $obj->addField('tag');
//        $obj->setFieldValues(array('tag' => 'mytag'));
//        $this->expectOutputString("Test\nmytag Test 2\n");
//        $obj->write('Test 2');
//    }
//
//    public function testCommsProwl()
//    {
//        $obj = Comms::Prowl('My Test App', 'testapikey');
//        $this->assertInstanceOf('Comms_Prowl', $obj);
//    }
//
//
//    public function tearDown()
//    {
//        if (file_exists($this->_testFileName)) {
//            unlink($this->_testFileName);
//        }
//        if (file_exists($this->_invalidFileName)) {
//            unlink($this->_invalidFileName);
//        }
//    }


}
