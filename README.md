SimpleLogger
============

SimpleLogger is simple library to write logs in PHP.

Features
--------

- Simple and easy to use
- Logging output: Syslog and Text files
- Compatible with PSR-3 Standard Logger Interface <http://www.php-fig.org/psr/psr-3/>

Requirements
------------

- PHP >= 5.3
- Composer

Author
------

Frédéric Guillot

Source code
-----------

On Github: [https://github.com/fguillot/simpleLogger](https://github.com/fguillot/simpleLogger)

License
-------

MIT

Usage
-----

### Installation

```bash
composer require fguillot/simpleLogger dev-master
```

### Syslog

```php
<?php

require 'vendor/autoload.php';

// Setup Syslog logging
$logger = new SimpleLogger\Syslog('myapp');

// Output to syslog: "Jun  2 15:55:09 hostname myapp[2712]: boo"
$logger->error('boo');

// Output to syslog: "Jun  2 15:55:09 hostname myapp[2712]: Error at /Users/Me/Devel/libraries/simpleLogger/example.php at line 15"
$logger->error('Error at {filename} at line {line}', array('filename' => __FILE__, 'line' => __LINE__));
```

### Text files

```php
<?php

require 'vendor/autoload.php';

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
```
