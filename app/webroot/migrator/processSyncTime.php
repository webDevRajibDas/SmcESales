<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/25/2018
 * Time: 1:12 AM
 */

//Check Time Difference
date_default_timezone_set('asia/dhaka');

function getSyncTime($lag = true,$laginMin = 10) {
    $file = "timeconfigure.txt";
    $json = json_decode(file_get_contents($file), true);
    $LastSyncDateTime = $json['LastSyncDateTime'];
    $LastSyncDateTimeDateObj = new DateTime($LastSyncDateTime);
    $LaggingMinutesToAdd = $lag?$laginMin:0;
    $LastSyncDateTimeDateObj->modify("-{$LaggingMinutesToAdd} minutes");
    return $queryDateStr = date_format($LastSyncDateTimeDateObj, 'Y-m-d H:i:s');
}

//Write SyncTime Log Again
function setSyncTime() {
    $file = "timeconfigure.txt";
    $newjson = array("LastSyncDateTime" => date('Y-m-d H:i:s', time()));
    file_put_contents($file, json_encode($newjson));
}

?>