<?php
/* Connect using SQL Server Authentication. */
$dssconn = sqlsrv_connect( $dssServerName, $dssconnectionInfo);
if( $dssconn ) {
    echo "DSS DB Connection established.<br />";
}else{
    echo "DSS DB  Connection could not be established.<br />";
    die( print_r( sqlsrv_errors(), true));
}
/* Connect using SQL Server Authentication. */
$esalesconn = sqlsrv_connect( $esalesServerName, $esalesconnectionInfo);
if( $esalesconn ) {
    echo "ESALES DB Connection established.<br />";
}else{
    echo "ESALES DB  Connection could not be established.<br />";
    die( print_r( sqlsrv_errors(), true));
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

$query = "Select *, rc.id as ReturnID, rcd.id as ReturnItemID from return_challans rc
inner join return_challan_details rcd on rc.id = rcd.challan_id
where rc.created_at >= '$queryDateStr'";

$esRetChalQueryResult = sqlsrv_query($esalesconn, $query );
$returnData = [];
while($row = sqlsrv_fetch_array($esRetChalQueryResult, SQLSRV_FETCH_ASSOC) ) {
    $row['DSSFromStoreID'] = getDSSStoreID($esalesconn, $row['sender_store_id']);
    $row['DSSToStoreID'] = getDSSStoreID($esalesconn, $row['receiver_store_id']);
    $row['DSSUnitID'] =  getDSSUnitID($esalesconn, $row['measurement_unit_id']);
    $row['DSSProductID'] = getDSSProductID($esalesconn, $row['product_id']);
    $returnData[] = $row;
}

$asoStockQuery = "Select crin.*, prods.base_measurement_unit_id as meas_unit from current_inventories crin
inner join products prods on (crin.product_id = prods.id)
inner join store_map sm on (crin.store_id = sm.esales_store_id)
where crin.updated_at >= '$queryDateStr'";
$asoStockQueryResult = sqlsrv_query($esalesconn, $asoStockQuery );
$dataAsoStock = [];
while($asoStockRow = sqlsrv_fetch_array($asoStockQueryResult, SQLSRV_FETCH_ASSOC) ) {
    $asoStockRow['DSSStoreID'] = getDSSStoreID($esalesconn, $asoStockRow['store_id']);
    $asoStockRow['DSSUnitID'] =  getDSSUnitID($esalesconn, $asoStockRow['meas_unit']);
    $asoStockRow['DSSProductID'] = getDSSProductID($esalesconn, $asoStockRow['product_id']);
    $dataAsoStock[] = $asoStockRow;
}
sqlsrv_close($esalesconn);

function checkExistReturn($dssconn, $returnid, $returnitemid){
    $queryn = "Select * from [ASOReturnItemHistory] where ReturnID = $returnid and ReturnItemID=$returnitemid";
    $checkExistRetQue = sqlsrv_query($dssconn, $queryn);
    if($checkExistRetQue) {
        if (sqlsrv_has_rows($checkExistRetQue)) {
            return true;
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

if($asoReturnMigrate){
    for ($i=0; $i<count($returnData);$i++) {
    //    print_r(json_encode($returnData[$i]));
        $isRetExist = checkExistReturn($dssconn, $returnData[$i]['ReturnID'], $returnData[$i]['ReturnItemID']);
        if (!$isRetExist){
            createReturn($dssconn, $returnData[$i]);
        }
    }
}

function checkAsoStockProduct($dssconn, $storeid, $prodid){
    $queryn = "Select * from [ASOSKUStock] where StoreID = $storeid and SKUID = $prodid";
    $checkExistAsoStockQue = sqlsrv_query($dssconn, $queryn);
    if($checkExistAsoStockQue) {
        if (sqlsrv_has_rows($checkExistAsoStockQue)) {
            return true;
        } else {
            return false;
        }
    }
}
function createAsoStockEntry($dssconn, $obj){
    $currdatetime = date("Y-m-d H:i:s");
    $tsql= "INSERT INTO [ASOSKUStock] (
            SKUID,
            UnitID,
            StoreID,
            StockQty,
            ASOID,
            ASOStockQty,
            ASOStockCreateDate)
            VALUES
            (?, ?, ?, ?, ?, ?, ?)";
    $data = array((int)$obj['DSSProductID'], (int)$obj['DSSUnitID'], (int)$obj['DSSStoreID'], $obj['qty'], 0, 0.00, $currdatetime);
    if (!sqlsrv_query($dssconn, $tsql, $data)){
        echo "Error Occurred ASOEntry </br>";
    } else {
        echo "1 ASOStockEntry Record added\n";
    }

}
function updateAsoStockEntry($dssconn, $obj){
    $upQuantity = $obj['qty'];
    $time = date("Y-m-d H:i:s");
    $prodid = $obj['DSSProductID'];
    $storeid = $obj['DSSStoreID'];
    $sql = "UPDATE ASOSKUStock SET
			StockQty = ?,
			ASOStockCreateDate = ?
			WHERE SKUID  = ?
            AND StoreID = ?";
    $params = array($upQuantity, $time, $prodid, $storeid);
    if (!sqlsrv_query( $dssconn, $sql, $params)){
        echo "Update Error Occurred </br>";
//        print_r(sqlsrv_errors());
    } else {
        echo "1 ASOStock Entry Record updated </br>";
    }
}

if($asoStockMigrate){
    for($i=0; $i<count($dataAsoStock);$i++) {
        $isASOStockExist = checkAsoStockProduct($dssconn, $dataAsoStock[$i]['DSSStoreID'], $dataAsoStock[$i]['DSSProductID']);
        if(!$isASOStockExist) {
            createAsoStockEntry($dssconn, $dataAsoStock[$i]);
        } else {
            updateAsoStockEntry($dssconn, $dataAsoStock[$i]);
        }
    }
}
sqlsrv_close($dssconn);
?>