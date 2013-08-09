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
 * @category  Channel
 * @package   Talkback
 * @author    Chris Noden <chris.noden@gmail.com>
 * @copyright 2009-2013 Chris Noden
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link      https://github.com/chrisnoden
 */

namespace Talkback\Channel;

use Talkback\Exception\InvalidArgumentException;

/**
 * Class Growl
 * Provides a Syslog messaging object
 * So you can send messages to Syslog
 *
 * @category Channel\Growl
 * @package  talkback
 * @author   Chris Noden <chris.noden@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link     https://github.com/chrisnoden/synergy
 */
class Syslog extends ChannelObject
{
    /**
     * @var int
     */
    private $facility = LOG_LOCAL4;
    /**
     * @var int
     */
    private $option = LOG_PID;
    /**
     * @var bool is our syslog handler open
     */
    private $isOpen = false;


    public function __construct($name)
    {
        parent::__construct();
        $this->setName($name);
        // If this class is used for Debug logging then these are useful defaults - otherwise they'll likely be ignored
        $this->_fieldDelimiter = ' ';
        $this->_aFieldTitles = array('linenum' => 'line:');
    }


    public function __destruct()
    {
        $this->closeLog();
        parent::__destruct();
    }


    /**
     * SysLog facility (defaults to LOG_LOCAL4)
     *
     * @param $facility
     *
     * @throws \Talkback\Exception\InvalidArgumentException
     * @return void
     */
    public function setFacility($facility)
    {
        $aFacilities = array(
            LOG_AUTH,
            LOG_AUTHPRIV,
            LOG_CRON,
            LOG_DAEMON,
            LOG_KERN,
            LOG_LOCAL0,
            LOG_LOCAL1,
            LOG_LOCAL2,
            LOG_LOCAL3,
            LOG_LOCAL4,
            LOG_LOCAL5,
            LOG_LOCAL6,
            LOG_LOCAL7,
            LOG_LPR,
            LOG_MAIL,
            LOG_NEWS,
            LOG_SYSLOG,
            LOG_USER,
            LOG_UUCP
        );
        if (is_int($facility) && in_array($facility, $aFacilities)) {
            $this->facility = $facility;
            $this->resetLog();
        } else {
            throw new InvalidArgumentException("facility must be a valid LOG facility constant");
        }
    }


    /**
     * Set the SysLog option (defaults to LOG_PID)
     *
     * @param $option
     *
     * @throws \Talkback\Exception\InvalidArgumentException
     * @return void
     */
    public function setOption($option)
    {
        $aOptions = array(
            LOG_CONS,
            LOG_NDELAY,
            LOG_ODELAY,
            LOG_PERROR,
            LOG_PID
        );
        if (is_int($option) && (array_sum($aOptions) & $option)) {
            $this->option = $option;
            $this->resetLog();
        } else {
            throw new InvalidArgumentException("option must be a valid constant LOG_CONS, LOG_NDELAY, LOG_ODELAY, LOG_PERROR or LOG_PID (default)");
        }
    }


    /**
     * The name of the syslog handler
     *
     * @return mixed|string
     */
    public function getName()
    {
        return $this->_name;
    }


    /**
     * Open the syslog handler
     */
    private function openLog()
    {
        if (!$this->isOpen) {
            // open syslog
            openlog($this->_name, $this->option, $this->facility);
            $this->isOpen = true;
        }
    }


    /**
     * Close the syslog handler
     */
    private function closeLog()
    {
        if ($this->isOpen) {
            closelog();
            $this->isOpen = false;
        }
    }


    /**
     * Close and re-open the syslog (if it's already open)
     */
    private function resetLog()
    {
        if ($this->isOpen) {
            $this->closeLog();
            $this->openLog();
        }
    }


    /**
     * Send the message to SysLog
     *
     * @param string $message
     *
     * @return ChannelObject|void
     */
    public function write($message)
    {
        parent::write($message);
        $this->openLog();

        // map our log level to a syslog level
        switch ($this->_level)
        {
            case 'debug':
                $syslog_level = LOG_DEBUG;
                break;

            case 'notice':
                $syslog_level = LOG_NOTICE;
                break;

            case 'info':
                $syslog_level = LOG_INFO;
                break;

            case 'alert':
            case 'warn':
            case 'warning':
                $syslog_level = LOG_WARNING;
                break;

            case 'emergency':
            case 'critical':
            case 'error':
            case 'fatal':
                $syslog_level = LOG_ERR;
                break;

            default:
                $syslog_level = LOG_NOTICE;
        }
        $message = $this->prepareMessage($message, array('time', 'level'));
        syslog($syslog_level, $message);
        parent::written();
    }

}
