#!/bin/bash
# Skript zum Konvertieren von SVGs nach PNG (mit Alpha-Kanal) inklusive
# Größenänderung des Ausgangsmaterials.
# 
# (C) 2010 Michael Bemmerl für chiliwiki.de
#
# Usage:
# convert.sh Quelldatei Zieldatei Breite Höhe

QUALITY=85				# Qualität für ImageMagick
CONVERT=/usr/bin/convert		# ImageMagick binary
SVGCONVERT=`dirname $0`/svgconvert	# SVG nach PNG Konverter

exec 2>&1

if [ $# -ne 4 ]; then
	echo "Error: Missing arguments - Expecting four arguments." >&2
	exit 1
fi

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

$SVGCONVERT $1 $TMPFILE

if [ $? -ne 0 ]; then
	echo "Error: Could not convert SVG to PNG." >&2
	exit 5
fi

$CONVERT $TMPFILE -quality $QUALITY -resize $RESIZE -alpha on $2

if [ $? -ne 0 -o ! -f $2 ]; then
	echo "Error: Could not resize PNG." >&2
	exit 6
fi

rm $TMPFILE
