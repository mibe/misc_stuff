## convert.sh

This script is for converting SVG files to PNG (with transparency support)
in MediaWiki. This is done by using Mathieu Leplatre's static compile of
librsvg & libcairo, named svgconvert. After rendering, the resulting PNG will
be resized to the requested width & height of the thumbnail.

## Requirements

* ImageMagick
* _Mathieu Leplatre's_ svgconvert

ImageMagick is used for resizing the resulting PNG. svgconvert is just the
converter SVG --> PNG. The svgconvert can actually convert to PDF also, but
this is not used here. You can download the source code or the binary of the
converter at [Mathieu Leplatre's website](http://blog.mathieu-leplatre.info/static-build-of-cairo-and-librsvg.html).

## Installation

Build svgconvert (or use the binary) and copy the executable and the convert.sh
script to a location you prefer. Usually convert.sh does not need modifcation,
but have a look at the following three settings in the script to configure it
to your needs:

```shell
QUALITY
CONVERT
SVGCONVERT
```

MediaWiki has to be configured in `LocalSettings.php` to use this script
for converting SVGs:

```php
$wgSVGConverterPath = "/path/to/convert.sh";
$wgSVGConverters['svgconverter'] = '$path/convert.sh $input $output $width $height';
$wgSVGConverter = "svgconverter";
```

This will add a new SVG renderer named 'svgconverter', which points to
the convert.sh script. Refer to the [MediaWiki documentation](https://www.mediawiki.org/wiki/Manual:Image_Administration#SVG)
for more details about the different setting variables.