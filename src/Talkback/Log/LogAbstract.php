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
 * @category  Log
 * @package   Talkback
 * @author    Chris Noden <chris.noden@gmail.com>
 * @copyright 2009-2013 Chris Noden
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link      https://github.com/chrisnoden
 */

namespace Talkback\Log;

use Psr\Log\AbstractLogger;
use Talkback\Exception\InvalidArgumentException;

/**
 * Class LoggerAbstract
 *
 * @category Talkback\Log
 * @package  talkback
 * @author   Chris Noden <chris.noden@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link     https://github.com/chrisnoden/synergy
 */
abstract class LogAbstract extends AbstractLogger
{

    /**
     * @var array
     */
    private $_aValidLogLevels = array();


    /**
     * @param null $filename optional filename (path + filename)
     * @throws \Talkback\Exception\InvalidArgumentException
     */
    public function __construct($filename = null)
    {
        /**
         * Populate our valid log levels by Reflecting on the
         * constants exposed in the Psr\Log\LogLevel class
         */
        $t = new LogLevel();
        $r = new \ReflectionObject($t);
        $this->_aValidLogLevels = $r->getConstants();

        // Set our filename
        if (!is_null($filename)) {
            if (file_exists($filename) && !is_writable($filename)) {
                $processUser = posix_getpwuid(posix_geteuid());
                throw new InvalidArgumentException(
                    'logfile must be writeable by user: '.$processUser['name']
                );
            }

            $this->_filename = $filename;
        }
    }


    /**
     * Tests the $level to ensure it's accepted
     *
     * @param $level
     * @return bool
     * @throws InvalidArgumentException
     */
    protected function isValidLogLevel($level)
    {
        if (!in_array($level, $this->_aValidLogLevels)) {
            $logLevels = implode(
                ', \\Talkback\\LogLevel::',
                $this->_aValidLogLevels
            );
            throw new InvalidArgumentException(
                'Invalid LogLevel ('.$level.', must be one of \Psr\Log\LogLevel::' . $logLevels
            );
        }

        return true;
    }

}
