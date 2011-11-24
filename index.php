<?php

$tempfilename = 'xml/lashou.xml';
if (! file_exists ( $tempfilename )) {
	$fopen = fopen ( $tempfilename, 'w+' ); //新建文件命令 
	fclose ( $fopen );
}
$srcurl = 'http://open.client.lashou.com/api/detail/city/%E4%B8%8A%E6%B5%B7/p/1/r/10';
$dynpage = fopen ( $srcurl, 'r' );
if (! $dynpage) {
	echo ("<P>Unable to load $srcurl. Static page " . "update aborted!</P>");
	exit ();
}
$htmldata = file_get_contents ( $srcurl);
fclose ( $dynpage );
$tempfile = fopen ( $tempfilename, 'w' );
if (! $tempfile) {
	echo ("<P>Unable to open temporary file " . "($tempfilename) for writing. Static page " . "update aborted!</P>");
	exit ();
}
fwrite ( $tempfile, $htmldata );
fclose ( $tempfile );


