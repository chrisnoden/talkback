<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Channel;

use Talkback\Exception\ChannelException;
use Talkback\Exception\InvalidArgumentException;

/** @noinspection PhpDocSignatureInspection */
class ChannelFactory
{

    /**
     * A fall-back channel which just outputs to console/stdout/browser
     *
     * @return ChannelObject
     * @throws \Exception
     */
    public static function Basic()
    {
        try {
            if (PHP_SAPI == 'cli') {
                $oComms = new Console();
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
     * @param $filename string filename where the file will be created or appended
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
     * @static
     * @param $appName string
     * @param $apiKey string You can add up to 5 Prowl Api Keys - but need at least 1
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