<?php
class DataBase {
	static public $db;
	function __construct() {
		include ('lib/adodb/adodb.inc.php');
		if (! DataBase::$db) {
			DataBase::$db = ADONewConnection ( 'mysql' );
			DataBase::$db->execute("SET NAMES 'gbk'");
			DataBase::$db->execute("SET CHARACTER SET 'gbk'");
			DataBase::$db->Connect ( 'localhost', 'davis', '111111', 'coupon' );
		}
	}
}


	