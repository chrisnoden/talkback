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

use Talkback\Exception\ChannelException;
use Talkback\Exception\InvalidArgumentException;

/**
 * Class ChannelFactory
 * Create and return a Channel object
 *
 * @category Talkback\Channel
 * @package  talkback
 * @author   Chris Noden <chris.noden@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link     https://github.com/chrisnoden/synergy
 */
/** @noinspection PhpDocSignatureInspection */
class ChannelFactory
{

    /**
     * A fall-back channel which just outputs to console/stderr/browser
     *
     * @static
     * @return ChannelObject
     * @throws \Exception
     */
    public static function Basic()
    {
        try {
            if (PHP_SAPI == 'cli') {
                if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
                    $oComms = new Console();
                } else {
                    $oComms = new StdErr();
                }
                return $oComms;
            } else {
                $oComms = new Html();
                if (isset($_SERVER['HTTP_USER_AGENT']) && substr($_SERVER['HTTP_USER_AGENT'], 0, 13) == 'HTTP%20Client') {
                    ini_set('html_errors', 0); // disable HTML markup in errors (works on xDebug too)
                    $oComms->setBasicClient();
                }
                return $oComms;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * Outputs to Console (falls back to just echo'ing on Windows)
     *
     * @static
     * @return Console
     * @throws \Exception
     */
    public static function Console()
    {
        try {
            $oComms = new Console();
            return $oComms;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * Outputs to StdErr (falls back to just echo'ing on Windows)
     *
     * @static
     * @return StdErr
     * @throws \Exception
     */
    public static function StdErr()
    {
        try {
            $oComms = new StdErr();
            return $oComms;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * @param $filename string filename where the file will be created or appended
     *
     * @static
     * @return File
     * @throws \Exception
     */
    public static function File($filename)
    {
        try {
            $oComms = new File();
            $oComms->setFilename($filename);
            return $oComms;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * @param $appName string Application name used by Growl
     * @static
     * @return Growl
     * @throws \Exception
     */
    public static function Growl($appName)
    {
        try {
            $oComms = new Growl($appName);
            return $oComms;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * @static
     * @return Syslog
     * @throws \Talkback\Exception\InvalidArgumentException
     */
    public static function Syslog($name)
    {
        try {
            $oComms = new Syslog($name);
            return $oComms;
        } catch (InvalidArgumentException $ex) {
            throw $ex;
        }
    }


    /**
     * @param $appName string
     * @param $apiKey string You can add up to 5 Prowl Api Keys - but need at least 1
     *
     * @static
     * @return Prowl
     * @throws \Exception
     */
    public static function Prowl($appName, $apiKey)
    {
        if (!class_exists('Prowl\Message')) {
            throw new ChannelException('Prowl library not installed');
        }
        try {
            $oComms = new Prowl($appName, $apiKey);
            return $oComms;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}