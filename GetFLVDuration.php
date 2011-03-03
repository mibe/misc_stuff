<?php

// Copyright (C) 2011 Michael Bemmerl
// Released under a  CC0 license (http://creativecommons.org/publicdomain/zero/1.0/)
//
// This function returns the duration of a Flash Video file in seconds or false,
// if an error occurred.

function GetFLVDuration($file)
{
	if (!file_exists($file))
		return false;

	$handle = fopen($file, "r");
	$header = fread($handle, 3);

	if ($header != "FLV")
	{
		fclose($handle);
		return false;
	}

	fseek($handle, -4, SEEK_END);

	$bytes = fread($handle, 4);
	$taglen = unpack("N", $bytes);
	$taglen = $taglen[1];

	fseek($handle, -1 * $taglen, SEEK_END);

	$bytes = fread($handle, 3);
	$duration = unpack("N", "\x00" . $bytes);
	$duration = $duration[1];

	fclose($handle);

	return $duration / 1000;
}
