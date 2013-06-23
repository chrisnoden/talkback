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
 * @category  Talkback
 * @package   Talkback
 * @author    Chris Noden <chris.noden@gmail.com>
 * @copyright 2009-2013 Chris Noden
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link      https://github.com/chrisnoden
 */

namespace Talkback;

use Talkback\Log\LogAbstract;
use Talkback\Log\LogLevel;
use Talkback\Log\LogInterface;
use Talkback\Channel\ChannelFactory;
use Talkback\Channel\ChannelObject;
use Talkback\Exception\InvalidArgumentException;

/**
 * The Talkback debugging/logging class
 * This works by adding a layer of detail and intelligence to the ChannelFactory objects
 * You add one or many ChannelFactory objects and Handlers and each is sent any
 * log messages that are received by this class
 */
final class Router extends LogAbstract
{

    /**
     * @var array these become columns/fields or prefixes in the final output
     */
    private $_aFields = array();
    /**
     * @var array of ChannelObject objects
     */
    private $_aChannels = array();
    /**
     * @var array of Logger objects
     */
    private $_aLoggers = array();
    /**
     * @var array of valid LogLevels
     */
    private $_aValidLogLevels = array();
    /**
     * @var bool if true then notice & warning logs are completely ignored
     */
    private $_block = false;
    /**
     * @var string
     */
    private $_name = 'Talkback';


    public function __construct()
    {
        // populate our array of valid LogLevels using Reflection

        /**
         * @var $t LogLevel
         */
        $t = new LogLevel();
        $r = new \ReflectionObject($t);
        $this->_aValidLogLevels = $r->getConstants();
    }


    /**
     * Add a ChannelFactory object to our framework debugger
     *
     * @static
     * @param $aLevels array set of log levels (eg Talkback\Log\LogLevel::INFO)
     * @param ChannelObject $oHandler from the ChannelFactory
     * @return Router
     * @throws \Talkback\Exception\InvalidArgumentException
     */
    public function addChannel($aLevels, ChannelObject $oHandler)
    {
        if (!is_array($aLevels)) {
            $aLevels = array($aLevels);
        }
        foreach ($aLevels AS $logLevel)
        {
            $logLevel = strtolower($logLevel);
            if (!in_array($logLevel, $this->_aValidLogLevels)) {
                throw new InvalidArgumentException('addChannel expects an array of valid LogLevel const values');
            }
            $this->addHandler($logLevel, $oHandler);
        }

        return $this;
    }


    /**
     * @param $level
     * @param \Talkback\Channel\ChannelObject $oHandler
     */
    private function addHandler($level,ChannelObject $oHandler)
    {
        if (!isset($this->_aChannels[$level])) {
            $this->_aChannels[$level] = array();
        }
        $this->_aChannels[$level][] = $oHandler;
    }


    /**
     * Add a new logger to our debugger
     *
     * @param Object $logger
     */
    public function addLogger($logger)
    {
        if ($logger instanceof LogInterface) {
            $this->_aLoggers[] = $logger;
        } else

            // else, is it a Logger object (hopefully from log4php)
            if (is_a($logger, '\Logger')) {
                $this->_aLoggers[] = $logger;
            }

    }


    /**
     * @static
     * @return SourceFile
     */
    private function buildSourceObject()
    {
        $arr = debug_backtrace();
        $aSource = $arr[1];
        $oSource = new SourceFile();
        $oSource->setFilename($aSource['file']);
        $oSource->setLineNum($aSource['line']);
        if (isset($aSource['class'])) {
            $className = $aSource['class'];
            if (strstr($className, 'Debug') && isset($arr[2]) && isset($arr[2]['class'])) {
                $oSource->setClassName($arr[2]['class']);
            } else {
                $oSource->setClassName($aSource['class']);
            }
        }
        return $oSource;
    }


    /**
     * @param $block bool if true then all but errors are blocked (not sent to the output channels)
     */
    public function setBlock($block)
    {
        if (is_bool($block)) {
            $this->_block = $block;
        }
    }


    /**
     * @param mixed|string $level
     * @param string       $message
     * @param array        $context
     * @return null|void
     */
    public function log($level = LogLevel::INFO, $message, array $context=array())
    {
        $oSource = self::buildSourceObject();
        $logFilename = $oSource->getFilename();

        // Populate our internal contexts if still part of the defined field list
        $aContexts = $this->_aFields;
        if (isset($this->_aFields['linenum'])) {
            $aContexts['linenum'] = $oSource->getLineNum();
        }
        if (isset($this->_aFields['filename'])) {
            $aContexts['filename'] = $logFilename;
        }
        if (isset($this->_aFields['time'])) {
            $aContexts['time'] = date('r');
        }
        if (isset($this->_aFields['level'])) {
            $aContexts['level'] = self::errorAsString($level);
        }

        // Replace with any contexts supplied in the log method request
        if (count($context) > 0) {
            $aContexts = array_replace($aContexts, $context);
        }

        if (isset($this->_aChannels[$level])) {
            /**
             * @var $oHandler ChannelObject
             */
            foreach ($this->_aChannels[$level] AS $oHandler)
            {
                $oHandler->setFieldValues($aContexts);
                if ($this->_block) $oHandler->disable();
                $oHandler->write($message);
                $oHandler->enable();
            }
        } else {
            switch ($level)
            {
                case LogLevel::WARNING:
                case LogLevel::ERROR:
                case LogLevel::CRITICAL:
                    $oHandler = ChannelFactory::Basic();
                    $oHandler->setFieldValues($aContexts);
                    if ($this->_block) $oHandler->disable();
                    $oHandler->write($message);
                    $oHandler->enable();
                    $oHandler = null;
                    break;
            }
        }

        // Iterate our loggers and send the message to them at the appropriate level
        /**
         * @var $oLogger \Talkback\Log\LogAbstract
         */
        foreach ($this->_aLoggers AS $oLogger)
        {
            $oLogger->log($level, $message, $context);
        }

    }


    /**
     * Assign an optional tag which is prefixed to the log output
     *
     * @deprecated
     * @static
     * @param $tag string
     */
    public function setTag($tag)
    {
        $tag = trim($tag);
        if (strlen($tag) == 0) {
            return;
        }
        $tag = substr($tag,0,12);

        $this->_aFields['tag'] = $tag;
    }


    /**
     * Return the current tag value
     *
     * @deprecated
     * @static
     * @return string
     */
    public function getTag()
    {
        if (isset($this->_aFields['tag'])) return $this->_aFields['tag'];
        return null;
    }


    /**
     * Set a project name used across the debugging
     *
     * @static
     * @param $name string limited to 20 characters
     */
    public function setName($name)
    {
        $name = trim($name);
        if (is_string($name) && strlen($name) > 0) {
            /**
             * @var $oModule ChannelObject
             */
            foreach ($this->_aChannels AS $oModule)
            {
                $oModule->setName($name);
            }
            $this->_name = substr($name, 0, 20);
        }
    }


    /**
     * @static
     * @return string current project name
     */
    public function getName()
    {
        return $this->_name;
    }


    /**
     * Changes the error level into a string for humans
     *
     * @param $level
     * @return string
     */
    protected function errorAsString($level)
    {
        $string = 'unknown';

        switch ($level)
        {
            case LogLevel::DEBUG:
                $string = 'debug';
                break;
            case LogLevel::INFO:
                $string = 'info';
                break;
            case LogLevel::NOTICE:
                $string = 'notice';
                break;
            case LogLevel::WARNING:
                $string = 'warning';
                break;
            case LogLevel::ERROR:
                $string = 'error';
                break;
            case LogLevel::CRITICAL:
                $string = 'critical';
                break;
        }

        return $string;
    }

}


