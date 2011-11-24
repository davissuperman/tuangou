<?php
require 'GetRecords.php';
require 'database.php';
require 'XmlOperate.php';
xhprof_enable(); 
xhprof_enable(XHPROF_FLAGS_NO_BUILTINS); //不记录内置的函数
xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY); // 同时分析CPU和Mem的开销
$xhprof_on = true;

$getRecord_Lashou = new GetRecords('lashou');
$xmlOperator = new XmlOperate($getRecord_Lashou->getFile());
echo '<div> Great, we have download and saved. </div>';
if($xhprof_on){
$xhprof_data = xhprof_disable();
$xhprof_root = '/var/www/Coupon/xhprof/';
include_once $xhprof_root."xhprof_lib/utils/xhprof_lib.php"; 
include_once $xhprof_root."xhprof_lib/utils/xhprof_runs.php"; 
$xhprof_runs = new XHProfRuns_Default(); 
$run_id = $xhprof_runs->save_run($xhprof_data, "hx");
echo '<a href="http://localhost/Coupon/xhprof/xhprof_html/index.php?run='.$run_id.'&source=hx" target="_blank">统计</a>';
}
?>



