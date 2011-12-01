<?php
class XML {
	protected $_file = null;
	protected $_tableName = null;
	protected $_tableItem = array ();
	function __construct($path, $couponName) {
		$file = $this->generateXml ( $path, $couponName );
		$this->saveXml ( $file );
	
	}
	function generateXml($path, $couponName) {
		$date = date ( 'Ymd' );
		$siteName = $couponName . $date;
		$tempfilename = 'xml/' . $siteName . '.xml';
		$this->_tableName = $siteName;
		if (! file_exists ( $tempfilename )) {
			$fopen = fopen ( $tempfilename, 'w+' ); //新建文件命令 
			fclose ( $fopen );
			
			//$srcurl = 'http://open.client.lashou.com/api/detail/city/%E4%B8%8A%E6%B5%B7/p/1';
			$srcurl = $path;
			$curl = curl_init ();
			curl_setopt ( $curl, CURLOPT_URL, $srcurl );
			curl_setopt ( $curl, CURLOPT_HEADER, 0 );
			curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
			$htmldata = curl_exec ( $curl );
			curl_close ( $curl );
			
			//			$htmldata = file_get_contents ( $srcurl );
			$tempfile = fopen ( $tempfilename, 'w+' );
			if (! $tempfile) {
				echo ("<P>Unable to open temporary file " . "($tempfilename) for writing. Static page " . "update aborted!</P>");
				exit ();
			}
			fwrite ( $tempfile, (html_entity_decode ( $htmldata, ENT_QUOTES, 'UTF-8' )) );
			//			$htmldata = str_replace('&hearts;', '', $htmldata);
			fclose ( $tempfile );
		}
		return $tempfilename;
	}
	
	function saveXml($file) {
		$tempfilename = $file;
		$items_table = array ();
		if (file_exists ( $tempfilename )) {
			$xml = simplexml_load_file ( $tempfilename, 'SimpleXMLElement', LIBXML_NOCDATA );
			$tag = array ();
			$parent = array ();
			XML::parseXML ( $xml, $parent, $tag );
			foreach ( $tag as $key => $item ) {
				$value = $parent [$key];
				$item_key = trim ( $tag [$key] );
				switch ($item_key){
					case 'range':
						$item_key = 'range1';
						break;
					default:
						
				}
				if (is_numeric ( $value )) {
					if (( int ) $value > ( int ) strtotime ( "1990-01-01" )) {
						$items_table [$item_key] = ' timestamp ';
					} else {
						$items_table [$item_key] = ' float ';
					}
				} elseif (is_string ( $value )) {
					$items_table [$item_key] = ' varchar (200)  CHARACTER SET gb2312 ';
				}
			}
			$this->_tableItem = $items_table;
			$this->generateTable ( $items_table );
			
			//save data
			$content = array ();
			$this->saveXMLTOTable ( $xml, $content, 0 );
		
		//			DataBase::dumpNow ( $content );
		}
	
	}
	function saveXMLTOTable($node, &$content = array(), $key) {
		$count = 0;
		$table = $this->_tableName;
		$table_item = $this->_tableItem;
		$keys = array_keys ( $table_item );
		$key_str = implode ( ',', $keys );
		
		$question_mark = array ();
		foreach ( $keys as $every_key ) {
			$question_mark [] = '?';
		}
		foreach ( $node->children () as $child_name => $child_node ) {
			$insert_sql = "insert into $table ";
			$insert_sql .= "(";
			$insert_sql .= $key_str . ")values";
			$content = array ();
			$value_array = array ();
			XML::getSubNodeValue ( $child_node, $content );
			
			$count ++;
			foreach ( $keys as $every_key ) {
				if(array_key_exists($every_key, $content)){
					$c =  $content [$every_key] ;
				}else{
					$c = " ";
				}
				$value_array [] = addslashes ($c );
			}
			$insert_sql .= "(" . implode ( ',', $question_mark ) . ")";
			DataBase::$db->Execute ( $insert_sql, $value_array );
		}
	}
	
	static function getSubNodeValue($node, &$content = array()) {
		if ($node->count () >= 1) {
			foreach ( $node->children () as $child_name => $child_node ) {
				if ($node->count () >= 1) {
					XML::getSubNodeValue ( $child_node, $content );
				} else {
					$content [$child_node->getName ()] = "$child_node";
				}
			}
		
		} else {
			$content [$node->getName ()] = "$node";
		}
	}
	function generateTable($items_table) {
		$table = $this->_tableName;
		$table_exist = DataBase::getDB ()->getCol ( 'show tables like "' . $table . '"' );
		$exists = ! empty ( $table_exist );
		if (! $exists) {
			$sql = null;
			foreach ( $items_table as $item => $set_item ) {
				$sql .= "`$item`" . " $set_item ,";
			}
			$create_sql = 'CREATE TABLE  `' . $table . '` (
 						 `id` int(11) NOT NULL AUTO_INCREMENT,' . $sql . '
						 PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=gbk;';
			DataBase::getDB ()->Execute ( $create_sql );
			$flush_table = "flush table $table";
			DataBase::getDB ()->Execute ( $flush_table );
		}
	
	}
	static function parseXML($node, &$parent = array(), &$tag = array()) {
		$only_child = true;
		if ($node->count () >= 1)
			$only_child = FALSE;
		$node_name = $node->getName ();
		
		if ($only_child) {
			$content = "$node";
			$parent [] = $content;
			if (! in_array ( $child_node->getName (), $tag )) {
				$tag [] = $child_node->getName ();
				$content = "$child_node";
				$parent [] = $content;
			}
		}
		$count = 0;
		foreach ( $node->children () as $child_name => $child_node ) {
			if ($child_node->count () >= 1)
				XML::parseXML ( $child_node, $parent, $tag );
			else {
				if (! in_array ( $child_node->getName (), $tag )) {
					$tag [] = $child_node->getName ();
					$content = "$child_node";
					$parent [] = $content;
				}
			
			}
			$count ++;
		}
	}
}