<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Comms;


class Comms
{

    /**
     * A fall-back channel which just outputs to console/stdout/browser
     *
     * @return CommsBase
     * @throws \Exception
     */
    public static function Basic()
    {
        try {
            if (PHP_SAPI == 'cli') {
                $oComms = new CommsConsole();
                return $oComms;
            } else {
                $oComms = new CommsHTML();
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
     * @param $filename filename where the file will be created or appended
     * @static
     * @return CommsFile
     * @throws \Exception
     */
    public static function File($filename)
    {
        try {
            $oComms = new CommsFile();
            $oComms->setFilename($filename);
            return $oComms;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * @param $appName Application name used by Growl
     * @static
     * @return CommsGrowl
     * @throws \Exception
     */
    public static function Growl($appName)
    {
        try {
            $oComms = new CommsGrowl($appName);
            return $oComms;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * @static
     * @return CommsSyslog
     * @throws SalException
     */
    public static function Syslog($name)
    {
        try {
            $oComms = new CommsSyslog($name);
            return $oComms;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * @static
     * @param $appName
     * @param $apiKey You can add up to 5 Prowl Api Keys - but need at least 1
     * @return CommsProwl
     * @throws \Exception
     */
    public static function Prowl($appName, $apiKey)
    {
        try {
            $oComms = new CommsProwl($appName, $apiKey);
            return $oComms;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}