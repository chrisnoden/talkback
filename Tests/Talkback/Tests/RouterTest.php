<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests\Talkback;


use Psr\Log\LogLevel;
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
         * @var $t \Psr\Log\LogLevel
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
        $obj = new Router();
        $obj
            ->addChannel(array(LogLevel::EMERGENCY, LogLevel::ERROR), ChannelFactory::Basic())
            ->addChannel(LogLevel::ERROR, ChannelFactory::Basic());

        $this->expectOutputString("EMERGENCY log\n");
        $obj->log(LogLevel::EMERGENCY, 'EMERGENCY log');

        $this->expectOutputString("EMERGENCY log\nERROR log\nERROR log\n");
        $obj->log(LogLevel::ERROR, 'ERROR log');
    }

}
