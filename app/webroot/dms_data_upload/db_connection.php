<?php
/*
$serverName = "Database/Sqlserver"; //serverName\instanceName
$connectionInfo = array("Database" => "Smc_sales_live_edited_cln", "UID" => "smc_user", "PWD" => "user123456");
*/

//$serverName = "DESKTOP-60ICTV0\SQLEXPRESS"; 
// New Database 
$serverName = "localhost"; //serverName\instanceNam
$connectionInfo = array("Database" => "Smc_uat", "UID" => "sa", "PWD" => "123456");
//$connectionInfo = array("Database" => "Smc_uat_test", "UID" => "smc_user", "PWD" => "user123456");

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn) {
    //echo "Connection established.<br />";
} else {
    echo "Connection could not be established for New Database.<br />";
    die(print_r(sqlsrv_errors(), true));
}
