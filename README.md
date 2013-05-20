talkback
========

PSR-3 compatible logging with a pluggable architecture

Created as an offshoot from my Synergy MVC Framework, Talkback is a PSR-3
compliant library that you can use to improve the communication coming out
of your PHP project.

Talkback comes with a range of Channels that can be attached to specific
LogLevels plus you can also attach any PSR-3 compliant logger.

Installing
----------

At the moment the best way is to use composer and Packagist

    $ composer require "chrisnoden/talkback" "dev-master"

Example:
--------

    <?php

    /**
     * Create a Talkback collection
     */
    $logger = \Talkback\Logger::getLogger('main logger');
    $logger
        ->addChannel(
            array(
                \Psr\Log\LogLevel::ERROR,
                \Psr\Log\LogLevel::CRITICAL,
                \Psr\Log\LogLevel::ALERT,
                \Psr\Log\LogLevel::EMERGENCY,
                \Psr\Log\LogLevel::INFO,
                \Psr\Log\LogLevel::NOTICE,
                \Psr\Log\LogLevel::WARNING
            ),
            \Talkback\Channel\ChannelFactory::File('/tmp/mylog.log'))
        ->addChannel(\Psr\Log\LogLevel::CRITICAL, \Talkback\Channel\ChannelFactory::Basic())
        ->addChannel(\Psr\Log\LogLevel::INFO, \Talkback\Channel\ChannelFactory::Growl('Bundle'));

The above example will create an object that is Psr-3 compliant (it will pass
tests comparing it to Psr\Log\LoggerInterface and/or Psr\Log\AbstractLogger).
Your new $logger object will write all logs with ERROR, CRITICAL, ALERT,
EMERGENCY, INFO, NOTICE or WARNING levels to the file 'tmp/mylog.log'.

It will also output any CRITICAL errors to your console or to the end of your
HTML output (it automatically chooses console or HTML, but you can override).

It will also output any INFO items to Growl (for Mac) - which automatically
degrades when not running on a dev Mac.

In the example the name of the logger is 'main logger' and it becomes static
so you can use Logger::getLogger('main logger') in any other block of code in
your PHP to attach to the same logger. You can create several loggers with
different names or just use one.

You can attach multiple channels to a Log Level. The example has INFO level
trapped by the File channel and by Growl.

Logging
---------------
As per the Psr-3 specification you can log in one of the following ways:

    <?php

    $logger->debug("This is a debug level message");
    $logger->log(\Psr\Log\LogLevel::ERROR, "This is an error level message");


The Channels currently include:

+ Console
+ HTML
+ Syslog
+ File
+ Growl
+ Prowl

Prowl is very handy to get iOS notifications of critical alerts for example!

I'll be refactoring this library frequently, so please check back regularly.

Questions/suggestions always welcome.