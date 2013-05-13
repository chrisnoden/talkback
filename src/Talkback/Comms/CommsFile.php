<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */


namespace Talkback\Comms;


use Psr\Log\InvalidArgumentException;

/**
 * Open a file so that we can write to it through our Comms
 * methods. Typically a CommsFile object is used as the place for project logs
 *
 * example:
 * $filelog = Comms::File('logs/hello.log');
 * $filelog->error()->write("This is a log message set at error level");
 */
class CommsFile extends CommsBase
{
    /**
     * @var string
     */
    protected $_filename;
    /**
     * @var resource
     */
    protected $_fh;
    /**
     * @var bool
     */
    protected $_bAddTimestamp = false;


    public function __construct()
    {
        parent::__construct();
    }


    public function __destruct()
    {
        $this->closeFH();
        parent::__destruct();
    }


    /**
     * Opens the file handler for append
     * @throws CommsTargetException
     */
    protected function openFH()
    {
        if (!is_resource($this->_fh)) {
            if (isset($this->_filename)) {
                $fh = @fopen($this->_filename, 'a');
                if (is_resource($fh)) {
                    $this->_fh = $fh;
                } else {
                    throw new CommsTargetException(sprintf("Invalid filename, unable to open for append (%s)", $this->_filename));
                }
            } else {
                throw new CommsTargetException(sprintf("Invalid filename: %s", $this->_filename));
            }
        }
    }


    /**
     * Closes the file handler
     */
    protected function closeFH()
    {
        if (is_resource($this->_fh)) {
            @fclose($this->_fh);
            $this->_fh = null;
        }
    }


    /**
     * @param $filename
     *
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function setFilename($filename)
    {
        // close any open file resource before changing the filename
        $this->closeFH();

        // check the filename is valid before setting
        if (is_string($filename) && substr($filename, 0, 1) == DIRECTORY_SEPARATOR && is_dir(dirname($filename)) && is_writable(dirname($filename))) {
            $filename = trim($filename);

            // split out the parts of the filename
            $parts = pathinfo($filename);

            // clean the filename
            $filename = $parts['dirname'] . DIRECTORY_SEPARATOR . preg_replace("/[^A-Za-z0-9+]/", '_', $parts['filename']);
            if (isset($parts['extension']) && strlen($parts['extension']) > 0) {
                $filename .= '.'.$parts['extension'];
            }

            $this->_filename = $filename;
        } else {
            throw new InvalidArgumentException("filename must be an absolute filename in a writeable directory");
        }

    }


    /**
     * @param $msg string string to add to the file
     */
    public function write($msg)
    {
        parent::write($msg);
        if ($this->_enabled) {
            $this->openFH();
            if ($this->_bAddTimestamp) {
                $this->addField('timestamp', date('Y/m/d H:i:s'));
            }
            $msg = $this->prepareMessage($msg);
            $msg .= "\n";
            fputs($this->_fh, $msg, strlen($msg));
        }
        parent::written();
    }


    /**
     * @return CommsFile
     */
    public function addTimestamp()
    {
        $this->addField('timestamp', date('Y/m/d H:i:s'));
        $this->_bAddTimestamp = true;
        return $this;
    }


}
