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
 * @category  File
 * @package   Talkback
 * @author    Chris Noden <chris.noden@gmail.com>
 * @copyright 2013 Chris Noden
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link      https://github.com/chrisnoden
 */

namespace Talkback\Channel;

/**
 * Class StdErr
 *
 * @category Talkback\Channel
 * @package  talkback
 * @author   Chris Noden <chris.noden@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link     https://github.com/chrisnoden/synergy
 */
class StdErr extends ChannelAbstract implements ChannelInterface
{

    public function __construct()
    {
        // If this class is used for Debug logging then these are useful defaults - otherwise they'll likely be ignored
        $this->fieldDelimiter = ' ';
        $this->aFieldTitles   = array('linenum' => 'line:');
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

        if (!is_null($this->level)) {
            $msg = sprintf(
                '%s:%s',
                $this->level,
                $msg
            );
        }

        return $msg;
    }


    /**
     * Output the message
     *
     * @param $msg string output the string
     *
     * @return ChannelAbstract
     */
    public function write($msg)
    {
        $msg = $this->prepareMessage($msg);

        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            printf('%s' . PHP_EOL, $msg);
        } else {
            if (class_exists('\cli\Streams')) {
                \cli\err($msg);
            } else {
                fwrite(STDERR, $msg . PHP_EOL);
            }
        }

        parent::written();
    }

}
