<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 * 
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback;

final class DebugLevel
{
    const DEBUG  = 1;
    const INFO   = 2;
    const NOTICE = 4;
    const WARN   = 8;
    const ERROR  = 16;
    const FATAL  = 32;
    const ALL    = 63;

}