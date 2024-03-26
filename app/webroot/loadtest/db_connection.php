<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
/*
$serverName = "Database/Sqlserver"; //serverName\instanceName
$connectionInfo = array("Database" => "Smc_sales_live_edited_cln", "UID" => "smc_user", "PWD" => "user123456");
*/

//$serverName = "DESKTOP-60ICTV0\SQLEXPRESS"; 
// New Database 
$start_time = date('Y-m-d H:i:s');
echo $start_time . ':' . "Request start  : <br>";
$serverName = "10.1.1.94"; //serverName\instanceNam
$connectionInfo = array("Database" => "Smc_uat", "UID" => "sa", "PWD" => "Arena123");
//$connectionInfo = array("Database" => "Smc_uat_test", "UID" => "smc_user", "PWD" => "user123456");

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn) {
    echo "Connection established.<br />";
	$end_time = date('Y-m-d H:i:s');
	$crrentSysDate = new DateTime($start_time);
	$userDefineDate = $crrentSysDate->format('m/d/y h:i:s a');

	$start = date_create($userDefineDate);
	$end = date_create(date('m/d/y h:i:s a', strtotime($end_time)));

	$diff = date_diff($start, $end);
	
	echo $end_time . ':' . "Request closed :" . $diff->i . " Min " . $diff->s . " Sec -- <br>" ; 
} else {
    echo "Connection could not be established for New Database.<br />";
	echo '<pre>';
	print_r(sqlsrv_errors());
    exit;
}
