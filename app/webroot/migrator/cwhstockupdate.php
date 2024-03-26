<?php
//Connection Strings Starts
$serverName = "USER-PC\\SQLEXPRESS";
$uid = "sayem_user";
$pwd = "say2018";
$databaseName = "SMCDSS";

$dssconnectionInfo = array( "UID"=>$uid,
    "PWD"=>$pwd,
    "Database"=>$databaseName);
/* Connect using SQL Server Authentication. */
$dssconn = sqlsrv_connect( $serverName, $dssconnectionInfo);
if( $dssconn ) {
    echo "DSS DB Connection established.<br />";
}else{
    echo "DSS DB  Connection could not be established.<br />";
    die( print_r( sqlsrv_errors(), true));
}

$esalesconnectionInfo = array( "UID"=>$uid,
    "PWD"=>$pwd,
    "Database"=> 'Smc_sales_live_edited_cln');
/* Connect using SQL Server Authentication. */
$esalesconn = sqlsrv_connect( $serverName, $esalesconnectionInfo);
if( $esalesconn ) {
    echo "ESALES DB Connection established.<br />";
}else{
    echo "ESALES DB  Connection could not be established.<br />";
    die( print_r( sqlsrv_errors(), true));
}
//Connections  Strings End

function checkInventoryType($dssconn, $prodid){
    $queryn = "select * from [SKUs] where SKUID = $prodid";
    $getInvProdTypeQue = sqlsrv_query($dssconn, $queryn);
    if($getInvProdTypeQue){
        if (sqlsrv_has_rows($getInvProdTypeQue)) {
            while ($obj = sqlsrv_fetch_object($getInvProdTypeQue)) {
                return $obj->InventoryTypeID;
            }
        } else {
            return "";
        }
    }
}

function getDSSStoreID($esalesconn, $esstrid){
    $queryn = "select * from [store_map] where esales_store_id = $esstrid";
    $getDSSStoreIDQue = sqlsrv_query($esalesconn, $queryn);
    while($obj = sqlsrv_fetch_object($getDSSStoreIDQue)) {
        return $obj->dss_store_id;
    }
}
function getDSSUnitID($esalesconn, $unitid){
    $queryn = "select * from [unit_measurement_map] where esales_measurement_unit_id = $unitid";
    $getESUnitIDQue = sqlsrv_query($esalesconn, $queryn);
    while($obj = sqlsrv_fetch_object($getESUnitIDQue)) {
        return $obj->dss_unit_id;
    }
}
function getDSSProductID($esalesconn, $prodid){
    $queryn = "select * from [product_map] where esales_product_id = $prodid";
    $getESProdIDQue = sqlsrv_query($esalesconn, $queryn);
    if($getESProdIDQue){
        if (sqlsrv_has_rows($getESProdIDQue)) {
            while ($obj = sqlsrv_fetch_object($getESProdIDQue)) {
                return $obj->dss_sku_id;
            }
        } else {
            return "";
        }
    }
}

//$query = "Select *, rc.id as ReturnID, rcd.id as ReturnItemID from return_challans rc
//inner join return_challan_details rcd on rc.id = rcd.challan_id
//where rc.created_at = '2017-09-07 16:56:41.000'";
////where rc.created_at  >= DATEADD(hour, -12, GETDATE())
//$esRetChalQueryResult = sqlsrv_query($esalesconn, $query );
//while($row = sqlsrv_fetch_array($esRetChalQueryResult, SQLSRV_FETCH_ASSOC) ) {
//    $row['DSSFromStoreID'] = getDSSStoreID($esalesconn, $row['sender_store_id']);
//    $row['DSSToStoreID'] = getDSSStoreID($esalesconn, $row['receiver_store_id']);
//    $row['DSSUnitID'] =  getDSSUnitID($esalesconn, $row['measurement_unit_id']);
//    $row['DSSProductID'] = getDSSProductID($esalesconn, $row['product_id']);
//    $data[] = $row;
//}


$asoStockQuery = "Select crin.*, prods.base_measurement_unit_id as meas_unit from current_inventories crin
inner join products prods on crin.product_id = prods.id";
//where rc.created_at = '2017-09-07 16:56:41.000'";
//where rc.created_at  >= DATEADD(hour, -12, GETDATE())
$asoStockQueryResult = sqlsrv_query($esalesconn, $asoStockQuery );
while($asoStockRow = sqlsrv_fetch_array($asoStockQueryResult, SQLSRV_FETCH_ASSOC) ) {
    $asoStockRow['DSSStoreID'] = getDSSStoreID($esalesconn, $asoStockRow['store_id']);
    $asoStockRow['DSSUnitID'] =  getDSSUnitID($esalesconn, $asoStockRow['meas_unit']);
    $asoStockRow['DSSProductID'] = getDSSProductID($esalesconn, $asoStockRow['product_id']);
    $dataAsoStock[] = $asoStockRow;
//    print_r($asoStockRow);
//    exit;
}
sqlsrv_close($esalesconn);

function checkExistReturn($dssconn, $returnid, $returnitemid){
    $queryn = "Select * from [ASOReturnItemHistory] where ReturnID = $returnid and ReturnItemID=$returnitemid";
    $checkExistRetQue = sqlsrv_query($dssconn, $queryn);
    if($checkExistRetQue) {
        if (sqlsrv_has_rows($checkExistRetQue)) {
            return true;
//            while ($obj = sqlsrv_fetch_object($checkExistRetQue)) {
//                print_r($obj);
//            }
        } else {
            return false;
        }
    }
}
function getDSSBatchID($dssconn, $batchno){
    $queryn = "select * from [SKUBatch] where BatchNo = $batchno";
    $getESProdIDQue = sqlsrv_query($dssconn, $queryn);
    if($getESProdIDQue){
        if (sqlsrv_has_rows($getESProdIDQue)) {
            while ($obj = sqlsrv_fetch_object($getESProdIDQue)) {
                return $obj->SKUBatchID;
            }
        } else {
            return "";
        }
    }
}
function createReturn($dssconn, $obj){
    $batchid = getDSSBatchID($dssconn, $obj['challan_no']);
    $tsql= "INSERT INTO [ASOReturnItem] (
            ChallanNo,
            IssueToStoreAreaID,
            SKUID,
            UnitID,
            SKUBatchID,
            ReturnQty)
            VALUES
            (?, ?, ?, ?, ?, ?)";
    $data = array($obj['challan_no'], $obj['DSSToStoreID'], $obj['DSSProductID'], $obj['DSSUnitID'], isset($batchid) ? $batchid : 0, $obj['challan_qty']);
    if (!sqlsrv_query($dssconn, $tsql, $data)){
        echo "Error Occurred";
    } else {
        echo "1 Return Record added\n";
        createReturnHistory($dssconn, $obj);
    }

}
function createReturnHistory($dssconn, $obj){
//    ASOReturnItemHistory
    $datetime = date("Y-m-d H:i:s");
    $retissDateObj = $obj['created_at'];
    $tsql= "INSERT INTO [ASOReturnItemHistory] (
            ReturnID,
            ReturnItemID,
            ReturnChallanNo,
            ReturnCreateDate,
            ReturnHistoryCreateDate)
            VALUES
            (?, ?, ?, ?, ?)";
    $data = array($obj['ReturnID'], $obj['ReturnItemID'], $obj['challan_no'], $retissDateObj, $datetime);
    if (!sqlsrv_query($dssconn, $tsql, $data)){
        echo "Error Occurred";
    } else {
        echo "1 Return Record History Added\n";
    }
}
//function checkAsoStockProduct($dssconn, $storeid, $prodid){
//    $queryn = "Select * from [ASOReturnItemHistory] where ReturnID = $returnid and ReturnItemID=$returnitemid";
//    $checkExistRetQue = sqlsrv_query($dssconn, $queryn);
//    if($checkExistRetQue) {
//        if (sqlsrv_has_rows($checkExistRetQue)) {
//            return true;
////            while ($obj = sqlsrv_fetch_object($checkExistRetQue)) {
////                print_r($obj);
////            }
//        } else {
//            return false;
//        }
//    }
//}
function createAsoStockEntry($dssconn, $obj){
//  ASOID int NULL,
//  ASOStockQty decimal(18, 2) NULL
    $tsql= "INSERT INTO [ASOSKUStock] (
            SKUID,
            UnitID,
            StoreID,
            StockQty,
            ASOID,
            ASOStockQty)
            VALUES
            (?, ?, ?, ?, ?, ?)";
    $data = array($obj['DSSProductID'], $obj['DSSUnitID'], $obj['DSSStoreID'], $obj['qty'], '', '');
    if (!sqlsrv_query($dssconn, $tsql, $data)){
        echo "Error Occurred";
    } else {
        echo "1 Return Record added\n";
//        createReturnHistory($dssconn, $obj);
    }

}
for ($i=0; $i<count($dataAsoStock);$i++) {
//    print_r(json_encode($data[$i]));
//    $isRetExist = checkExistReturn($dssconn, $data[$i]['ReturnID'], $data[$i]['ReturnItemID']);
    //if (!$isRetExist){
    createAsoStockEntry($dssconn, $dataAsoStock[$i]);
    //}
}

for($i=0; $i<count($dataAsoStock);$i++) {

    $isStockProdExist = checkAsoStockProduct($dssconn, $data[$i]['ReturnID'], $data[$i]['ReturnItemID']);
    if (!$isRetExist){
        createReturn($dssconn, $data[$i]);
    }
}
sqlsrv_close($dssconn);
?>