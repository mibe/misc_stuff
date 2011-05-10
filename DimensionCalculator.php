<?php

// Copyright (C) 2011 Michael Bemmerl
// Released under a  CC0 license (http://creativecommons.org/publicdomain/zero/1.0/)
// https://github.com/mibe/misc_stuff/blob/master/DimensionCalculator.php

/* This code calculates the screen size or viewable image size (VIS) of a monitor.
 * See http://en.wikipedia.org/wiki/Viewable_image_size for details of the VIS.
 *
 * The calculation routine has three inputs:
 *
 * - The diagonal of the monitor in inch (e.g. 15.6)
 * - The aspect ratio (e.g. 1.78 for 16:9)
 * - Calculate the real tube size instead of the VIS (applies to cathode ray
 *		monitors only) (optional)
 *
 * The ouput is an associated array with the elements 'Width' and 'Height',
 * which contains the size in centimeters.
*/

function calculateDimension($diagonal, $aspectRatio, $addTubeDifference = FALSE)
{
	// convert inch to cm
	$diagonal *= 2.54;

	// build a diagonal from the aspect ratio
	$aspectDiagonal = sqrt(pow($aspectRatio, 2) + 1);

	// now calculate a "zoom factor" between the real and the aspect ratio diagonal
	$factor = $diagonal / $aspectDiagonal;

	// the width is the aspect ratio multiplied by the factor, while the height
	// is actually the factor.
	$width = $aspectRatio * $factor;
	$height = $factor;

	// add 25 mm to the size if the tube size shall be calculated
	if ($addTubeDifference === TRUE)
	{
		$width += 2.5;
		$height += 2.5;
	}

	$result = array();
	$result['Width'] = $width;
	$result['Height'] = $height;

	return $result;
}

?>
<html>
<body>
<form>
Monitor diagonal in inch (e.g. 15.6): <input type="text" name="diagonal"><br>
Aspect ratio (e.g. 16:9 or 1.77777): <input type="text" name="aspect"><br>
<input type="checkbox" name="tube" value="1">Calculate real tube size<br>
<input type="submit">
</form><br><br>
<?php

if (isset($_GET['diagonal']) && isset($_GET['aspect']))
{
	$diagonal = $_GET['diagonal'];
	$aspectRatio = $_GET['aspect'];
	$addTube = FALSE;

	if (isset($_GET['tube']) && $_GET['tube'] == 1)
		$addTube = TRUE;

	// convert all user inputs in the appropriate data type
	$diagonal = (float)$diagonal;
	
	if (strpos($aspectRatio, ':') !== FALSE)
	{
		$tmp = explode(':', $aspectRatio);
		$aspectRatio = $tmp[0] / $tmp[1];
	}
	else
		$aspectRatio = (float)$aspectRatio;

	$result = calculateDimension($diagonal, $aspectRatio, $addTube);

	print 'Results:<br>';
	print 'Width: ' . round($result['Width'], 2) . ' cm<br>';
	print 'Height: ' . round($result['Height'], 2) . ' cm';
}
?>
</html>