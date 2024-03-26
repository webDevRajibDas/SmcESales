<?php
include 'databaseinfo.php';

$dssconnectionInfo = array("UID"=>$dssUid,
                           "PWD"=>$dssPwd,
                           "Database"=>$dssDatabaseName);
/* Connect using SQL Server Authentication. */
$dssconn = sqlsrv_connect( $dssServerName, $dssconnectionInfo);
if( $dssconn ) {
    echo "DSS DB Connection established.<br />";
}else{
    echo "DSS DB  Connection could not be established.<br />";
    die( print_r( sqlsrv_errors(), true));
}


$esalesconnectionInfo = array( "UID"=>$esalesUid,
                               "PWD"=>$esalesPwd,
                               "Database"=> $esalesDatabaseName);
/* Connect using SQL Server Authentication. */
$esalesconn = sqlsrv_connect( $esalesServerName, $esalesconnectionInfo);
if( $esalesconn ) {
    echo "ESALES DB Connection established.<br />";
}else{
    echo "ESALES DB  Connection could not be established.<br />";
    die( print_r( sqlsrv_errors(), true));
}

sqlsrv_close($dssconn);
sqlsrv_close($esalesconn);