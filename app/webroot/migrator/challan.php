<?php
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

//End Time Difference
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
function getBatchNo($dssconn, $skubatchid){
    $queryn = "select * from [SKUBatch] where SKUBatchID = $skubatchid";
    $getESStoreIDQue = sqlsrv_query($dssconn, $queryn);
    while($obj = sqlsrv_fetch_object($getESStoreIDQue)) {
        return $obj->ManufacturerBatchNo;
    }
}
$queryDateStrForChallan = getSyncTime(true,$lagMinForChallan);
//Get Challans
$query = "Select * from dbo.ASOIssues asi
inner join dbo.ASOIssueItem asitm on asitm.ASOIssueID = asi.ASOIssueID
inner join dbo.SKUs sku on asitm.SKUID = sku.SKUID and (sku.InventoryTypeID = 43 or sku.InventoryTypeID = 47)
inner join dbo.ASOIssueDeliveryRecord asidc on asidc.ASOIssueID = asi.ASOIssueID
inner join dbo.SKUBatch sb on asitm.SKUBatchID = sb.SKUBatchID
where (asi.CreatedDate >= '$queryDateStrForChallan' OR asi.ModifiedDate >= '$queryDateStrForChallan') and asi.Status = 1";
$dssChallanQueryResult = sqlsrv_query($dssconn, $query );
$data = [];
$challanNoList = [];
if($dssChallanQueryResult){
    while($row = sqlsrv_fetch_array($dssChallanQueryResult, SQLSRV_FETCH_ASSOC) ) {
            $row['BatchNo'] = getBatchNo($dssconn, $row['SKUBatchID']);
            $data[] = $row;
            if(!in_array($row['ChallanNo'],$challanNoList))
                $challanNoList[] = $row['ChallanNo'];
    }
}

//Get CWH Stocks
$datacwhstock = [];
$dssCWHStockQuery = "Select * from dbo.vwSKUStock";
//where rc.created_at  >= DATEADD(hour, -12, GETDATE())
$dssCWHStockQueryResult = sqlsrv_query($dssconn, $dssCWHStockQuery);
while($cwhrow = sqlsrv_fetch_array($dssCWHStockQueryResult, SQLSRV_FETCH_ASSOC) ) {
    $datacwhstock[] = $cwhrow;
}

sqlsrv_close($dssconn);

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
$esalesStockQuery = "SELECT store_id,m_unit,product_id,SUM(qty) as quantity FROM dbo.current_inventories GROUP BY store_id,m_unit,product_id";
$esalesStockQueryResult = sqlsrv_query($esalesconn, $esalesStockQuery);
while($esrow = sqlsrv_fetch_array($esalesStockQueryResult, SQLSRV_FETCH_ASSOC) ) {
    $esalesStock[] = $esrow;
}

// Challan Create Update
function getEsalesStoreID($esalesconn, $dssstrid){
	$queryn = "select * from [store_map] where dss_store_id = $dssstrid"; 
	$getESStoreIDQue = sqlsrv_query($esalesconn, $queryn);
    if($getESStoreIDQue){
        if (sqlsrv_has_rows($getESStoreIDQue)) {
            while ($obj = sqlsrv_fetch_object($getESStoreIDQue)) {
                return $obj->esales_store_id;
            }
        } else {
            return "0";
        }
    }
}

function getEsalesStoreName($esalesconn, $esalessstrid){
    $queryn = "select * from [stores] where id = $esalessstrid";
    $getESStoreIDQue = sqlsrv_query($esalesconn, $queryn);
    if($getESStoreIDQue){
        if (sqlsrv_has_rows($getESStoreIDQue)) {
            while ($obj = sqlsrv_fetch_object($getESStoreIDQue)) {
                return $obj->name;
            }
        } else {
            return "0";
        }
    }
}

function getEsalesStoreTypeByEsalesStoreId($esalesconn,$storeId){
    $queryn = "select sttype.name from [stores] st INNER JOIN [store_types] sttype ON (st.store_type_id = sttype.id) where st.id = $storeId";
    $result = sqlsrv_query($esalesconn, $queryn);
    if($result){
        if (sqlsrv_has_rows($result)) {
            while ($obj = sqlsrv_fetch_object($result)) {
                return $obj->name;
            }
        } else {
            return "0";
        }
    }
}

function getEsalesProductById($esalesconn,$productId){
    $queryn = "select p.name as product_name,ptype.name as product_type,pbrand.name as product_brand, pcat.name as product_category
        from [products] p
        INNER JOIN [product_type] ptype ON (p.product_type_id = ptype.id)
        INNER JOIN [product_map] pm ON (p.id = pm.esales_product_id)
        INNER JOIN [brands] pbrand ON (p.brand_id = pbrand.id)
        INNER JOIN [product_categories] pcat ON (p.product_category_id = pcat.id)
        where p.id = $productId";
    $result = sqlsrv_query($esalesconn, $queryn);
    if($result){
        if (sqlsrv_has_rows($result)) {
            while ($obj = sqlsrv_fetch_array($result)) {
                return $obj;
            }
        } else {
            return "0";
        }
    }
}


function getEsalesRegionOfficeByOfficeId($esalesconn,$officeId){
    $queryn = "SELECT o.id,o.office_name
    FROM offices o
    where o.id = $officeId";
    $result = sqlsrv_query($esalesconn, $queryn);
    if($result){
        if (sqlsrv_has_rows($result)) {
            while ($obj = sqlsrv_fetch_array($result)) {
                return $obj;
            }
        }
        else{
            return "0";
        }
    }
}

function getEsalesParentOfficeByOfficeId($esalesconn,$officeId){
    $queryn = "SELECT o.parent_office_id
    FROM offices o
    where o.id = $officeId";
    $result = sqlsrv_query($esalesconn, $queryn);
    if($result){
        if (sqlsrv_has_rows($result)) {
            while ($obj = sqlsrv_fetch_object($result)) {
                return $obj->parent_office_id;
            }
        }
        else{
            return "0";
        }
    }
}

function getEsalesOfficeByStore($esalesconn,$storeId){
    $queryn = "SELECT s.office_id
    FROM stores s
    where s.id = $storeId";
    $result = sqlsrv_query($esalesconn, $queryn);
    if($result){
        if (sqlsrv_has_rows($result)) {
            while ($obj = sqlsrv_fetch_object($result)) {
                return $obj->office_id;
            }
        }
        else{
            return "0";
        }
    }
}

function getEsalesUnitID($esalesconn, $dssunitid){
	$queryn = "select * from [unit_measurement_map] where dss_unit_id = $dssunitid"; 
	$getESUnitIDQue = sqlsrv_query($esalesconn, $queryn);
    if($getESUnitIDQue){
        if (sqlsrv_has_rows($getESUnitIDQue)) {
            while ($obj = sqlsrv_fetch_object($getESUnitIDQue)) {
                return $obj->esales_measurement_unit_id;
            }
        } else {
            return "0";
        }
    }
}
function getEsalesProductID($esalesconn, $dssprodid){
	$queryn = "select * from [product_map] where dss_sku_id = $dssprodid";
	$getESProdIDQue = sqlsrv_query($esalesconn, $queryn);
	if($getESProdIDQue){
		if (sqlsrv_has_rows($getESProdIDQue)) {
		  while ($obj = sqlsrv_fetch_object($getESProdIDQue)) {
			return $obj->esales_product_id;
		  }
		} else {
		  return "0";
		}
	}
}



function checkExistChallan($esalesconn, $challanno){
    $queryn = "select * from [challans] where challan_no = '$challanno'";
    $checkExistChalQue = sqlsrv_query($esalesconn, $queryn);
    $challanId = -1;

    if($checkExistChalQue) {
        if (sqlsrv_has_rows($checkExistChalQue)) {
            while ($obj = sqlsrv_fetch_object($checkExistChalQue)) {
                $challanId = $obj->id;
                break;
            }
        }
    }

    return $challanId;
}


function createNewChallan($esalesconn, $obj){
    $time = date("Y-m-d H:i:s");
    $issDateObj = $obj['IssueDate'];
    $tsql= "INSERT INTO [challans] (
            challan_no,
            challan_date,
            sender_store_id,
            requisition_id,
            transaction_type_id,
            inventory_status_id,
            status,
            receiver_store_id,
            created_at,
            created_by,
            updated_by,
            driver_name,
            truck_no)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $data = array($obj['ChallanNo'], $issDateObj, $obj['EsalesFromStoreID'], 0, 1, 1,  $obj['Status'], $obj['EsalesToStoreID'], $time, 1, 1, $obj['DriverName'], $obj['TruckNo']);
    if (!sqlsrv_query($esalesconn, $tsql, $data)){
        echo "--------------ERROR CHALLAN START-----------------"."<br/>";
        print_r(sqlsrv_errors());
        echo "--------------ERROR CHALLAN END-----------------"."<br/>";
    } else {
        echo $obj['ChallanNo']." Challan Record added\n";
    }

}
function createNewChallanItem($esalesconn, $challanId, $obj){
    $prdid = $obj['EsalesProductID'];
    $unitid = $obj['EsalesUnitID'];
    if(!($obj['EsalesProductID']=="0" || $obj['EsalesUnitID']=="0")) {
        $tsql = "INSERT INTO [challan_details] (
            challan_id,
            product_id,
            measurement_unit_id,
            challan_qty,
            received_qty,
            batch_no,
            expire_date,
            inventory_status_id,
            remarks,
            remaining_qty,
            source)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $data = array((int)$challanId, $prdid, (int)$unitid, (double)$obj['IssueQty'], 0.00, $obj['BatchNo'], $obj['ExpiryDate'], 1, $obj['Remarks'], 0.00, '');
        if (!sqlsrv_query($esalesconn, $tsql, $data)) {
            echo "--------------ERROR CHALLAN ITEM START-----------------"."<br/>";
            print_r(sqlsrv_errors());
            echo "--------------ERROR CHALLAN ITEM END-----------------"."<br/>";
        } else {
            echo $challanId." Challan Record added\n";
        }
    }
}
$existingChallanIds = [];
for ($i=0; $i<count($challanNoList);$i++) {
    $challanId = checkExistChallan($esalesconn, $challanNoList[$i]);
    if ( $challanId != -1 ){
        $existingChallanIds[] = $challanId;
    }
}

if($challanMigrate){
    for ($i=0; $i<count($data);$i++) {
    //    print_r(json_encode($data[$i]));
        $data[$i]['EsalesFromStoreID'] = getEsalesStoreID($esalesconn, $data[$i]['IssueFromStoreAreaID']);
        $data[$i]['EsalesToStoreID'] = getEsalesStoreID($esalesconn, $data[$i]['IssueToStoreAreaID']);
        $data[$i]['EsalesUnitID'] =  getEsalesUnitID($esalesconn, $data[$i]['UnitID']);
        $data[$i]['EsalesProductID'] = getEsalesProductID($esalesconn, $data[$i]['SKUID']);
        $challanId = checkExistChallan($esalesconn, $data[$i]['ChallanNo']);

        if(in_array($challanId,$existingChallanIds)){
            continue;
        }

        if ( $challanId == -1 ){
            createNewChallan($esalesconn, $data[$i]);
            $challanId = checkExistChallan($esalesconn, $data[$i]['ChallanNo']);
        }
        createNewChallanItem($esalesconn, $challanId, $data[$i]);
    }
}

// CWH Stock Create/Update
function checkCWHInvProductExist($esalesconn, $esprodid, $esstoreid){
    if($esprodid!=null) {
        $queryn = "Select * from [current_cwh_inventories] where store_id = $esstoreid and product_id = $esprodid and transaction_date= '".TRANSACTION_DATE."'";
        //echo $queryn.'</br>';exit;
        $checkExistRetQue = sqlsrv_query($esalesconn, $queryn);
        if ($checkExistRetQue) {
            if (sqlsrv_has_rows($checkExistRetQue)) {
                return true;
            } else {
                return false;
            }
        }
    }
}
function createCWHInvItem($esalesconn, $obj){
    if(!($obj['EsalesProductID']=="0" || $obj['EsalesStoreID']=="0")) {
        $time = date("Y-m-d H:i:s");
        $tsql = "INSERT INTO [current_cwh_inventories] (
            store_id,
            inventory_status_id,
            product_id,
            batch_number,
            expire_date,
            m_unit,
            qty,
            updated_at,
            transaction_date,
            transaction_type_id,
            store_type,
            store_name,
            region_office_id,
            region_office_name,
            product_name,
            product_type,
            product_brand,
            product_category)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $data = array($obj['EsalesStoreID'], 1, $obj['EsalesProductID'], '', '',
            $obj['EsalesUnitID'], $obj['StockQuantity'], $time, TRANSACTION_DATE, '',
            $obj['StoreType'],$obj['EsalesStoreName'],isset($obj['EsalesOfficeID'])?$obj['EsalesOfficeID']:0,isset($obj['EsalesOfficeName'])?$obj['EsalesOfficeName']:"Head Office",$obj['ProductName'],$obj['ProductType'],
            $obj['ProductBrand'],$obj['ProductCategory']);
        if (!sqlsrv_query($esalesconn, $tsql, $data)) {
            echo print_r(sqlsrv_errors());
        } else {
            echo $obj['StoreType'].'----'.$obj['ProductName'].'----'.$obj['ProductType']." Stock Record added </br>";
        }
    } else {
        echo $obj['StoreType'].'----'.$obj['ProductName'].'----'.$obj['ProductType'].' Invalid Data<br/>';
    }
}
function updateCWHInvItem($esalesconn, $obj){
    $upQuantity = $obj['StockQuantity'];
    $time = date("Y-m-d H:i:s");
    $prodid = $obj['EsalesProductID'];
    $storeid = $obj['EsalesStoreID'];

    $sql = "UPDATE current_cwh_inventories SET
			qty = ? ,
			updated_at = ?
			WHERE store_id  = ?
            AND product_id = ?
            AND transaction_date = ?";
    $params = array($upQuantity, $time, $storeid, $prodid, TRANSACTION_DATE);
    if (!sqlsrv_query( $esalesconn, $sql, $params)){
        echo "--------------ERROR LINE 400  START-----------------"."<br/>";
        print_r(sqlsrv_errors());
        echo "--------------ERROR LINE 400  END-----------------"."<br/>";
    } else {
        echo "CWH Inv Record updated </br>";
    }
}

if($nationalStockMigrate){
    for ($i=0; $i<count($datacwhstock);$i++) {
        $esprodid = getEsalesProductID($esalesconn, $datacwhstock[$i]['SKUID']);
        $esstoreid = getEsalesStoreID($esalesconn, $datacwhstock[$i]['StoreID']);
        $esunitid = getEsalesUnitID($esalesconn, $datacwhstock[$i]['UnitID']);
        $datacwhstock[$i]['EsalesProductID'] = $esprodid;
        $datacwhstock[$i]['EsalesStoreID'] = $esstoreid;
        $datacwhstock[$i]['EsalesStoreName'] = getEsalesStoreName($esalesconn,$esstoreid);
        $datacwhstock[$i]['EsalesUnitID'] = $esunitid;

        $datacwhstock[$i]['StoreType'] = getEsalesStoreTypeByEsalesStoreId($esalesconn,$esstoreid);
        $esalesProduct = getEsalesProductById($esalesconn,$esprodid);

        if($esalesProduct == "0"){
            continue;
        }

        $datacwhstock[$i]['ProductType'] = isset($esalesProduct["product_type"])?$esalesProduct["product_type"]:"0";
        $datacwhstock[$i]['ProductName'] = isset($esalesProduct["product_name"])?$esalesProduct["product_name"]:"0";
        $datacwhstock[$i]['ProductBrand'] = isset($esalesProduct["product_brand"])?$esalesProduct["product_brand"]:"0";
        $datacwhstock[$i]['ProductCategory'] = isset($esalesProduct["product_category"])?$esalesProduct["product_category"]:"0";

        if(!checkCWHInvProductExist($esalesconn, $esprodid, $esstoreid)) {
            createCWHInvItem($esalesconn, $datacwhstock[$i]);
        } else {
            updateCWHInvItem($esalesconn, $datacwhstock[$i]);
        }
    }



    for ($i=0; $i<count($esalesStock);$i++) {

        $esalesStock[$i]['StoreType'] = getEsalesStoreTypeByEsalesStoreId($esalesconn,$esalesStock[$i]['store_id']);
        $esalesProduct = getEsalesProductById($esalesconn,$esalesStock[$i]['product_id']);

        $officeId = getEsalesOfficeByStore( $esalesconn, $esalesStock[$i]['store_id']);
        $parentOfficeId = getEsalesParentOfficeByOfficeId( $esalesconn, $officeId);

        $esalesRegionOffice = [ "id"=>0, "office_name"=>"Head Office" ];

        if($parentOfficeId !== 0)
        {
            $esalesRegionOffice = getEsalesRegionOfficeByOfficeId($esalesconn,$parentOfficeId);
        }
        else{
            var_dump($esalesStock[$i]['store_id']);
            var_dump($officeId);
            var_dump($parentOfficeId);
            var_dump($esalesRegionOffice);exit;
        }




        if($esalesProduct == "0"){
            continue;
        }

        $esalesStock[$i]['ProductType'] = isset($esalesProduct["product_type"])?$esalesProduct["product_type"]:"0";
        $esalesStock[$i]['ProductName'] = isset($esalesProduct["product_name"])?$esalesProduct["product_name"]:"0";
        $esalesStock[$i]['ProductBrand'] = isset($esalesProduct["product_brand"])?$esalesProduct["product_brand"]:"0";
        $esalesStock[$i]['ProductCategory'] = isset($esalesProduct["product_category"])?$esalesProduct["product_category"]:"0";

        $esalesStock[$i]['EsalesProductID'] = $esalesStock[$i]['product_id'];
        $esalesStock[$i]['EsalesStoreID'] = $esalesStock[$i]['store_id'];
        $esalesStock[$i]['EsalesStoreName'] = getEsalesStoreName($esalesconn,$esalesStock[$i]['store_id']);

        $esalesStock[$i]['EsalesOfficeID'] = $esalesRegionOffice['id'];
        $esalesStock[$i]['EsalesOfficeName'] = $esalesRegionOffice['office_name'];

        $esalesStock[$i]['EsalesUnitID'] = $esalesStock[$i]['m_unit'];
        $esalesStock[$i]['StockQuantity'] = $esalesStock[$i]['quantity'];

        if(!checkCWHInvProductExist($esalesconn, $esalesStock[$i]['EsalesProductID'] , $esalesStock[$i]['EsalesStoreID'])) {
            createCWHInvItem($esalesconn, $esalesStock[$i]);
        } else {
            updateCWHInvItem($esalesconn, $esalesStock[$i]);
        }
    }
}

sqlsrv_close($esalesconn);

?>