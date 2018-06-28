SimpleLogger
============

[![Build Status](https://travis-ci.org/Firehed/simpleLogger.svg?branch=master)](https://travis-ci.org/Firehed/simpleLogger)
[![Coverage Status](https://coveralls.io/repos/github/Firehed/simpleLogger/badge.svg?branch=master)](https://coveralls.io/github/Firehed/simpleLogger?branch=master)

SimpleLogger is a PHP library to write logs.

- Drivers: Syslog, stdout, stderr and text file
- Compatible with [PSR-3 Standard Logger Interface](http://www.php-fig.org/psr/psr-3/)
- Requirements: PHP >= 7.1
- Author: Frédéric Guillot, Eric Stern
- License: MIT

This is a fork from Frédéric Guillot's original SimpleLogger package, which has since been abandoned. I intend to actively maintain this as needed.

Usage
-----

### Installation

```bash
composer require firehed/simplelogger @stable
```

### Syslog

Send log messages to Syslog:

```php
<?php

require 'vendor/autoload.php';

// Setup Syslog logging
$logger = new Firehed\SimpleLogger\Syslog('myapp');

// Output to syslog: "Jun  2 15:55:09 hostname myapp[2712]: foobar"
$logger->error('foobar');

// Output to syslog: "Jun  2 15:55:09 hostname myapp[2712]: Error at /Users/Me/Devel/libraries/simpleLogger/example.php at line 15"
$logger->error('Error at {filename} at line {line}', ['filename' => __FILE__, 'line' => __LINE__]);
```

### Stdout

```php
$logger = new \Firehed\SimpleLogger\Stdout();
$logger->error('foobar');
```

### Stderr

```php
$logger = new \Firehed\SimpleLogger\Stderr();
$logger->info('foobar');
```

### Text file

Send log messages to a text file:

```php
<?php

require 'vendor/autoload.php';

// Setup File logging
$logger = new Firehed\SimpleLogger\File('/tmp/simplelogger.log');

// Output to the file: "[2013-06-02 16:03:28] [info] foobar"
$logger->info('foobar');

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
```

### Multiple loggers

Send log messages to multiple loggers:

```php
<?php

require 'vendor/autoload.php';

$logger = new Firehed\SimpleLogger\ChainLogger;
$logger->addLogger(new Firehed\SimpleLogger\Syslog('myapp'));
$logger->addLogger(new Firehed\SimpleLogger\File('/tmp/simplelogger.log'));

$logger->info('my message');
$logger->error('my error message');
$logger->error('my error message with a {variable}', ['variable' => 'test']);
```

### Minimum log level for loggers

In this example, only messages with the level >= "error" will be sent to the Syslog handler but everything is sent to the File handler:

```php
<?php

require 'vendor/autoload.php';

$syslog = new Firehed\SimpleLogger\Syslog('myapp');
$syslog->setLevel(Psr\Log\LogLevel::ERROR);  // Define the minimum log level

$file = new Firehed\SimpleLogger\File('/tmp/simplelogger.log');

$logger = new Firehed\SimpleLogger\ChainLogger;
$logger->addLogger($syslog);
$logger->addLogger($file);

$logger->debug('debug info sent only to the text file');
$logger->error('my error message');
$logger->error('my error message with a {variable}', array('variable' => 'test'));
```

The minimum log level is `LogLevel::DEBUG` by default.

### Formatting

Starting in 2.1.0, custom message formatting can be configured with the `setFormat(string $format)` method.
The format provided MUST include `%s`, which is where the actual interpolated message will be placed.
Formats MAY include `{date}` and/or `{level}`, which are placeholders for the timestamp and log level respectively.

The default format is `[{date}] [{level}] %s`, which will result in a log message like this:

```
[2018-06-28T13:32:12+00:00] [debug] query finished in 0.0021688938140869s
```

The date defaults to ATOM format, but can also be customized via `setDateFormat(string $format)` using any format string that `date()` accepts.

Note: at this time, the `Syslog` logger does not use these formats.
