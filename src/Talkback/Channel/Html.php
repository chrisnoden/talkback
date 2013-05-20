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
    /**
     * @var bool
     */
    private $_topBlockDone = false;
    /**
     * @var bool
     */
    private $_endBlockDone = false;


    public function __construct()
    {
        parent::__construct();
    }


    public function __destruct()
    {
        if (count($this->_aCachedMessages) > 0) {
            $this->flush();
        }
        parent::__destruct();
    }


    private function outputTopBlock()
    {
        if (!$this->_topBlockDone) {
    //        if (headers_sent()) {
            echo "\n\n<hr/>\n\n";
    //        } else {
    //            if (Config::about('url')) header('X-Powered-By: '. (string)Config::about('url'));
    //        }

            echo "<table style='font-size: small;' border='1' cellspacing='0' cellpadding='2'>\n";

            // Display our column headings
            echo "<thead style='font-weight: bold;'>";
            foreach ($this->_aFields AS $fieldTitle=>$fieldDefaultValue)
            {
                printf("<th>%s</th>", $fieldTitle);
            }
            printf("<th>message</th></thead>\n");

            $this->_topBlockDone = true;
        }
    }

    private function outputEndBlock()
    {
        if (!$this->_endBlockDone) {
            echo "</table>\n\n\n";
            $this->_endBlockDone = true;
        }
    }


    private function outputMessage($aMsg)
    {
        printf("<tr>");
        foreach ($this->_aFields AS $fieldTitle=>$fieldDefaultValue)
        {
            if (isset($aMsg[$fieldTitle])) {
                printf("<td valign=\"top\">%s</td>", $aMsg[$fieldTitle]);
            } else {
                printf("<td valign=\"top\"></td>");
            }
        }
        printf("<td valign=\"top\"><pre>%s</pre></td></tr>\n", $aMsg['message']);
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
            if ($this->_bAddTimestamp) {
                $arr['timestamp'] = date('Y/m/d H:i:s');
            }
            foreach ($this->_aMessageFields AS $pfxName=>$pfxValue)
            {
                if ($pfxName == 'timestamp') {
                    continue;
                } else {
                    $arr[$pfxName] = $pfxValue;
                }
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


    public function flush()
    {
        $this->outputTopBlock();

        // Output our messages
        foreach ($this->_aCachedMessages AS $aMsg)
        {
            $this->outputMessage($aMsg);
        }

        // empty our cache
        $this->_aCachedMessages = array();

        $this->outputEndBlock();
    }

}
