Simple Logger - Easy to use logger for PHP
==========================================

SimpleLogger is simple library to write logs in PHP. 
Logging is very useful for debugging web apps, so that need to be very easy to use.


Features
--------

- Simple and easy to use
- No dependencies
- Supported Logging system: Syslog and Files


Requirements
------------

- PHP >= 5.3


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

I want to write log to Syslog:

	use SimpleLogger\Logger;
	use SimpleLogger\Loggers;

	$log = new Logger(new Loggers\Syslog);
	$log->write(Logger::INFO, 'super message');

I want to dump a variable to my logs:

	$log->dump($my_var); // The priority is set to DEBUG automatically

I want to stream my logs to a file:

	$log = new Logger(new Loggers\File('/tmp/simple_logger.txt'));
	$log->write(Logger::AUTH, 'user connected');

I want to send error messages:

	$log->write(Logger::ERROR, 'my error message', __FILE__, __LINE__);