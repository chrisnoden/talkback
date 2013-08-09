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
 * @category  Talkback
 * @package   Talkback
 * @author    Chris Noden <chris.noden@gmail.com>
 * @copyright 2009-2013 Chris Noden
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link      https://github.com/chrisnoden
 */

namespace Talkback;

class Logger extends Singleton
{

    /**
     * @var array
     */
    private static $aLoggers = array();


    /**
     * @param string $loggerName
     * @return Router
     */
    public static function getLogger($loggerName = 'my logger')
    {
        if (isset(self::$aLoggers[$loggerName])) {
            return self::$aLoggers[$loggerName];
        }

        $obj = new Router();
        $obj->setName($loggerName);

        self::$aLoggers[$loggerName] = $obj;
        return $obj;
    }


}
