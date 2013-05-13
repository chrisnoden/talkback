<?php
/**
 * Created by JetBrains PhpStorm
 *
 * @copyright Copyright (c) 2011 to 2013, Chris Noden : @chrisnoden
 * @link      http://www.noden.net/sal
 * @author    Chris Noden, @chrisnoden
 *
 * A container for information about the log source file
 * The file that generated the log
 */

namespace Talkback\Insight;

use Talkback\Object;

class SourceFile extends Object
{
    /**
     * @var string absolute filename
     */
    private $_filename;
    /**
     * @var string relative filename (relative to the SAL base/root directory)
     */
    private $_relFilename;
    /**
     * @var int
     */
    private $_lineNum;
    /**
     * @var string
     */
    private $_className;


    public function setFilename($filename)
    {
        if (is_string($filename)) {
            $this->_filename = $filename;

            if (defined('SAL_BASE_DIRECTORY')) {
                $this->_relFilename = str_replace(SAL_BASE_DIRECTORY . DIRECTORY_SEPARATOR, '', $filename);
            }
        }
    }

    public function setLineNum($lineNum)
    {
        if (is_int($lineNum)) {
            $this->_lineNum = $lineNum;
        }
    }

    public function setClassName($className)
    {
        if (is_string($className)) {
            $this->_className = $className;
        }
    }

    public function getFilename()
    {
        if (isset($this->_filename)) {
            return $this->_filename;
        }
        return '';
    }

    public function getRelativeFilename()
    {
        if (isset($this->_relFilename)) {
            return $this->_relFilename;
        }
        return '';
    }

    public function getLineNum()
    {
        if (isset($this->_lineNum)) {
            return $this->_lineNum;
        }
        return 0;
    }

    public function getClassName()
    {
        return $this->_className;
    }

}

