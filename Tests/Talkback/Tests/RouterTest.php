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


use Talkback\Channel\Console;
use Talkback\Log\LogLevel;
use Talkback\Channel\ChannelFactory;
use Talkback\Logger;
use Talkback\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{


    /**
     * Test our Router class can be instantiated properly
     */
    public function testRouterObjectInstantiation()
    {
        $obj = new Router();
        $this->assertInstanceOf('Talkback\Router', $obj);
    }


    public function testAddingValidHandler()
    {
        /**
         * @var $t \Talkback\Log\LogLevel
         */
        $t = new LogLevel();
        $r = new \ReflectionObject($t);
        $aLogLevels = $r->getConstants();

        $obj = Logger::getLogger('test3');

        $aBuildLevels = array();
        foreach ($aLogLevels AS $LogLevel)
        {
            $aBuildLevels[] = $LogLevel;
            $obj->addChannel($aBuildLevels, ChannelFactory::Basic());
        }
    }


    /**
     * You should be able to chain the addHandler calls
     * @depends testAddingValidHandler
     */
    public function testChainedHandlers()
    {
        $obj = new Router();
        $obj
            ->addChannel(array(LogLevel::EMERGENCY, LogLevel::ERROR), ChannelFactory::Basic())
            ->addChannel(LogLevel::INFO, ChannelFactory::Growl('Test App'));
    }


    public function testChainedLogging()
    {
        $console1 = new Console();
        $console1->setUseStream(false);
        $console2 = new Console();
        $console2->setUseStream(false);
        $obj = new Router();
        $obj
            ->addChannel(array(LogLevel::EMERGENCY, LogLevel::ERROR), $console1)
            ->addChannel(LogLevel::ERROR, $console2);

        $this->expectOutputString("emergency:EMERGENCY log\n");
        $obj->log(LogLevel::EMERGENCY, 'EMERGENCY log');

        $this->expectOutputString("emergency:EMERGENCY log\nerror:ERROR log\nerror:ERROR log\n");
        $obj->log(LogLevel::ERROR, 'ERROR log');
    }

}
