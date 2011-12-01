<?php
class DataBase {
	public static $db;
	/**
	 * custom dumper - see MUCO :: setDumper()
	 */
	private static $dumper = null;
	static function getDB() {
		include ('lib/adodb/adodb.inc.php');
		if (! DataBase::$db) {
			DataBase::$db = ADONewConnection ( 'mysql' );
			DataBase::$db->execute ( "SET NAMES 'gbk'" );
			DataBase::$db->execute ( "SET CHARACTER SET 'gbk'" );
			DataBase::$db->Connect ( 'localhost', 'davis', '111111', 'coupon' );
		}
		return DataBase::$db;
	}
	function __construct() {
	}
	public static function dumpNow($obj, $msg = null, $backTraceLevel = 0, $evenIfLive = false) {
		
		
		$dumpNow = self::makeDump ( $obj, $msg, $backTraceLevel + 1 );
		
		//CLI script can attach their own dump mechanisms
		if (self::$dumper) {
			//custom dumper - see MUCO :: setDumper()
			self::$dumper->outputNow ( $dumpNow [0], $dumpNow [1] );
		} else {
			$return = null;
			$return .= '<div class="diagnostics">';
			$return .= '<pre>';
			$return .= "<i>Diagnostic:</i> {$dumpNow[0]}<br/>{$dumpNow[1]}\n";
			$return .= '</pre>';
			$return .= '</div>';
			echo $return;
			flush ();
		}
	}
	
	private static function makeDump($obj = null, $msg = null, $backTraceLevel = 0) {
		$type = 'String';
		if ($obj !== null) {
			//autogenerate basic info and append to $msg
			$type = ucfirst ( gettype ( $obj ) );
			if (is_object ( $obj )) {
				$type = get_class ( $obj ) . ' Object';
			}
			if (is_string ( $obj )) {
				$obj = htmlspecialchars ( $obj, ENT_QUOTES ); //display markup, etc.
			} else {
				$obj = var_export ( $obj, true );
			}
		}
		$backtrace = debug_backtrace (); //TODO: use debug_backtrace(false) when server PHP >= 5.2.5
		

		//shorten file path
		@ $file = preg_replace ( '{^.*?muco/}', '', $backtrace [$backTraceLevel] ['file'] );
		
		@ $msg .= " ({$file}:{$backtrace[$backTraceLevel]['line']})";
		
		return array ($msg, $obj );
	}
}


	