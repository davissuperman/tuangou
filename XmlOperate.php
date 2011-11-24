<?php
class XmlOperate {
	function __construct($file) {
		$tempfilename = $file;
		if (file_exists ( $tempfilename )) {
			$xml = simplexml_load_file ( $tempfilename, 'SimpleXMLElement', LIBXML_NOCDATA );
			//	$wap_url = $xml->xpath('/urlset/url/wap_url');
			$arr = array ();
			$database = new DataBase ();
			$date = date ( 'Ymd' );
			$table = 'lashou_' . $date;
			$table_exist = DataBase::$db->getCol ( 'show tables like "' . $table . '"' );
			$exists = ! empty ( $table_exist );
			if (! $exists) {
				$create_sql = 'CREATE TABLE IF NOT EXISTS `' . $table . '` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) CHARACTER SET gb2312,
  `image` varchar(200) CHARACTER SET gb2312 ,
  `small_image` varchar(200) ,
  `startTime` timestamp  DEFAULT \'0000-00-00 00:00:00\',
  `endTime` timestamp DEFAULT \'0000-00-00 00:00:00\',
  `value` int(11),
  `price` int(11) ,
  `rebate` int(11),
  `bought` int(11),
  `detail` varchar(200) CHARACTER SET gbk COLLATE gbk_bin ,
  `wap_url` varchar(50) CHARACTER SET gbk COLLATE gbk_bin ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk;';
				DataBase::$db->Execute ($create_sql);
				$flush_table = "flush table $table";
				DataBase::$db->Execute ($flush_table);
				foreach ( $xml->url as $value ) {
					$arr ['loc'] = ( string ) $value->loc;
					$arr ['title'] = ( string ) $value->data->display->title;
					$arr ['image'] = ( string ) $value->data->display->image;
					$arr ['small_image'] = ( string ) $value->data->display->small_image;
					$arr ['startTime'] = ( string ) $value->data->display->startTime;
					$arr ['endTime'] = ( string ) $value->data->display->endTime;
					$arr ['value'] = ( string ) $value->data->display->value;
					$arr ['price'] = ( string ) $value->data->display->price;
					$arr ['rebate'] = ( string ) $value->data->display->rebate;
					$arr ['bought'] = ( string ) $value->data->display->bought;
					$arr ['detail'] = ( string ) $value->data->display->detail;
					$arr ['wap_url'] = ( string ) $value->wap_url;
					$sql = "insert into " . $table . " ( `title`, `image`, `small_image`, `startTime`, `endTime`, `value`, `price`, `rebate`, `bought`, `detail`, `wap_url`) values (?,?,?,?,?,?,?,?,?,?,?)";
					DataBase::$db->Execute ( $sql, array (addslashes ( $arr ['title'] ), addslashes ( $arr ['image'] ), addslashes ( $arr ['small_image'] ), $arr ['startTime'], $arr ['endTime'], $arr ['value'], $arr ['price'], $arr ['rebate'], $arr ['bought'], addslashes ( $arr ['detail'] ), $arr ['wap_url'] ) );
				}
				echo 'Insert ID equal: '.DataBase::$db->Insert_ID($table,'id');
			}
		
		}
	}
}

?>