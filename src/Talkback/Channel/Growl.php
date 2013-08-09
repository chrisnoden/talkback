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
 *
 * @category Channel\Growl
 * @package  talkback
 * @author   Chris Noden <chris.noden@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link     https://github.com/chrisnoden/synergy
 */
class Growl extends ChannelObject implements ChannelInterface
{

    /**
     * @var string Name of the Application
     */
    private $applicationName;
    /**
     * @var string
     */
    private $growl_password = '';
    /**
     * @var string ip address or hostname of the growl server
     */
    private $growl_host = '127.0.0.1';
    /**
     * @var string growl protocol type
     */
    private $growl_protocol = 'tcp';
    /**
     * @var int growl port
     */
    private $growl_port;
    /**
     * @var int timeout for growl server connections
     */
    private $growl_timeout = 5;
    /**
     * @var array the different notification types available
     */
    private $growl_notifications = array();


    /**
     * @param $appName
     *
     * @throws \Talkback\Exception\InvalidArgumentException
     */
    public function __construct($appName)
    {
        parent::__construct();

        if (is_string($appName) && mb_strlen($appName, 'utf-8') > 0 && mb_strlen($appName, 'utf-8') < 50) {
            $this->applicationName = trim($appName);
        } else {
            throw new InvalidArgumentException("Growl appName must be a string, max 50 chars");
        }

        // Notification Type definitions
        if (!defined('GROWL_NOTIFY_STATUS')) {
            define('GROWL_NOTIFY_STATUS', 'GROWL_NOTIFY_STATUS');
        }
        if (!defined('GROWL_NOTIFY_PHPERROR')) {
            define('GROWL_NOTIFY_PHPERROR', 'GROWL_NOTIFY_PHPERROR');
        }
        $this->growl_notifications = array(
            GROWL_NOTIFY_STATUS => array(
                'display' => 'Status'
            ),
            GROWL_NOTIFY_PHPERROR => array(
                'display' => 'Error-Log'
            )
        );
        $this->growl_port = \Net_Growl::GNTP_PORT;
        $this->_trapLevels = E_USER_ERROR | E_USER_WARNING;
    }


    public function __destruct()
    {
        parent::__destruct();
    }


    /**
     * The Growl host
     *
     * @param $host string hostname or ip of the growl server
     *
     * @return void
     */
    public function setHost($host)
    {
        $this->growl_host = $host;
    }

    /**
     * Timeout connecting to Growl
     *
     * @param $timeout int seconds
     *
     * @throws \Talkback\Exception\InvalidArgumentException
     * @return void
     */
    public function setTimeout($timeout)
    {
        if (is_int($timeout)) {
            $this->growl_timeout = $timeout;
        } else {
            throw new InvalidArgumentException("timeout must be an integer");
        }
    }

    /**
     * Your Growl password
     *
     * @param $password growl server password
     *
     * @return void
     */
    public function setPassword($password)
    {
        $this->growl_password = $password;
    }


    /**
     * Send the message to Growl
     *
     * @param string $message
     *
     * @return ChannelObject|void
     * @throws \Net_Growl_Exception
     */
    public function write($message)
    {
        if ($this->functional) {
            parent::write($message);
            if ($this->_enabled) {
                $growl_options = array(
                    'host' => $this->growl_host,
                    'protocol' => $this->growl_protocol,
                    'port' => $this->growl_port,
                    'timeout' => $this->growl_timeout
                );

                try {
                    $growl = @\Net_Growl::singleton($this->applicationName, $this->growl_notifications, $this->growl_password, $growl_options);
                    $growl_name = GROWL_NOTIFY_STATUS;
                    $growl->notify($growl_name, $this->applicationName, $message, $growl_options);
                    $growl = null;
                } catch (\Net_Growl_Exception $ex) {
                    $this->functional = false;
                }
            }
            parent::written();
        }
    }

}
