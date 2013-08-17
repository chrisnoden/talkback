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
 * Class ChannelInterface
 *
 * @category Interface
 * @package  talkback
 * @author   Chris Noden <chris.noden@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link     https://github.com/chrisnoden/synergy
 */
interface ChannelInterface
{

    /**
     * Sends the message to the chosen Communication channel
     *
     * @param $msg
     *
     * @return void
     */
    public function write($msg);


    /**
     * Enable output to the channel (default)
     *
     * @return ChannelAbstract
     */
    public function enable();


    /**
     * Disable output to the channel
     * NB - the channel can still choose to ignore this
     *
     * @return ChannelAbstract
     */
    public function disable();


    /**
     * @param $delimiter
     *
     * @return ChannelAbstract
     */
    public function setFieldDelimiter($delimiter);


    /**
     * The field matching the fieldName will show the fixed displayName before the value
     *
     * @param $fieldName
     * @param $displayName
     *
     * @return ChannelAbstract
     */
    public function showFieldName($fieldName, $displayName);


    /**
     * Add a field/context to our message
     *
     * @param $name
     *
     * @return ChannelAbstract
     * @throws \Exception
     */
    public function addField($name);


    /**
     * Replaces any defined fields/contexts with the values in the array
     *
     * @param array $aContexts
     */
    public function setFieldValues(array $aContexts);


    /**
     * Set the name of our Channel
     *
     * @param $name
     *
     * @return mixed
     */
    public function setName($name);
}
