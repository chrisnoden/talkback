<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */


namespace Talkback\Channel;

use Talkback\Exception\InvalidArgumentException;

class Growl extends ChannelObject
{

    /**
     * @var string Name of the Application
     */
    private $_applicationName;
    /**
     * @var string
     */
    private $_growl_password = '';
    /**
     * @var string ip address or hostname of the growl server
     */
    private $_growl_host = '127.0.0.1';
    /**
     * @var string growl protocol type
     */
    private $_growl_protocol = 'tcp';
    /**
     * @var int growl port
     */
    private $_growl_port;
    /**
     * @var int timeout for growl server connections
     */
    private $_growl_timeout = 5;
    /**
     * @var array the different notification types available
     */
    private $_growl_notifications = array();


    /**
     * @param $appName
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function __construct($appName)
    {
        parent::__construct();

        if (is_string($appName) && mb_strlen($appName, 'utf-8') > 0 && mb_strlen($appName, 'utf-8') < 50) {
            $this->_applicationName = trim($appName);
        } else {
            throw new InvalidArgumentException("Growl appName must be a string, max 50 chars");
        }

        // Notification Type definitions
        if (!defined('GROWL_NOTIFY_STATUS')) define('GROWL_NOTIFY_STATUS', 'GROWL_NOTIFY_STATUS');
        if (!defined('GROWL_NOTIFY_PHPERROR')) define('GROWL_NOTIFY_PHPERROR', 'GROWL_NOTIFY_PHPERROR');
        $this->_growl_notifications = array(
            GROWL_NOTIFY_STATUS => array(
                'display' => 'Status'
            ),
            GROWL_NOTIFY_PHPERROR => array(
                'display' => 'Error-Log'
            )
        );
        $this->_growl_port = \Net_Growl::GNTP_PORT;
        $this->_trapLevels = E_USER_ERROR | E_USER_WARNING;
    }


    public function __destruct()
    {
        parent::__destruct();
    }


    /**
     * @param $host string hostname or ip of the growl server
     */
    public function setHost($host)
    {
        $this->_growl_host = $host;
    }

    /**
     * @param $timeout int seconds
     */
    public function setTimeout($timeout)
    {
        if (is_int($timeout)) {
            $this->_growl_timeout = $timeout;
        } else {
            throw new InvalidArgumentException("timeout must be an integer");
        }
    }

    /**
     * @param $password growl server password
     */
    public function setPassword($password)
    {
        $this->_growl_password = $password;
    }


    /**
     * Send the message to Growl
     *
     * @param string $message
     * @return ChannelObject|void
     * @throws \Net_Growl_Exception
     */
    public function write($message)
    {
        parent::write($message);
        if ($this->_enabled) {
            $growl_options = array(
                'host' => $this->_growl_host,
                'protocol' => $this->_growl_protocol,
                'port' => $this->_growl_port,
                'timeout' => $this->_growl_timeout
            );

            try
            {
                $growl = @\Net_Growl::singleton($this->_applicationName, $this->_growl_notifications, $this->_growl_password, $growl_options);
                $growl_name = GROWL_NOTIFY_STATUS;
                $growl->notify($growl_name, $this->_applicationName, $message, $growl_options);
                $growl = null;
            }
            catch (\Net_Growl_Exception $ex)
            {
                throw $ex;
            }
        }
        parent::written();
    }


}
