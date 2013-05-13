<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */


namespace Talkback\Comms;


use Psr\Log\InvalidArgumentException;

/**
 * Uses the Prowl API to send messages to the Prowl app
 * Has a rudimentary de-duper to stop you getting duplicate messages
 */
class CommsProwl extends ChannelObject
{

    /**
     * @var string Name of the Application
     */
    private $_applicationName;
    /**
     * @var array log of messages to prevent dupes
     */
    private static $_aMessages = array();
    /**
     * @var array up to 5 unique Prowl API keys
     */
    private $_aApiKey = array();
    /**
     * @var string an event name to send to Prowl
     */
    private $_eventName;


    public function __construct($appName, $apiKey)
    {
        parent::__construct();

        if (is_string($appName) && mb_strlen($appName, 'utf-8') < 255) {
            $this->_applicationName = trim($appName);
        } else {
            throw new InvalidArgumentException("Prowl appName must be a string, max 254 chars");
        }
        $this->addApiKey($apiKey);
   }


    public function __destruct()
    {
        parent::__destruct();
    }


    public function write($message)
    {
        parent::write($message);
        if (!in_array($message, self::$_aMessages) && $this->_enabled) {
            $this->sendProwlMessage($message);
            self::$_aMessages[] = $message;
        }
        parent::written();
    }


    /**
     * Send the message through the Prowl API
     * @param $message
     */
    private function sendProwlMessage($message)
    {
        // We can only send a message if we have at least on API key
        if (count($this->_aApiKey) == 0) {
            throw new CommsTargetException("Prowl requires you add at least one ApiKey");
        }
        // We need an eventName
        if (!isset($this->_eventName)) {
            throw new CommsTargetException("Prowl requires you set an eventName up to 1024 chars");
        }

        // Use \Prowl\SecureConnector to make cUrl use SSL
        $oProwl = new \Prowl\Connector();

        $oMsg = new \Prowl\Message();

        try {

            // You can choose to pass a callback
            $oProwl->setFilterCallback(function($sText) {
                return $sText;
            });

            $oProwl->setIsPostRequest(true);
            $oMsg->setPriority(0);

            foreach ($this->_aApiKey AS $apiKey) {
                $oMsg->addApiKey($apiKey);
            }
            $oMsg->setEvent($this->_eventName);
            $oMsg->setDescription($message);
            $oMsg->setApplication($this->_applicationName);

            $oResponse = $oProwl->push($oMsg);

            if ($oResponse->isError()) {
                Debug::log($oResponse->getErrorAsString(), Debug::NOTICE);
            }

        } catch (\InvalidArgumentException $oIAE) {
            throw new InvalidArgumentException ($oIAE->getMessage());
        } catch (\OutOfRangeException $oOORE) {
            throw new CommsTargetException($oOORE->getMessage());
        }
    }


    /**
     * You can add up to 5 unique Prowl API keys
     *
     * @link https://www.prowlapp.com/register.php
     * @param $apiKey string
     */
    public function addApiKey($apiKey)
    {
        if (count($this->_aApiKey) < 5 && !in_array($apiKey, $this->_aApiKey)) {
            $this->_aApiKey[] = $apiKey;
        }
    }


    /**
     * @param $eventName string
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function setEventName($eventName)
    {
        if (is_string($eventName)) {
            $eventName = trim($eventName);
            $iContentLength = mb_strlen($eventName, 'utf-8');

            if ($iContentLength > 1024) {
                throw new InvalidArgumentException('eventName length is limited to 1024 chars. Yours is ' . $iContentLength);
            }
            $this->_eventName = $eventName;
        } else {
            throw new InvalidArgumentException("eventName must be a string, max length 1024 chars");
        }
    }

}
