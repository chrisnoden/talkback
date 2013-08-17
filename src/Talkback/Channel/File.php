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

use Talkback\Exception\InvalidArgumentException;
use Talkback\Exception\ChannelTargetException;

/**
 * Class File
 * Open a file so that we can write to it through our ChannelFactory
 * methods. Typically a File object is used as the place for project logs
 *
 * example:
 * $filelog = ChannelFactory::File('logs/hello.log');
 * $filelog->error()->write("This is a log message set at error level");
 *
 * @category Channel\File
 * @package  talkback
 * @author   Chris Noden <chris.noden@gmail.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link     https://github.com/chrisnoden/synergy
 */
class File extends ChannelAbstract implements ChannelInterface
{

    /**
     * @var string
     */
    protected $filename;
    /**
     * @var resource
     */
    protected $fh;


    public function __construct()
    {
        // default to add the timestamp field
        $this->addTimestamp();
        $this->setFieldDelimiter(' ');
    }


    public function __destruct()
    {
        $this->closeFH();
    }


    /**
     * Opens the file handler for append
     *
     * @throws ChannelTargetException
     */
    protected function openFH()
    {
        if (!is_resource($this->fh)) {
            if (isset($this->filename)) {
                $fh = @fopen($this->filename, 'a');
                if (is_resource($fh)) {
                    $this->fh = $fh;
                } else {
                    throw new ChannelTargetException(sprintf("Invalid filename, unable to open for append (%s)", $this->filename));
                }
            } else {
                throw new ChannelTargetException(sprintf("Invalid filename: %s", $this->filename));
            }
        }
    }


    /**
     * Closes the file handler
     */
    protected function closeFH()
    {
        if (is_resource($this->fh)) {
            @fclose($this->fh);
            $this->fh = null;
        }
    }


    /**
     * @param $filename
     *
     * @throws \Talkback\Exception\InvalidArgumentException
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
                $filename .= '.' . $parts['extension'];
            }

            $this->filename = $filename;
        } else {
            throw new InvalidArgumentException("filename must be an absolute filename in a writeable directory");
        }

    }


    /**
     * @param $msg string string to add to the file
     */
    public function write($msg)
    {
        if ($this->enabled) {
            $this->openFH();
            $msg = $this->prepareMessage($msg);
            $msg .= "\n";
            fputs($this->fh, $msg, strlen($msg));
        }
        parent::written();
    }


}
