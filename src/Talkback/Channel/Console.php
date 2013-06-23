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

/**
 * Class Console
 * Outputs to the console (StdOut)
 *
 * @category Talkback\Channel
 * @package  talkback
 * @author   Chris Noden <chris.noden@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link     https://github.com/chrisnoden/synergy
 */
class Console extends ChannelObject
{

    /**
     * @var bool use the STDOUT and similar streams
     */
    private $_useStream = true;


    public function __construct()
    {
        parent::__construct();
        // If this class is used for Debug logging then these are useful defaults - otherwise they'll likely be ignored
        $this->_fieldDelimiter = ' ';
        $this->_aFieldTitles = array('linenum' => 'line:');

        if (!class_exists('\cli\Streams')) {
            $this->_useStream = false;
        }
    }


    /**
     * Modify the message before we output it
     *
     * @param       $msg
     * @param array $aSkipFields
     *
     * @return string
     */
    protected function prepareMessage($msg, $aSkipFields = array())
    {
        $msg = parent::prepareMessage($msg, $aSkipFields);

        if (!is_null($this->_level)) {
            if ($this->_useStream && !defined('PHP_WINDOWS_VERSION_MAJOR')) {
                $msg = sprintf(
                    '%%R%s%%n:%s', $this->_level, $msg
                );
            } else {
                $msg = sprintf(
                    '%s:%s', $this->_level, $msg
                );
            }
        }

        return $msg;
    }


    /**
     * Write the output to the console
     *
     * @param $msg string output the string
     *
     * @return ChannelObject
     */
    public function write($msg)
    {
        parent::write($msg);
        $msg = $this->prepareMessage($msg);

        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            printf('%s'.PHP_EOL, $msg);
        } else if ($this->_useStream && !is_null($this->_level)) {
            \cli\line($msg);
        } else {
            print($msg.PHP_EOL);
        }

        parent::written();
    }


    /**
     * Set the value of _useStream member
     *
     * @param boolean $useStream
     *
     * @return void
     */
    public function setUseStream($useStream)
    {
        if ($useStream == true && class_exists('\cli\Streams')) {
            $this->_useStream = true;
        } else {
            $this->_useStream = false;
        }
    }


}
