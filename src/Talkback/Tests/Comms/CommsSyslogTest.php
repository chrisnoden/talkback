<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests;

use Talkback;
use Talkback\Comms;

/**
 * PHPUnit tests specifically for the Comms_Syslog class
 */
class CommsSyslogTest extends PHPUnit_Framework_TestCase
{

    public function testSyslog()
    {
        $obj = Comms::Syslog('My Test Logger');
        $this->assertInstanceOf('CommsSyslog', $obj);
        $this->assertInstanceOf('CommsBase', $obj);
        $this->assertInstanceOf('Object', $obj);
        $this->assertEquals('MyTestLogger', $obj->getName());
        $obj->setOption(LOG_ODELAY | LOG_PID); // should be fine
        $obj->setFacility(LOG_CRON); // should be fine
    }

    public function testSyslogLongNameException()
    {
        $this->setExpectedException(
            'SalInvalidArgumentException', 'Syslog name must be a string, max 30 chars'
        );
        $obj = new Logger_Syslog('this name is too long to fit in the syslog object so should raise an exception');
    }

    public function testSyslogInvalidLevelException()
    {
        $obj = new Logger_Syslog('test app');
        $this->setExpectedException(
            'Psr\Log\InvalidArgumentException', 'Invalid log $level, must be one of debug, info, notice, warning, error, critical, alert, emergency'
        );
        $obj->log('invalid', 'Test of Exception');
    }

    public function testLoggerSyslogSetNameExceptions()
    {
        $obj = new Logger_Syslog('test app');
        $this->setExpectedException(
            'SalInvalidArgumentException', 'Syslog name must be a string, max 30 chars'
        );
        $obj->setName('this name is too long to fit in the syslog object so should raise an exception');
    }

    public function testSyslogSetFacilityExceptions()
    {
        $obj = new Logger_Syslog('test app');

        // Test an invalid int
        $fail = true;
        try {
            $obj->setFacility(123456);
        }
        catch (SalInvalidArgumentException $e) {
            if ($e->getMessage() == 'facility must be a valid LOG facility constant') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised");

        // test an invalid datatype
        $fail = true;
        try {
            $obj->setFacility("LOG_AUTH"); // This would be OK if it was a constant LOG_AUTH not a string
        }
        catch (SalInvalidArgumentException $e) {
            if ($e->getMessage() == 'facility must be a valid LOG facility constant') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised");

        // test a constant that looks OK but is actually an option argument
        $fail = true;
        try {
            $obj->setFacility(LOG_CONS);
        }
        catch (SalInvalidArgumentException $e) {
            if ($e->getMessage() == 'facility must be a valid LOG facility constant') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised");

        // test a combination of facilities
        $fail = true;
        try {
            $obj->setFacility(LOG_AUTH | LOG_CRON);
        }
        catch (SalInvalidArgumentException $e) {
            if ($e->getMessage() == 'facility must be a valid LOG facility constant') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised");
    }

    public function testSyslogSetOptionExceptions()
    {
        $obj = new Logger_Syslog('test app');

        // Test an invalid int
        $fail = true;
        try {
            $obj->setOption(123456);
        }
        catch (SalInvalidArgumentException $e) {
            if ($e->getMessage() == 'option must be a valid constant LOG_CONS, LOG_NDELAY, LOG_ODELAY, LOG_PERROR or LOG_PID (default)') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised");

        // test an invalid datatype
        $fail = true;
        try {
            $obj->setOption("LOG_CONS"); // This would be OK if it was a constant LOG_AUTH not a string
        }
        catch (SalInvalidArgumentException $e) {
            if ($e->getMessage() == 'option must be a valid constant LOG_CONS, LOG_NDELAY, LOG_ODELAY, LOG_PERROR or LOG_PID (default)') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised");

        // test a constant that looks OK but does not resolve to a valid integer
        $fail = true;
        try {
            $obj->setOption(LOG_LOCAL0);
        }
        catch (SalInvalidArgumentException $e) {
            if ($e->getMessage() == 'option must be a valid constant LOG_CONS, LOG_NDELAY, LOG_ODELAY, LOG_PERROR or LOG_PID (default)') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised when testing wrong constant");
    }

}
