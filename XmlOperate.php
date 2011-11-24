<?php
$tempfilename = $tempfilename = 'xml/lashou.xml';
include_once 'database.php';
if (file_exists ( $tempfilename )) {
	$xml = simplexml_load_file($tempfilename,'SimpleXMLElement', LIBXML_NOCDATA);
//	$wap_url = $xml->xpath('/urlset/url/wap_url');
	$arr = array();
	$database = new DataBase();
	foreach ($xml->url as $value){
		$arr['loc'] = (string)$value->loc;
		$arr['title'] = (string)$value->data->display->title;
		$arr['image'] = (string)$value->data->display->image;
		$arr['small_image'] = (string)$value->data->display->small_image;
		$arr['startTime'] = (string)$value->data->display->startTime;
		$arr['endTime'] = (string)$value->data->display->endTime;
		$arr['value'] = (string)$value->data->display->value;
		$arr['price'] = (string)$value->data->display->price;
		$arr['rebate'] = (string)$value->data->display->rebate;
		$arr['bought'] = (string)$value->data->display->bought;
		$arr['detail'] = (string)$value->data->display->detail;
		$arr['wap_url'] = (string)$value->wap_url;
		echo '<br/> ----------- <br/>';
		$sql = "insert into lashou ( `title`, `image`, `small_image`, `startTime`, `endTime`, `value`, `price`, `rebate`, `bought`, `detail`, `wap_url`) values (?,?,?,?,?,?,?,?,?,?,?)";
		DataBase::$db->Execute($sql,array(addslashes($arr['title']),addslashes($arr['image']),addslashes($arr['small_image']),$arr['startTime'],$arr['endTime'] ,$arr['value'],$arr['price'],$arr['rebate'],$arr['bought'],addslashes($arr['detail']),$arr['wap_url']));
	}
}

?>