<?php
require 'XML.php';
//require 'GetRecords.php';
require 'database.php';
//require 'XmlOperate.php';
require 'Widget/ProgressIndicator.php';
$progress = new Widget_ProgressIndicator ();
$progress->showBar ();
$progress->setText ( "get xml" );

$arr = array ('lashou' => 'http://open.client.lashou.com/api/detail/city/上海/p/1', 'nuomi' => 'http://www.nuomi.com/api/dailydeal?version=v1&city=shanghai', 'ftuan' => 'http://newapi.ftuan.com/api/v2.aspx?city=shanghai', 'kutuan' => 'http://www.kutuan.com/tuangou/api', 'meituan' => 'http://www.meituan.com/api/v2/shanghai/deals', '58tuan' => 'open.t.58.com/api/hao123' );
$progress->setRange ( 0, count($arr)-1 );
$x = 0;
foreach ( $arr as $coupon_name => $coupon_path ) {
	$xml = new XML ( $coupon_path, $coupon_name );
	$progress->setProgress ( $x );
	$x ++;
}
$progress->setText ( "done" );
//$path = 'http://open.client.lashou.com/api/detail/city/上海/p/1';
//$couponName = 'lashou';
//$xml = new XML($path,$couponName);
//
//$path = 'http://www.nuomi.com/api/dailydeal?version=v1&city=shanghai';
//$couponName = 'nuomi';
//$xml = new XML($path,$couponName);
//
//$path = 'http://newapi.ftuan.com/api/v2.aspx?city=shanghai';
//$couponName = 'ftuan';
//$xml = new XML($path,$couponName);
//
//
//$path = 'http://www.kutuan.com/tuangou/api';
//$couponName = 'kutuan';
//$xml = new XML($path,$couponName);
//
//
//$path = 'http://www.meituan.com/api/v2/shanghai/deals';
//$couponName = 'meituan';
//$xml = new XML($path,$couponName);
//
//$path = 'open.t.58.com/api/hao123';
//$couponName = '58tuan';
//$xml = new XML($path,$couponName);
//
//$path_lashou = "http://open.client.lashou.com/api/detail/city/%E4%B8%8A%E6%B5%B7/p/1/";
//$couponName = 'lashou';
//$xml = new XML($path_lashou,$couponName);


/*
 * version 1
 */
/*
xhprof_enable(); 
xhprof_enable(XHPROF_FLAGS_NO_BUILTINS); //不记录内置的函数
xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY); // 同时分析CPU和Mem的开销
$xhprof_on = true;


$getRecord_Lashou = new GetRecords ( 'lashou' );
$xmlOperator = new XmlOperate ( $getRecord_Lashou->getFile () );
echo '<div> Great, we have download and saved. </div>';
if ($xhprof_on) {
	$xhprof_data = xhprof_disable ();
	$xhprof_root = '/var/www/Coupon/xhprof/';
	include_once $xhprof_root . "xhprof_lib/utils/xhprof_lib.php";
	include_once $xhprof_root . "xhprof_lib/utils/xhprof_runs.php";
	$xhprof_runs = new XHProfRuns_Default ();
	$run_id = $xhprof_runs->save_run ( $xhprof_data, "hx" );
	echo '<a href="http://localhost/Coupon/xhprof/xhprof_html/index.php?run=' . $run_id . '&source=hx" target="_blank">统计</a>';
}
*/
?>



