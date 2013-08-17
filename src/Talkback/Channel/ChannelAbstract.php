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
use Psr\Log\LogLevel;
use Talkback\Object;

/**
 * Class ChannelObject
 * This is the root Object for all ChannelFactory classes
 * They should all inherit this class and build
 *
 * @category Talkback\Channel
 * @package  talkback
 * @author   Chris Noden <chris.noden@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link     https://github.com/chrisnoden/synergy
 */
abstract class ChannelAbstract extends Object
{

    /**
     * @var string Our messaging level (defaults to INFO)
     */
    protected $level;
    /**
     * @var string
     */
    protected $fieldDelimiter = ':';
    /**
     * @var bool has output writing been disabled
     */
    protected $enabled = true;
    /**
     * The template fields
     *
     * @var array
     */
    protected $aFields = array();
    /**
     * These are the fields we actually drop into our message
     *
     * @var array
     */
    protected $aMessageFields = array();
    /**
     * Fields that we will include a title in the output
     *
     * @var array associative array of field name (key) with the text to output
     * @example array('time' => 'time=');
     */
    protected $aFieldTitles = array();
    /**
     * @var bool
     */
    protected $bAddTimestamp = false;
    /**
     * @var int maximum length of our name
     */
    protected $maxNameLength = 30;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var bool is the channel fully functional
     */
    protected $functional = true;


    /**
     * Write the string to the Channel
     *
     * @param $msg string output the string
     */
    abstract public function write($msg);


    /**
     * Called after the message has been written
     * Resets the fields array
     */
    protected function written()
    {
        $this->aMessageFields = $this->aFields;
        $this->level          = null;
    }


    /**
     * Prepares the message for writing out to the channel
     *
     * @param $msg
     * @param $aSkipFields array of field names to exclude from message
     *
     * @return string
     */
    protected function prepareMessage($msg, $aSkipFields = array())
    {
        if ($this->bAddTimestamp) {
            $this->setFieldValue('timestamp', date('Y/m/d H:i:s'));
        }
        if (count($this->aMessageFields) > 0) {
            $preMsg = '';
            foreach ($this->aMessageFields as $fieldName => $fieldValue) {
                if (in_array($fieldName, $aSkipFields)) {
                    continue;
                }
                if (isset($this->aFieldTitles[$fieldName])) {
                    $title = $this->aFieldTitles[$fieldName];
                } else {
                    $title = '';
                }
                $preMsg .= $title . $fieldValue . $this->fieldDelimiter;
            }
            $msg = sprintf("%s%s", $preMsg, $msg);
        }

        return $msg;
    }


    /**
     * mainly used for logging/debugging - sets the log level
     *
     * @param $level string one of \Psr\Log\LogLevel constants
     *
     * @throws \Talkback\Exception\InvalidArgumentException
     */
    public function setLevel($level)
    {
        $t       = new LogLevel();
        $r       = new \ReflectionObject($t);
        $aLevels = $r->getConstants();
        $level   = strtolower($level);
        if (!in_array($level, $aLevels)) {
            throw new InvalidArgumentException('setLevel($level) must be set with a \Psr\Log\LogLevel const value');
        }
        $this->level = $level;
    }


    /**
     * Replaces any defined fields/contexts with the values in the array
     *
     * @param array $aContexts
     */
    public function setFieldValues(array $aContexts)
    {
        foreach ($aContexts as $fieldName => $fieldValue) {
            if (!isset($this->aFields[$fieldName])) {
                $this->aFields[$fieldName] = false;
            }
        }
        $this->aMessageFields = array_replace($this->aFields, $aContexts);
    }


    /**
     * Add a field/context to our message
     *
     * @param $name
     *
     * @return ChannelAbstract
     * @throws \Talkback\Exception\InvalidArgumentException
     */
    public function addField($name)
    {
        if (is_string($name)) {
            $name                 = trim($name);
            $this->aFields[$name] = false;
        } else {
            throw new InvalidArgumentException("field name must be a string");
        }
        return $this;
    }


    /**
     * Set the value of the field for the next message/s
     *
     * @param $fieldName
     * @param $value
     */
    public function setFieldValue($fieldName, $value)
    {
        if (isset($this->aFields[$fieldName])) {
            $this->aMessageFields[$fieldName] = $value;
        }
    }


    /**
     * Return the fields we currently have set
     * Which will be an array of keys with the value set to null
     *
     * @return array
     */
    public function getFields()
    {
        return $this->aFields;
    }


    /**
     * The field matching the fieldName will show the fixed displayName before the value
     *
     * @param $fieldName
     * @param $displayName
     *
     * @return ChannelAbstract
     */
    public function showFieldName($fieldName, $displayName)
    {
        $this->aFieldTitles[$fieldName] = $displayName;
        return $this;
    }


    /**
     * Disable output to the channel
     * NB - the channel can still choose to ignore this
     *
     * @return ChannelAbstract
     */
    public function disable()
    {
        $this->enabled = false;
        return $this;
    }


    /**
     * Enable output to the channel (default)
     *
     * @return ChannelAbstract
     */
    public function enable()
    {
        $this->enabled = true;
        return $this;
    }


    /**
     * @param $delimiter
     *
     * @return ChannelAbstract
     */
    public function setFieldDelimiter($delimiter)
    {
        if (is_string($delimiter)) {
            $this->fieldDelimiter = $delimiter;
        }
        return $this;
    }


    /**
     * @return File
     */
    public function addTimestamp()
    {
        $this->bAddTimestamp = true;
        $this->addField('timestamp');
        return $this;
    }


    /**
     * @param $name
     *
     * @return $this
     * @throws \Talkback\Exception\InvalidArgumentException
     */
    public function setName($name)
    {
        if (is_string($name) && mb_strlen($name, 'utf-8') <= $this->maxNameLength) {
            $name = ucwords($name);
            /** @noinspection PhpUndefinedFieldInspection */
            $this->name = preg_replace('/[^0-9a-zA-Z]/', '', $name);
        } else {
            throw new InvalidArgumentException("Syslog name must be a string, max {$this->maxNameLength} chars");
        }

        return $this;
    }


    /**
     * If this channel functional
     * The channel handler will disable itself if any critical problem arises
     *
     * @return boolean is the channel functional
     */
    public function getFunctional()
    {
        return $this->functional;
    }

}
