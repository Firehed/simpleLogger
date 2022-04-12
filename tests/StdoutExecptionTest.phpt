--TEST--
Logging to stdout
--FILE--
<?php
require 'vendor/autoload.php';
$e = new RuntimeException('Got back 502 response');
$logger = new Firehed\SimpleLogger\Stdout();
$logger->error('Network call failed', ['exception' => $e]);
$logger->setRenderExceptions(true);
$logger->error('Network call failed', ['exception' => $e]);
?>
--EXPECTF--
[%s] [error] Network call failed
[%s] [error] Network call failed RuntimeException: Got back 502 response in %A
Stack trace:
%A
