<?php

/*
 * This file is part of Simple Logger.
 *
 * (c) Frédéric Guillot <contact@fredericguillot.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SimpleLogger\Loggers;

use SimpleLogger\Logger;

/**
 * @author Frédéric Guillot <contact@fredericguillot.com>
 */
class Syslog
{
	public function __construct($ident = 'PHP', $facility = LOG_USER)
	{
		if (! \openlog($ident, LOG_ODELAY, $facility)) {

			throw new \RuntimeException('Unable to connect to syslog.');
		}
	}


	public function write($type, $message, $file = '', $line = '')
	{
		$priority = LOG_DEBUG;

		if ($type === Logger::INFO || $type === Logger::AUTH) {

			$priority = LOG_INFO;
		}
		elseif ($type === Logger::ERROR) {

			$priority = LOG_ERR;
		}

		\syslog($priority, sprintf(
			'%s%s%s',
			$file ? '['.$file.'] ' : '',
			$line ? '['.$line.'] ' : '',
			$message
		));
	}
}