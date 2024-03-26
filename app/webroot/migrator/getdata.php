<?php
include 'databaseinfo.php';
//Connection Strings Starts

$esalesconnectionInfo = array( "UID"=>$esalesUid,
                               "PWD"=>$esalesPwd,
                               "Database"=> $esalesDatabaseName);
/* Connect using SQL Server Authentication. */
$esalesconn = sqlsrv_connect( $esalesServerName, $esalesconnectionInfo);
if( $esalesconn ) {
}else{
    die( print_r( sqlsrv_errors(), true));
}

$queryData = $_GET['query_data'];
$colInd = $_GET['col_ind'];
$res_array=array();
if($queryData != null && !empty($queryData))
{
    $qurey = $queryData;
    $qResult = sqlsrv_query($esalesconn, $qurey);
    if($qResult){
        while ($row = sqlsrv_fetch_array($qResult, SQLSRV_FETCH_ASSOC)) {
            array_push($res_array,$row[$colInd]);
        }
    }
}

echo json_encode($res_array);