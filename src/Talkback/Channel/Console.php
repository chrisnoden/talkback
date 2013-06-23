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
 * This class also implements basic console logging
 */
class Console extends ChannelObject
{


    public function __construct()
    {
        parent::__construct();
        // If this class is used for Debug logging then these are useful defaults - otherwise they'll likely be ignored
        $this->_fieldDelimiter = ' ';
        $this->_aFieldTitles = array('linenum' => 'line:');
    }


    /**
     * @param $msg string output the string
     * @return ChannelObject
     */
    public function write($msg)
    {
        parent::write($msg);
        $msg = $this->prepareMessage($msg);
        printf("%s\n", $msg);
        parent::written();
    }


}
