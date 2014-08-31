<?php
# The RSS feed obtained from mapdust.com is broken and does not validate
# because of some whitespace before the XML declaration. Mozilla Thunderbird
# does not like this. This script fixes that by removing the
# trailing whitespace.
#
# (C) 2014 Michael Bemmerl
# License: WTFPL-2.0

$url = 'http://www.mapdust.com/feed';
$query = http_build_query($_GET);
$url .= '?' . $query;

$content = file_get_contents($url);
$content = trim($content);

header('Content-Type: application/rss+xml; charset=utf-8');
print $content;
