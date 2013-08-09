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
 * @package   talkback
 * @author    Chris Noden <chris.noden@gmail.com>
 * @copyright 2013 Chris Noden
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link      https://github.com/chrisnoden
 */

require('../vendor/autoload.php');

/**
 * Create a Talkback collection
 *
 * A File channel outputs all events to /tmp/test.log
 * A Basic channel will output CRITICAL events to console
 * A Growl channel will show all INFO events
 */
$logger = \Talkback\Logger::getLogger('complex logger');
$logger
    ->addChannel(
        array(
            \Psr\Log\LogLevel::ERROR,
            \Psr\Log\LogLevel::CRITICAL,
            \Psr\Log\LogLevel::ALERT,
            \Psr\Log\LogLevel::EMERGENCY,
            \Psr\Log\LogLevel::INFO,
            \Psr\Log\LogLevel::NOTICE,
            \Psr\Log\LogLevel::WARNING,
            \Psr\Log\LogLevel::DEBUG
        ),
        \Talkback\Channel\ChannelFactory::File('/tmp/test.log')
    )
    ->addChannel(\Psr\Log\LogLevel::CRITICAL, \Talkback\Channel\ChannelFactory::Basic())
    ->addChannel(\Psr\Log\LogLevel::INFO, \Talkback\Channel\ChannelFactory::Growl('Bundle'));

/**
 * Send an INFO event which will appear in the File log and on Growl
 */
$logger->info('My goodness this is good information');

// dump the log file to the console
$file = file_get_contents('/tmp/test.log');
echo "Your file contains:".PHP_EOL.$file;

// clean up
unlink('/tmp/test.log');
