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
class File
{
	private $fp = null;


	public function __construct($path)
	{
		$this->fp = new \SplFileObject($path, 'a');
	}


	public function write($type, $message, $file = '', $line = '')
	{
		$priority = 'DEBUG';

		switch ($type) {

			case Logger::ERROR:
				$priority = 'ERROR';
				break;

			case Logger::INFO:
				$priority = 'INFO';
				break;

			case Logger::AUTH:
				$priority = 'AUTH';
				break;
		}

		$this->fp->fwrite(sprintf(
			'%s%s%s %s'.PHP_EOL,
			'['.$priority.']',
			$file ? ' ['.$file.']' : '',
			$line ? ' ['.$line.']' : '',
			$message
		));
	}
}