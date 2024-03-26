<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/31/2018
 * Time: 6:06 PM
 */
require_once 'processSyncTime.php';
$queryDateStr = getSyncTime();
$transDate = date("Y-m-d");
define('TRANSACTION_DATE',$transDate);
include 'databaseinfo.php';
include 'challan.php';
include 'return.php';
include 'stockupdatetodss.php';
setSyncTime();
?>