<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 *
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Tests\Talkback\Channel;

use Talkback\Channel\ChannelLauncher;
use Talkback\Channel\Syslog;
use Talkback\Exception\InvalidArgumentException;

/**
 * PHPUnit tests specifically for the Comms_Syslog class
 */
class SyslogTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Basic instantiation and config of a new Syslog Channel object
     */
    public function testSyslog()
    {
        $obj = ChannelLauncher::Syslog('MyTestLogger');
        $this->assertInstanceOf('Talkback\Channel\Syslog', $obj);
        $this->assertInstanceOf('Talkback\Channel\ChannelObject', $obj);
        $this->assertInstanceOf('Talkback\Object', $obj);
        $this->assertInstanceOf('Talkback\Channel\Channelinterface', $obj);
        $this->assertEquals('MyTestLogger', $obj->getName());
        $obj->setOption(LOG_ODELAY | LOG_PID); // should be fine
        $obj->setFacility(LOG_CRON); // should be fine
    }


    /**
     * The Syslog Channel object has a limit on the name you can provide
     */
    public function testSyslogLongNameException()
    {
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'Syslog name must be a string, max 30 chars'
        );
        $obj = new Syslog('this name is too long to fit in the syslog object so should raise an exception');
    }


    /**
     * setName() method accepts a string up to 30 chars
     */
    public function testLoggerSyslogSetNameExceptions()
    {
        $obj = new Syslog('test app');
        $this->setExpectedException(
            'Talkback\Exception\InvalidArgumentException', 'Syslog name must be a string, max 30 chars'
        );
        $obj->setName('this name is too long to fit in the syslog object so should raise an exception');
    }


    /**
     * Test the various facility levels of the Syslog Channel
     */
    public function testSyslogSetFacilityExceptions()
    {
        $obj = new Syslog('test app');

        // Test an invalid int
        $fail = true;
        try {
            $obj->setFacility(123456);
        }
        catch (InvalidArgumentException $e) {
            if ($e->getMessage() == 'facility must be a valid LOG facility constant') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised");

        // test an invalid datatype
        $fail = true;
        try {
            $obj->setFacility("LOG_AUTH"); // This would be OK if it was a constant LOG_AUTH not a string
        }
        catch (InvalidArgumentException $e) {
            if ($e->getMessage() == 'facility must be a valid LOG facility constant') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised");

        // test a constant that looks OK but is actually an option argument
        $fail = true;
        try {
            $obj->setFacility(LOG_CONS);
        }
        catch (InvalidArgumentException $e) {
            if ($e->getMessage() == 'facility must be a valid LOG facility constant') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised");

        // test a combination of facilities
        $fail = true;
        try {
            $obj->setFacility(LOG_AUTH | LOG_CRON);
        }
        catch (InvalidArgumentException $e) {
            if ($e->getMessage() == 'facility must be a valid LOG facility constant') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised");
    }


    /**
     * Test the various Option valus of the Syslog Channel
     */
    public function testSyslogSetOptionExceptions()
    {
        $obj = new Syslog('test app');

        // Test an invalid int
        $fail = true;
        try {
            $obj->setOption(123456);
        }
        catch (InvalidArgumentException $e) {
            if ($e->getMessage() == 'option must be a valid constant LOG_CONS, LOG_NDELAY, LOG_ODELAY, LOG_PERROR or LOG_PID (default)') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised");

        // test an invalid datatype
        $fail = true;
        try {
            $obj->setOption("LOG_CONS"); // This would be OK if it was a constant LOG_AUTH not a string
        }
        catch (InvalidArgumentException $e) {
            if ($e->getMessage() == 'option must be a valid constant LOG_CONS, LOG_NDELAY, LOG_ODELAY, LOG_PERROR or LOG_PID (default)') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised");

        // test a constant that looks OK but does not resolve to a valid integer
        $fail = true;
        try {
            $obj->setOption(LOG_LOCAL0);
        }
        catch (InvalidArgumentException $e) {
            if ($e->getMessage() == 'option must be a valid constant LOG_CONS, LOG_NDELAY, LOG_ODELAY, LOG_PERROR or LOG_PID (default)') $fail = false;
        }
        if ($fail) $this->fail("An expected exception has not been raised when testing wrong constant");
    }

}
