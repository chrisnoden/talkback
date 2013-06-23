<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category  Test
 * @package   Talkback
 * @author    Chris Noden <chris.noden@gmail.com>
 * @copyright 2009-2013 Chris Noden
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link      https://github.com/chrisnoden
 */

namespace Talkback\Tests\Talkback;

use Talkback\Log\LogLevel;
use Talkback\Logger;

class LoggerTest extends \PHPUnit_Framework_TestCase
{


    /**
     * The Logger class should be a singleton
     * and should return a \Psr\Log\AbstractLogger inherited object
     */
    public function testLoggerSingleton()
    {
        $this->assertInstanceOf('Talkback\Singleton', Logger::getInstance());
        $obj = Logger::getLogger('test logger');
        $this->assertInstanceOf('Talkback\Log\LogAbstract', $obj);
        $this->assertInstanceOf('\Psr\Log\AbstractLogger', $obj);
    }



    /**
     * Test our main method of created a new Talkback service
     * ie - by using a Static method call to the Logger class
     */
    public function testSingletonBinding()
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
     *
     * @depends testSingletonBinding
     */
    public function testLoggerStaticTest()
    {
        $obj = Logger::getLogger('test logger');
        $this->assertEquals('new logger name', $obj->getName());
    }


    /**
     * Tests the very basic assertion that an ERROR should be written
     * out to the client (on Windows) or STDERR on Linux/Unix
     */
    public function testLoggerConsoleOutput()
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->expectOutputString("hello, this is a test log\n");
        } else {
            $this->expectOutputString("");
        }
        $obj = Logger::getLogger('test logger');
        $obj->log(LogLevel::ERROR, "hello, this is a test log");
    }


}