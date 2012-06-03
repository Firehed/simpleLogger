<?php

require '../src/SimpleLogger/Logger.php';
require '../src/SimpleLogger/Loggers/Syslog.php';
require '../src/SimpleLogger/Loggers/File.php';

use SimpleLogger\Logger;
use SimpleLogger\Loggers;

$log = new Logger(new Loggers\Syslog);
$log->write(Logger::INFO, 'super info message', '', __LINE__);
$log->dump($log);

$log = new Logger(new Loggers\File('/tmp/simple_logger.txt'));
$log->write(Logger::AUTH, 'user connected', __FILE__, __LINE__);
$log->dump($log);