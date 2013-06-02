SimpleLogger - Easy to use logger for PHP
==========================================

SimpleLogger is simple library to write logs in PHP.
Logging is very useful for debugging web apps, so that need to be very easy to use.

Features
--------

- Simple and easy to use
- Logging output: Syslog and Text files
- Implements PSR-3 Standard Logging Interface

Requirements
------------

- PHP >= 5.3
- PSR-3 Standard Logger Interface <https://github.com/php-fig/log>

Author
------

Frédéric Guillot: [http://fredericguillot.com](http://fredericguillot.com)

Source code
-----------

On Github: [https://github.com/fguillot/simpleLogger](https://github.com/fguillot/simpleLogger)

License
-------

MIT

Usage
-----

### Syslog

    <?php

    // Autoload yourself or include files manually
    require 'src/Psr/Log/LoggerInterface.php';
    require 'src/Psr/Log/LogLevel.php';
    require 'src/SimpleLogger/Syslog.php';

    // Setup Syslog logging
    $logger = new SimpleLogger\Syslog('myapp');

    // Output to syslog: "Jun  2 15:55:09 hostname myapp[2712]: boo"
    $logger->error('boo');

    // Output to syslog: "Jun  2 15:55:09 hostname myapp[2712]: Error at /Users/fred/Devel/libraries/simpleLogger/example.php at line 15"
    $logger->error('Error at {filename} at line {line}', array('filename' => __FILE__, 'line' => __LINE__));

### Text files

    <?php

    // Autoload yourself or include files manually
    require 'src/Psr/Log/LoggerInterface.php';
    require 'src/Psr/Log/LogLevel.php';
    require 'src/SimpleLogger/File.php';

    // Setup File logging
    $logger = new SimpleLogger\File('/tmp/simplelogger.log');

    // Output to the file: "[2013-06-02 16:03:28] [info] boo"
    $logger->info('boo');

    // Output to the file: "[2013-06-02 16:03:28] [error] Error at /Users/fred/Devel/libraries/simpleLogger/example.php at line 24"
    $logger->error('Error at {filename} at line {line}', array('filename' => __FILE__, 'line' => __LINE__));

    // Dump a variable
    $values = array(
        'key' => 'value'
    );

    // Output: [2013-06-02 16:05:32] [debug] array (
    //  'key' => 'value',
    // )
    $logger->dump($values);
