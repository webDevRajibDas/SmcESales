<?php
$dssconnectionInfo = array("UID"=>$dssUid,
    "PWD"=>$dssPwd,
    "Database"=>$dssDatabaseName);

/* Connect using SQL Server Authentication. */

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

$esalesStock = [];
$esalesStockQuery = "SELECT cwhinv.store_id, cwhinv.store_name,cwhinv.region_office_id, cwhinv.region_office_name, sm.dss_store_id, cwhinv.store_type,cwhinv.product_type,cwhinv.product_name,
cwhinv.product_category,cwhinv.product_brand,cwhinv.transaction_date,pm.dss_sku_id,cwhinv.qty,umm.dss_unit_id
FROM [current_cwh_inventories]  cwhinv
left join product_map pm on (cwhinv.product_id = pm.esales_product_id)
left join store_map sm on (cwhinv.store_id = sm.esales_store_id)
left join unit_measurement_map umm on (cwhinv.m_unit = umm.esales_measurement_unit_id)
where transaction_date='".TRANSACTION_DATE."'";
$esalesStockQueryResult = sqlsrv_query($esalesconn, $esalesStockQuery);
while($esrow = sqlsrv_fetch_array($esalesStockQueryResult, SQLSRV_FETCH_ASSOC) ) {
    $esalesStock[] = $esrow;
}

sqlsrv_close($esalesconn);

$dssconn = sqlsrv_connect( $dssServerName, $dssconnectionInfo);
if( $dssconn ) {
    echo "DSS DB Connection established.<br />";
}else{
    echo "DSS DB  Connection could not be established.<br />";
    die( print_r( sqlsrv_errors(), true));
}


function getDSSProductById($dssconn,$productId){
    $queryn = "select s.Name from [SKUs] s
        where s.SKUID = $productId";
    $result = sqlsrv_query($dssconn, $queryn);
    if($result){
        if (sqlsrv_has_rows($result)) {
            while ($obj = sqlsrv_fetch_object($result)) {
                return $obj->Name;
            }
        } else {
            return "0";
        }
    }
}

// CWH Stock Create/Update
function checkDSSInvProductExist($dssconn, $dssprodid, $esstoreid){
    if($dssprodid!=null) {
        $queryn = "Select * from [NationalInventories] where EsalesStoreId = $esstoreid and ProductId = $dssprodid and TransactionDate='".TRANSACTION_DATE."'";
        $checkExistRetQue = sqlsrv_query($dssconn, $queryn);
        if ($checkExistRetQue) {
            if (sqlsrv_has_rows($checkExistRetQue)) {
                return true;
            } else {
                return false;
            }
        }
    }
    return false;
}
function createDSSInvItem($dssconn, $obj){
    if(!($obj['dss_sku_id']=="0" || $obj['store_id']=="0")) {
        $time = date("Y-m-d H:i:s");
        $tsql = "INSERT INTO [NationalInventories]
           ([EsalesStoreId]
           ,[EsalesStoreName]
           ,[EsalesRegionOfficeId]
           ,[EsalesRegionOfficeName]
           ,[StoreId]
           ,[StoreType]
           ,[ProductName]
           ,[ProductType]
           ,[ProductCategory]
           ,[ProductBrand]
           ,[ProductId]
           ,[UnitId]
           ,[Qty]
           ,[UpdatedAt]
           ,[TransactionDate])
     VALUES
           (?
           ,?
           ,?
           ,?
           ,?
           ,?
           ,?
           ,?
           ,?
           ,?
           ,?
           ,?
           ,?
           ,?
           ,?)";
        $data = array($obj['store_id'],$obj['store_name'],$obj['region_office_id'],$obj['region_office_name'], $obj['dss_store_id'], $obj['store_type'],
        $obj['ProductName'],$obj['product_type'],$obj['product_category'],$obj['product_brand'],$obj['dss_sku_id'],$obj['dss_unit_id'],
        $obj['qty'],$time,$obj['transaction_date']);
        if (!sqlsrv_query($dssconn, $tsql, $data)) {
            echo "Create Error Occurred </br>";
        } else {
            echo $obj['store_type'].'----'.$obj['ProductName'].'----'.$obj['product_type']." Stock Record added </br>";
        }
    } else {
        echo $obj['store_type'].'----'.$obj['ProductName'].'----'.$obj['product_type'].' Invalid Data<br/>';
    }
}
function updateDSSInvItem($dssconn, $obj){
    $upQuantity = $obj['qty'];
    $time = date("Y-m-d H:i:s");
    $prodid = $obj['dss_sku_id'];
    $storeid = $obj['store_id'];
    $transactionDate = $obj['transaction_date'];

    $sql = "UPDATE NationalInventories SET
			Qty = ? ,
			UpdatedAt = ?
			WHERE EsalesStoreId  = ?
            AND ProductId = ?
            AND TransactionDate = ?";
    $params = array($upQuantity, $time, $storeid, $prodid, $transactionDate);
    if (!sqlsrv_query( $dssconn, $sql, $params)){
        echo "Update Error Occurred </br>";
    } else {
        echo "DSS Inv Record updated </br>";
    }
}


if($nationalStockMigrate){
    for ($i=0; $i<count($esalesStock);$i++) {
        $dssProduct = getDSSProductById($dssconn,$esalesStock[$i]['dss_sku_id']);
        $esalesStock[$i]['ProductName'] = $dssProduct!="0"?$dssProduct:$esalesStock[$i]['product_name'];

        if(!checkDSSInvProductExist($dssconn, $esalesStock[$i]['dss_sku_id'] , $esalesStock[$i]['store_id'])) {
            createDSSInvItem($dssconn, $esalesStock[$i]);
        } else {
            updateDSSInvItem($dssconn, $esalesStock[$i]);
        }
    }
}

sqlsrv_close($dssconn);

?>