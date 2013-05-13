<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Channel;

use Psr\Log\InvalidArgumentException;
use Talkback\Object;

/**
 * This is the root Object for all ChannelLauncher classes
 * They should all inherit this class and build
 */
class ChannelObject extends Object implements ChannelInterface
{

    /**
     * @var string Our messaging level (defaults to INFO)
     */
    protected $_level;
    /**
     * @var string
     */
    protected $_fieldDelimiter = ':';
    /**
     * @var bool has output writing been disabled
     */
    protected $_enabled = true;
    /**
     * The template fields
     *
     * @var array
     */
    protected $_aFields = array();
    /**
     * These are the fields we actually drop into our message
     *
     * @var array
     */
    protected $_aMessageFields = array();
    /**
     * Fields that we will include a title in the output
     *
     * @var array associative array of field name (key) with the text to output
     * @example array('time' => 'time=');
     */
    protected $_aFieldTitles = array();


    public function __construct()
    {
        parent::__construct();
    }


    public function __destruct()
    {
        parent::__destruct();
    }


    /**
     * @param $msg string output the string
     */
    public function write($msg)
    {
    }


    /**
     * Called after the message has been written
     * Resets the fields array
     */
    protected function written()
    {
        $this->_aMessageFields = $this->_aFields;
    }


    /**
     * Prepares the message for writing out to the channel
     * @param $msg
     * @param $aSkipFields array of field names to exclude from message
     * @return string
     */
    protected function prepareMessage($msg, $aSkipFields = array())
    {
        if (count($this->_aMessageFields) > 0) {
            $preMsg = '';
            foreach ($this->_aMessageFields AS $fieldName=>$fieldValue)
            {
                if (in_array($fieldName, $aSkipFields)) continue;
                if (isset($this->_aFieldTitles[$fieldName])) {
                    $title = $this->_aFieldTitles[$fieldName];
                } else {
                    $title = '';
                }
                $preMsg .= $title.$fieldValue.$this->_fieldDelimiter;
            }
            $msg = sprintf("%s%s", $preMsg, $msg);
        }

        return $msg;
    }


    /**
     * mainly used for logging/debugging - sets the log level
     *
     * @param $level
     */
    public function setLevel($level)
    {
        if (is_string($level)) {
            $this->_level = strtolower($level);
        } else if (is_int($level)) {
            switch ($level) {
                case 1:
                    $this->_level = 'debug';
                    break;

                case 2:
                    $this->_level = 'info';
                    break;

                case 4:
                    $this->_level = 'notice';
                    break;

                case 8:
                    $this->_level = 'warn';
                    break;

                case 16:
                    $this->_level = 'error';
                    break;

                case 32:
                    $this->_level = 'fatal';
                    break;
            }
        }
    }


    /**
     * Replaces any defined fields/contexts with the values in the array
     *
     * @param array $aContexts
     */
    public function setFieldValues(array $aContexts)
    {
        foreach ($aContexts AS $fieldName=>$fieldValue)
        {
            if (!isset($this->_aFields[$fieldName])) {
                $this->_aFields[$fieldName] = false;
            }
        }
        $this->_aMessageFields = array_replace($this->_aFields, $aContexts);
    }


    /**
     * Add a field/context to our message
     *
     * @param $name
     * @return ChannelObject
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function addField($name)
    {
        if (is_string($name)) {
            $name = trim($name);
            $this->_aFields[$name] = false;
        } else {
            throw new InvalidArgumentException("field name must be a string");
        }
        return $this;
    }


    /**
     * Return the fields we currently have set
     * Which will be an array of keys with the value set to null
     *
     * @return array
     */
    public function getFields()
    {
        return $this->_aFields;
    }


    /**
     * The field matching the fieldName will show the fixed displayName before the value
     *
     * @param $fieldName
     * @param $displayName
     * @return ChannelObject
     */
    public function showFieldName($fieldName, $displayName)
    {
        $this->_aFieldTitles[$fieldName] = $displayName;
        return $this;
    }


    /**
     * Disable output to the channel
     * NB - the channel can still choose to ignore this
     *
     * @return ChannelObject
     */
    public function disable()
    {
        $this->_enabled = false;
        return $this;
    }


    /**
     * Enable output to the channel (default)
     *
     * @return ChannelObject
     */
    public function enable()
    {
        $this->_enabled = true;
        return $this;
    }


    /**
     * @param $delimiter
     * @return ChannelObject
     */
    public function setFieldDelimiter($delimiter)
    {
        if (is_string($delimiter)) {
            $this->_fieldDelimiter = $delimiter;
        }
        return $this;
    }


}
