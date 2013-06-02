<?php

require 'src/Psr/Log/LoggerInterface.php';
require 'src/Psr/Log/LogLevel.php';
require 'src/SimpleLogger/Syslog.php';
require 'src/SimpleLogger/File.php';

// Setup Syslog logging
$logger = new SimpleLogger\Syslog('myapp');

// Output to syslog: "Jun  2 15:55:09 hostname myapp[2712]: boo"
$logger->error('boo');

// Output to syslog: "Jun  2 15:55:09 hostname myapp[2712]: Error at /Users/fred/Devel/libraries/simpleLogger/example.php at line 15"
$logger->error('Error at {filename} at line {line}', array('filename' => __FILE__, 'line' => __LINE__));

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