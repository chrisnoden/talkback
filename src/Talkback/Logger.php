<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 * 
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback;

class Logger extends Singleton
{

    /**
     * @var array
     */
    private static $_aLoggers = array();


    /**
     * @param string $loggerName
     * @return Router
     */
    public static function getLogger($loggerName = 'my logger')
    {
        if (isset(self::$_aLoggers[$loggerName])) {
            return self::$_aLoggers[$loggerName];
        }

        $obj = new Router();
        $obj->setName($loggerName);

        self::$_aLoggers[$loggerName] = $obj;
        return $obj;
    }


}
