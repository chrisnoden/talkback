<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */


namespace Talkback\Channel;


/**
 * This class also implements basic console logging
 */
class Console extends ChannelObject
{


    public function __construct()
    {
        parent::__construct();
        // If this class is used for Debug logging then these are useful defaults - otherwise they'll likely be ignored
        $this->_fieldDelimiter = ' ';
        $this->_aFieldTitles = array('linenum' => 'line:');
    }


    public function __destruct()
    {
        parent::__destruct();
    }


    /**
     * @param $msg string output the string
     * @return ChannelObject
     */
    public function write($msg)
    {
        parent::write($msg);
        $msg = $this->prepareMessage($msg);
        printf("%s\n", $msg);
        parent::written();
    }


}
