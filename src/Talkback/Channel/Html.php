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
 * Class Growl
 * This class implements HTML logging
 * storing the messages and outputting them after your script has terminated
 *
 * @category Channel\Growl
 * @package  talkback
 * @author   Chris Noden <chris.noden@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link     https://github.com/chrisnoden/synergy
 */
class Html extends ChannelAbstract implements ChannelInterface
{

    /**
     * @var bool a basic Http Client that doesn't like HTML (eg HTTP Client on Mac App Store)
     */
    protected $isBasicHttpClient = false;
    /**
     * @var array cache of messages to output at the end of the HTML block
     */
    private $aCachedMessages = array();
    /**
     * @var bool
     */
    private $topBlockDone = false;
    /**
     * @var bool
     */
    private $endBlockDone = false;


    public function __destruct()
    {
        if (count($this->aCachedMessages) > 0) {
            $this->flush();
        }
    }


    /**
     * Output our HTML 'top block'
     *
     * @return void
     */
    private function outputTopBlock()
    {
        if (!$this->topBlockDone) {
            if (headers_sent()) {
                echo "\n\n<hr/>\n\n";
            } else {
                header('X-Logging-By: chrisnoden/Talkback');
            }

            echo "<table style='font-size: small;' border='1' cellspacing='0' cellpadding='2'>\n";

            // Display our column headings
            echo "<thead style='font-weight: bold;'>";
            foreach ($this->aFields as $fieldTitle => $fieldDefaultValue) {
                printf("<th>%s</th>", $fieldTitle);
            }
            printf("<th>message</th></thead>\n");

            $this->topBlockDone = true;
        }
    }


    /**
     * output the end block
     *
     * @return void
     */
    private function outputEndBlock()
    {
        if (!$this->endBlockDone) {
            echo "</table>\n\n\n";
            $this->endBlockDone = true;
        }
    }


    /**
     * Output the messages in the arrayg
     *
     * @param $aMsg array of messages
     *
     * @return void
     */
    private function outputMessage($aMsg)
    {
        printf("<tr>");
        foreach ($this->aFields as $fieldTitle => $fieldDefaultValue) {
            if (isset($aMsg[$fieldTitle])) {
                printf("<td valign=\"top\">%s</td>", $aMsg[$fieldTitle]);
            } else {
                printf("<td valign=\"top\"></td>");
            }
        }
        printf("<td valign=\"top\"><pre>%s</pre></td></tr>\n", $aMsg['message']);
    }


    /**
     * Output the string to the HTML channel
     *
     * @param $msg string output the string
     *
     * @return ChannelAbstract
     */
    public function write($msg)
    {
        if ($this->isBasicHttpClient) {
            $msg = $this->prepareMessage($msg);
            printf("%s\n", $msg);
        } else {
            $arr = array();
            if ($this->bAddTimestamp) {
                $arr['timestamp'] = date('Y/m/d H:i:s');
            }
            foreach ($this->aMessageFields as $pfxName => $pfxValue) {
                if ($pfxName == 'timestamp') {
                    continue;
                } else {
                    $arr[$pfxName] = $pfxValue;
                }
            }
            $arr['message']          = $msg;
            $this->aCachedMessages[] = $arr;
        }
        parent::written();
    }


    /**
     * Stops all the HTML markup being sent to the client
     * useful for simple HTTP clients
     *
     * @return Html
     */
    public function setBasicClient()
    {
        $this->isBasicHttpClient = true;
        // If this class is used for Debug logging then these are useful defaults - otherwise they'll likely be ignored
        $this->fieldDelimiter = ' ';
        $this->aFieldTitles   = array('linenum' => 'line:');
        return $this;
    }


    /**
     * Output everything that's been stored
     *
     * @return void
     */
    public function flush()
    {
        $this->outputTopBlock();

        // Output our messages
        foreach ($this->aCachedMessages as $aMsg) {
            $this->outputMessage($aMsg);
        }

        // empty our cache
        $this->aCachedMessages = array();

        $this->outputEndBlock();
    }

}
