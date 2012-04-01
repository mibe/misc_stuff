<?php

// Copyright (C) 2011 Michael Bemmerl
// Released under a CC0 license (http://creativecommons.org/publicdomain/zero/1.0/)
// https://github.com/mibe/misc_stuff/blob/master/imagemagick.php

/*
 * This is a simple interface for calling ImageMagick.
 *
 *
 * Example:
 *
 * $im = new ImageMagick();
 * $im->addSequence('rose.jpg');
 * $im->addSequence('-resize 50%');
 * $im->addSequence('rose.png');
 * $result = $im->execute();
 *
 *
 * Another example:
 *
 * $im = new ImageMagick();
 * $im->addSequence('base.gif');
 * $im->addSequence('-compose Screen');
 * 
 * foreach($files as $file)
 * {
 * 	$im->startSubSequence();
 * 	$im->addSequence('-clone 0');
 * 	$im->addSequence($file);
 * 	$im->addSequence('-composite');
 * 	$im->endSubSequence();
 * }
 * 
 * $im->addSequence('-delete 0');
 * $im->addSequence('-set delay 100');
 * $im->addSequence('-layers optimize');
 * $im->addSequence($file);
 * 
 * echo $im->getCommandLine();
 * 
 * $result = $im->execute($output);
 */

class ImageMagick
{
	var $path;

	var $sequences;
	var $isSubSequence;
	var $subSequences;

	public function __construct()
	{
		if (!$this->detectExecutable())
			throw new Exception("Executable not found");

		$this->reset();
	}

	public function startSubSequence()
	{
		$this->endSubSequence();
		$this->isSubSequence = TRUE;
	}

	public function endSubSequence()
	{
		if (!$this->isSubSequence)
			return;

		$newSequence = '(';

		foreach($this->subSequences as $sequence)
			$newSequence .= ' ' . $sequence;

		$newSequence .= ' )';

		$this->isSubSequence = FALSE;
		$this->subSequences = array();

		$this->addSequence($newSequence);
	}

	public function addSequence($input)
	{
		$this->isSubSequence ? $arr = &$this->subSequences : $arr = &$this->sequences;
		$arr[] = $input;
	}

	public function execute(&$output = NULL)
	{
		$cmd = $this->buildCommandLine();
		$cmd = escapeshellcmd($cmd);
		$cmd .= " 2>&1";

		exec($cmd, $output, $return);

		$this->reset();

		return $return == 0;
	}

	public function reset()
	{
		$this->sequences = array();
		$this->subSequences = array();
		$this->isSubSequence = FALSE;
	}

	public function getCommandLine()
	{
		return $this->buildCommandLine();
	}

	private function buildCommandLine()
	{
		$result = '';

		foreach($this->sequences as $sequence)
			$result .= ' ' . $sequence;

		return $this->path . $result;
	}

	private function detectExecutable()
	{
		$file = 'convert';

		if (PHP_OS == 'WINNT')
			$file .= '.exe';

		$path = __DIR__ . '/' . $file;

		if (file_exists($path))
		{
			$this->path = $path;
			return TRUE;
		}

		if (PHP_OS == 'WINNT')
			$path = getenv('ProgramFiles') . '\\ImageMagick\\' . $file;
		else
			$path = '/usr/bin/' . $file;

		if (file_exists($path))
		{
			$this->path = $path;
			return TRUE;
		}

		return FALSE;
	}
}