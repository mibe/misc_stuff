#!/bin/bash
# Script for converting SVG files to PNG (with transparency support) by using
# Mathieu Leplatre's static compile of librsvg & libcairo, named svgconvert.
# After conversion, the resulting PNG will be resized for thumbnailing.
# 
# (C) 2010, 2012 Michael Bemmerl für chiliwiki.de
#
# Requirements:
#  * ImageMagick (see CONVERT variable below)
#  * svgconvert (see SVGCONVERT variable below)
#    (http://blog.mathieu-leplatre.info/static-build-of-cairo-and-librsvg.html)
#
# Usage:
# convert.sh source target width height
#
# License:
# MIT License (see LICENSE file)

# Quality setting for ImageMagick (used for thumbnailing)
#
QUALITY=85

# Path to ImageMagick binary
#
CONVERT=/usr/bin/convert

# Path to svgconvert (default to look in same directory as this script)
#
SVGCONVERT=`dirname $0`/svgconvert

exec 2>&1

# Handle missing arguments
if [ $# -ne 4 ]; then
	echo "Error: Missing arguments - Expecting four arguments." >&2
	exit 1
fi

# Check arguments
if [ ! -f $1 ]; then
	echo "Errror: Source file \"$1\" does not exist." >&2
	exit 2
fi

if [ ! $3 -gt 0 ]; then
	echo "Error: Width must be greater than zero." >&2
	exit 3
fi

if [ ! $4 -gt 0 ]; then
	echo "Error: Height must be greather than zero." >&2
	exit 4
fi

RESIZE=$3x$4
TMPFILE=`tempfile --suffix=.png`

# Execute SVG converter
$SVGCONVERT $1 $TMPFILE

if [ $? -ne 0 ]; then
	echo "Error: Could not convert SVG to PNG." >&2
	exit 5
fi

# Execute resizing
$CONVERT $TMPFILE -quality $QUALITY -resize $RESIZE -alpha on $2

if [ $? -ne 0 -o ! -f $2 ]; then
	echo "Error: Could not resize PNG." >&2
	exit 6
fi

rm $TMPFILE
