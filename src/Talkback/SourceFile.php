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

/**
 * Class SourceFile
 * Holds information about the source file that triggered the Logging event
 *
 * @package Talkback
 */
class SourceFile extends Object
{
    /**
     * @var string absolute filename
     */
    private $filename;
    /**
     * @var string relative filename (relative to the SAL base/root directory)
     */
    private $relFilename;
    /**
     * @var int
     */
    private $lineNum;
    /**
     * @var string
     */
    private $className;


    /**
     * @param $filename
     */
    public function setFilename($filename)
    {
        if (is_string($filename)) {
            $this->filename = $filename;

            if (defined('SAL_BASE_DIRECTORY')) {
                $this->relFilename = str_replace(SAL_BASE_DIRECTORY . DIRECTORY_SEPARATOR, '', $filename);
            }
        }
    }


    /**
     * @param $lineNum
     */
    public function setLineNum($lineNum)
    {
        if (is_int($lineNum)) {
            $this->lineNum = $lineNum;
        }
    }


    /**
     * @param $className
     */
    public function setClassName($className)
    {
        if (is_string($className)) {
            $this->className = $className;
        }
    }


    /**
     * @return string
     */
    public function getFilename()
    {
        if (isset($this->filename)) {
            return $this->filename;
        }
        return '';
    }


    /**
     * @return string
     */
    public function getRelativeFilename()
    {
        if (isset($this->relFilename)) {
            return $this->relFilename;
        }
        return '';
    }


    /**
     * @return int
     */
    public function getLineNum()
    {
        if (isset($this->lineNum)) {
            return $this->lineNum;
        }
        return 0;
    }


    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

}

