<?php

/*
 * This file is part of Simple Logger.
 *
 * (c) Frédéric Guillot <contact@fredericguillot.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SimpleLogger;


/**
 * @author Frédéric Guillot <contact@fredericguillot.com>
 */
class Logger
{
	const DEBUG = 100;
	const AUTH = 200;
	const ERROR = 300;
	const INFO = 400;

	private $logger = null;


	public function __construct($logger)
	{
		$this->logger = $logger;
	}


	public function write($type, $message, $file = '', $line = '')
	{
		$this->logger->write($type, $message, $file, $line);
	}


	public function dump($variable, $file = '', $line = '')
	{
		$this->logger->write(self::DEBUG, \var_export($variable, true), $file = '', $line = '');
	}
}