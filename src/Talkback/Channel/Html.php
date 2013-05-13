<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */


namespace Talkback\Channel;


/**
 * This class implements HTML logging
 * storing the messages and outputting them after your script has terminated
 */
class Html extends ChannelObject
{

    /**
     * @var bool a basic Http Client that doesn't like HTML (eg HTTP Client on Mac App Store)
     */
    protected $_isBasicHttpClient = false;
    /**
     * @var array cache of messages to output at the end of the HTML block
     */
    private $_aCachedMessages = array();


    public function __construct()
    {
        parent::__construct();
    }


    public function __destruct()
    {
        if (count($this->_aCachedMessages) > 0) {
            $this->dumpHtmlMessages();
        }
        parent::__destruct();
    }


    /**
     * Outputs all the cached messages with HTML markup
     */
    private function dumpHtmlMessages()
    {
//        if (headers_sent()) {
            echo "\n\n<hr/>\n\n";
//        } else {
//            if (Config::about('url')) header('X-Powered-By: '. (string)Config::about('url'));
//        }

        echo "<table style='font-size: small;' border='1' cellspacing='0' cellpadding='2'>";

        // Display our column headings
        echo "<thead style='font-weight: bold;'>";
        printf("<td valign=\"top\">time</td>");
        foreach ($this->_aFields AS $fieldTitle=>$fieldDefaultValue)
        {
            if ($fieldTitle == 'time') continue;
            printf("<td valign=\"top\">%s</td>", $fieldTitle);
        }
        printf("<td valign=\"top\">message</td>\n");

        // Output our messages
        foreach ($this->_aCachedMessages AS $aMsg)
        {
            printf("<tr><td valign=\"top\">%s</td>", date("H:i:s", strtotime($aMsg['time'])));
            foreach ($this->_aFields AS $fieldTitle=>$fieldDefaultValue)
            {
                if ($fieldTitle == 'time') continue;
                if (isset($aMsg[$fieldTitle])) {
                    printf("<td valign=\"top\">%s</td>", $aMsg[$fieldTitle]);
                } else {
                    printf("<td valign=\"top\"></td>");
                }
            }
            printf("<td valign=\"top\"><pre>%s</pre></td>\n</tr>", $aMsg['message']);
        }

        echo "</table>\n\n\n";

    }


    /**
     * @param $msg string output the string
     * @return ChannelObject
     */
    public function write($msg)
    {
        parent::write($msg);
        if ($this->_isBasicHttpClient) {
            $msg = $this->prepareMessage($msg);
            printf("%s\n", $msg);
        } else {
            $arr = array();
            foreach ($this->_aMessageFields AS $pfxName=>$pfxValue)
            {
                $arr[$pfxName] = $pfxValue;
            }
            $arr['message'] = $msg;
            $this->_aCachedMessages[] = $arr;
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
        $this->_isBasicHttpClient = true;
        // If this class is used for Debug logging then these are useful defaults - otherwise they'll likely be ignored
        $this->_fieldDelimiter = ' ';
        $this->_aFieldTitles = array('linenum' => 'line:');
        return $this;
    }

}
