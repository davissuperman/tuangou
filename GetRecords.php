<?php
class GetRecords {
	protected $_file = null;
	function __construct($siteName) {
		$date = date ( 'Y-m-d' );
		$siteName = $siteName . $date;
		$tempfilename = 'xml/' . $siteName . '.xml';
		if (! file_exists ( $tempfilename )) {
			$fopen = fopen ( $tempfilename, 'w+' ); //新建文件命令 
			fclose ( $fopen );
			
			$srcurl = 'http://open.client.lashou.com/api/detail/city/%E4%B8%8A%E6%B5%B7/p/1';
			$dynpage = fopen ( $srcurl, 'r' );
			if (! $dynpage) {
				echo ("<P>Unable to load $srcurl. Static page " . "update aborted!</P>");
				exit ();
			}
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $srcurl);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$htmldata = curl_exec($curl);
			curl_close($curl);
			
//			$htmldata = file_get_contents ( $srcurl );
			fclose ( $dynpage );
			$tempfile = fopen ( $tempfilename, 'w+' );
			if (! $tempfile) {
				echo ("<P>Unable to open temporary file " . "($tempfilename) for writing. Static page " . "update aborted!</P>");
				exit ();
			}
			fwrite ( $tempfile, $htmldata );
			fclose ( $tempfile );
		}
		$this->_file = $tempfilename;
	}
	
	public function getFile() {
		return $this->_file;
	}
}

?>