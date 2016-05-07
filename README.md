SimpleLogger
============

SimpleLogger is a PHP library to write logs.

- Drivers: Syslog, stdout, stderr and text file
- Compatible with [PSR-3 Standard Logger Interface](http://www.php-fig.org/psr/psr-3/)
- Requirements: PHP >= 5.3
- Author: Frédéric Guillot
- License: MIT

Usage
-----

### Installation

```bash
composer require fguillot/simpleLogger @stable
```

### Syslog

Send log messages to Syslog:

```php
<?php

require 'vendor/autoload.php';

// Setup Syslog logging
$logger = new SimpleLogger\Syslog('myapp');

// Output to syslog: "Jun  2 15:55:09 hostname myapp[2712]: foobar"
$logger->error('foobar');

// Output to syslog: "Jun  2 15:55:09 hostname myapp[2712]: Error at /Users/Me/Devel/libraries/simpleLogger/example.php at line 15"
$logger->error('Error at {filename} at line {line}', ['filename' => __FILE__, 'line' => __LINE__]);
```

### Stdout

```php
$logger = new \SimpleLogger\Stdout();
$logger->error('foobar');
```

### Stderr

```php
$logger = new \SimpleLogger\Stderr();
$logger->info('foobar');
```

### Text file

Send log messages to a text file:

```php
<?php

require 'vendor/autoload.php';

// Setup File logging
$logger = new SimpleLogger\File('/tmp/simplelogger.log');

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

$logger = new SimpleLogger\Logger;
$logger->setLogger(new SimpleLogger\Syslog('myapp'));
$logger->setLogger(new SimpleLogger\File('/tmp/simplelogger.log'));

$logger->info('my message');
$logger->error('my error message');
$logger->error('my error message with a {variable}', array('variable' => 'test'));
```

### Minimum log level for loggers

In this example, only messages with the level >= "error" will be sent to the Syslog handler but everything is sent to the File handler:

```php
<?php

require 'vendor/autoload.php';

$syslog = new SimpleLogger\Syslog('myapp');
$syslog->setLevel(Psr\Log\LogLevel::ERROR);  // Define the minimum log level

$file = new SimpleLogger\File('/tmp/simplelogger.log');

$logger = new SimpleLogger\Logger;
$logger->setLogger($syslog);
$logger->setLogger($file);

$logger->debug('debug info sent only to the text file');
$logger->error('my error message');
$logger->error('my error message with a {variable}', array('variable' => 'test'));
```

The minimum log level is `LogLevel::DEBUG` by default.
