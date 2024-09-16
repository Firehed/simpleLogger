SimpleLogger
============

[![Test](https://github.com/Firehed/simpleLogger/workflows/Test/badge.svg)](https://github.com/Firehed/simpleLogger/actions?query=workflow%3ATest)
[![Lint](https://github.com/Firehed/simpleLogger/workflows/Lint/badge.svg)](https://github.com/Firehed/simpleLogger/actions?query=workflow%3ALint)
[![Static analysis](https://github.com/Firehed/simpleLogger/workflows/Static%20analysis/badge.svg)](https://github.com/Firehed/simpleLogger/actions?query=workflow%3A%22Static+analysis%22)
[![codecov](https://codecov.io/gh/Firehed/simpleLogger/branch/master/graph/badge.svg)](https://codecov.io/gh/Firehed/simpleLogger)

SimpleLogger is a PHP library to write logs.

- Drivers: Syslog, stdout, stderr and text file
- Compatible with [PSR-3 Standard Logger Interface](http://www.php-fig.org/psr/psr-3/)
- Requirements: PHP >= 7.2 (older versions may work, but are not tested)
- Author: Frédéric Guillot, Eric Stern
- License: MIT

This is a fork from Frédéric Guillot's original SimpleLogger package, which has since been abandoned. I intend to actively maintain this as needed.

Usage
-----

### Installation

```bash
composer require firehed/simplelogger
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

Starting in 3.0.0, message format customization can be accomplished in several ways:

- Initialize `DefaultFormatter`, change its format with `setFormat(string $format)`, and pass it to your logger's constructor
- Use a different bundled formatter, such as `LogFmtFormatter`
- Create a class that implements `FormatterInterface` and pass that to your logger's constructor

#### `DefaultFormatter`

The format provided MUST include `%s`, which is where the actual interpolated message will be placed.
Formats MAY include `{date}` and/or `{level}`, which are placeholders for the timestamp and log level respectively.

The default format is `[{date}] [{level}] %s`, which will result in a log message like this:

```
[2018-06-28T13:32:12+00:00] [debug] query finished in 0.0021688938140869s
```

The date defaults to ATOM format, but can also be customized via `setDateFormat(string $format)` using any format string that `date()` accepts.

By default, this will ignore `exception` keys and perform normal message interpolation.
By calling `setRenderExceptions(true)`, the equivalent of `(string) $context['exception']` will be appended to the log message if that key is set, so long as that value is `Throwable`.

#### LogFmt

The `LogFmtFormatter` will write logs in `lgtfmt`(https://brandur.org/logfmt).
By default, the `msg`, `level`, and `ts` keys will be set, and any values in `context` that are not interpolated will be added as additional key/value pairs.
Exceptions will also be rendered, in `exception_message`, `exception_type`, and `exception_stacktrace` per [OTel conventions](https://opentelemetry.io/docs/specs/semconv/exceptions/exceptions-logs/).

> [!TIP]
> Any interpolated values from context will _not_ be put in the key/value pairs.
> To make the most out of structured log formats such as logfmt, limit interpolation keys in the coded message.
>
> For example, prefer this:
>
> ```php
> $logger->debug('Request complete', ['duration_ms' => $ms]);
> // produces `msg="Request complete" duration_ms=42
> ```
>
> over this:
>
> ```php
> $logger->debug('Request complete in {duration} ms', ['duration' => $ms]);
> // produces `msg="Request complete in 42 ms"
> ```

#### Custom `FormatterInterface`

If you need deeper customization, such as log enrichment or more major format shifts, a custom `FormatterInterface` is the way to go.
Doing so requires your own interpolation logic, as well as any other message enrichment or formatting.
You'll be passed the same (unmodified) parameters as `LoggerInterface::log()` gets, and are responsible for transforming these values into a string.
