<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Channel;

use Talkback\Exception\InvalidArgumentException;

/**
 * Provides a Syslog messaging object
 * So you can send messages to Syslog
 */
class Syslog extends ChannelObject
{
    /**
     * @var int
     */
    private $_facility = LOG_LOCAL4;
    /**
     * @var int
     */
    private $_option = LOG_PID;
    /**
     * @var bool is our syslog handler open
     */
    private $_isOpen = false;
    /**
     * @var string name of our project
     */
    private $_name;


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
     * @param $facility
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
            $this->_facility = $facility;
            $this->resetLog();
        } else {
            throw new InvalidArgumentException("facility must be a valid LOG facility constant");
        }
    }


    /**
     * @param $option
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
            $this->_option = $option;
            $this->resetLog();
        } else {
            throw new InvalidArgumentException("option must be a valid constant LOG_CONS, LOG_NDELAY, LOG_ODELAY, LOG_PERROR or LOG_PID (default)");
        }
    }


    /**
     * @param $name
     * @throws \Talkback\Exception\InvalidArgumentException
     */
    public function setName($name)
    {
        if (is_string($name) && mb_strlen($name, 'utf-8') < 30) {
            $name = ucwords($name);
            $this->_name = preg_replace('/[^0-9a-zA-Z]/', '', $name);
        } else {
            throw new InvalidArgumentException("Syslog name must be a string, max 30 chars");
        }
    }


    /**
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
        if (!$this->_isOpen) {
            // open syslog
            openlog($this->_name, $this->_option, $this->_facility);
            $this->_isOpen = true;
        }
    }


    /**
     * Close the syslog handler
     */
    private function closeLog()
    {
        if ($this->_isOpen) {
            closelog();
            $this->_isOpen = false;
        }
    }


    /**
     * Close and re-open the syslog (if it's already open)
     */
    private function resetLog()
    {
        if ($this->_isOpen) {
            $this->closeLog();
            $this->openLog();
        }
    }


    /**
     * @param string $message
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
