<?php
include 'databaseinfo.php';
require_once 'processSyncTime.php';
$queryDateStr = getSyncTime(false);
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

$transDate = date("Y-m-d");

$selectedCat = "";
$selectedProductType = "";
$selectedBrand = "";
$selectedStoreType = "";
$selectedStore = "";
$selectedRegionOffice = "";

$filtersArray = [];

if(isset($_POST)){
    if(isset($_REQUEST['product_category']) && $_REQUEST['product_category']!="") {
        $selectedCat = $_REQUEST['product_category'];
        $filtersArray[] = "product_category='".$selectedCat."'";
    }

    if(isset($_REQUEST['product_type']) && $_REQUEST['product_type']!="") {
        $selectedProductType = $_REQUEST['product_type'];
        $filtersArray[] = "product_type='".$selectedProductType."'";
    }

    if(isset($_REQUEST['product_brand']) && $_REQUEST['product_brand']!="") {
        $selectedBrand = $_REQUEST['product_brand'];
        $filtersArray[] = "product_brand='".$selectedBrand."'";
    }

    if(isset($_REQUEST['store_type']) && $_REQUEST['store_type']!="") {
        $selectedStoreType = $_REQUEST[ 'store_type'];
        $filtersArray[] = "store_type='".$selectedStoreType."'";
    }

    if(isset($_REQUEST['store_name']) && $_REQUEST['store_name']!="") {
        $selectedStore = $_REQUEST[ 'store_name'];
        $filtersArray[] = "store_name='".$selectedStore."'";
    }

    if(isset($_REQUEST['region_office_name']) && $_REQUEST['region_office_name']!="") {
        $selectedRegionOffice = $_REQUEST[ 'region_office_name'];
        $filtersArray[] = "region_office_name='".$selectedRegionOffice."'";
    }

}
$transDate = isset($_REQUEST['transaction_date'])?$_REQUEST['transaction_date']:$transDate;
$filtersArray[] = "transaction_date='".$transDate."'";

/**************************************populate old data**************************************/
/**************************************      start      **************************************/
function createEsalesInvItem($esalesconn, $obj, $ToDate){
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
    $data = array($obj['store_id'], 1, $obj['product_id'], '', '',
                  $obj['m_unit'], $obj['qty'], $time, $ToDate, '',
                  $obj['store_type'],$obj['store_name'],$obj['region_office_id'],$obj['region_office_name'],$obj['product_name'],$obj['product_type'],
                  $obj['product_brand'],$obj['product_category']);
    if (!sqlsrv_query($esalesconn, $tsql, $data)) {
    } else {
    }
}

if( strtotime($transDate) < strtotime(date('Y-m-d')) ) {
    $queryHasDataInDate = "select * from [current_cwh_inventories] where transaction_date='".$transDate."'";
    $checkExistRetQue = sqlsrv_query($esalesconn, $queryHasDataInDate);
    if ($checkExistRetQue) {
        if (!sqlsrv_has_rows($checkExistRetQue)) {

            $lastTransDate = date('Y-m-d', strtotime($transDate .' -1 day'));
            $lastStockDateQueryStr = "SELECT MAX(transaction_date) as last_transaction_date  FROM [current_cwh_inventories] cwhinv where transaction_date<'".$transDate."'";
            $lastStockDateQuery = sqlsrv_query($esalesconn, $lastStockDateQueryStr);
            if($lastStockDateQuery) {

                if (sqlsrv_has_rows($lastStockDateQuery)) {

                    while ($obj = sqlsrv_fetch_object($lastStockDateQuery)) {
                        $lastTransDateObj = $obj->last_transaction_date;
                        $lastTransDate = date('Y-m-d',$lastTransDateObj->getTimestamp());
                        break;
                    }
                }
            }

            $esalesStock = [];
            $esalesStockQuery = "SELECT * FROM [current_cwh_inventories] cwhinv where transaction_date='".$lastTransDate."'";
            $esalesStockQueryResult = sqlsrv_query($esalesconn, $esalesStockQuery);
            while($esrow = sqlsrv_fetch_array($esalesStockQueryResult, SQLSRV_FETCH_ASSOC) ) {
                $esalesStock[] = $esrow;
            }

            for ($i=0; $i<count($esalesStock);$i++) {
                createEsalesInvItem($esalesconn, $esalesStock[$i], $transDate);
            }
        }
    }
    else {
        echo print_r(sqlsrv_errors());
    }
}
/**************************************       end       **************************************/
/**************************************populate old data**************************************/


$whereClause = implode(" and ",$filtersArray);

$data= [];
$productTypes = [];
$productCategories = [];
$productBrands = [];
$storeTypes = [];
$stores = [];
$offices = [];

$queryn = "select product_name,product_type,product_brand,product_category,
sum(CASE WHEN store_type='CWH' THEN qty END) as cwh_qty,
sum(CASE WHEN store_type='SO Store' THEN qty END) as so_qty,
sum(CASE WHEN store_type='Area Store' THEN qty END) as aso_qty,
sum(qty) as national_qty
from  current_cwh_inventories ccwh
where $whereClause
group by product_name,product_type,product_brand,product_category";

$qResult = sqlsrv_query($esalesconn, $queryn);
if($qResult){
    while ($row = sqlsrv_fetch_array($qResult, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
    }
}

$qureyProdCat = "select distinct (product_category) from current_cwh_inventories ccwh";
$qResult = sqlsrv_query($esalesconn, $qureyProdCat);
if($qResult)
{
    while ( $row = sqlsrv_fetch_array( $qResult, SQLSRV_FETCH_ASSOC ) )
    {
        $productCategories[] = $row[ 'product_category' ];
    }
}

$qureyProdType = "select distinct (product_type) from current_cwh_inventories ccwh";
$qResult = sqlsrv_query($esalesconn, $qureyProdType);
if($qResult)
{
    while ( $row = sqlsrv_fetch_array( $qResult, SQLSRV_FETCH_ASSOC ) )
    {
        $productTypes[] = $row[ 'product_type' ];
    }
}

$qureyProdBrand = "select distinct (product_brand) from current_cwh_inventories ccwh".( !empty($selectedCat) ? " where product_category='".$selectedCat."'" : "");
$qResult = sqlsrv_query($esalesconn, $qureyProdBrand);
if($qResult)
{
    while ( $row = sqlsrv_fetch_array( $qResult, SQLSRV_FETCH_ASSOC ) )
    {
        $productBrands[] = $row[ 'product_brand' ];
    }
}

$qureyStoreTypes = "select distinct (store_type) from current_cwh_inventories ccwh";
$qResult = sqlsrv_query($esalesconn, $qureyStoreTypes);
if($qResult)
{
    while ( $row = sqlsrv_fetch_array( $qResult, SQLSRV_FETCH_ASSOC ) )
    {
        $storeTypes[] = $row[ 'store_type' ];
    }
}

$qureyStores = "select distinct (store_name) from current_cwh_inventories ccwh".( !empty($selectedStoreType) ? " where store_type='".$selectedStoreType."'" : "");
$qResult = sqlsrv_query($esalesconn, $qureyStores);
if($qResult)
{
    while ( $row = sqlsrv_fetch_array( $qResult, SQLSRV_FETCH_ASSOC ) )
    {
        $stores[] = $row[ 'store_name' ];
    }
}

$qureyOffices = "select distinct (region_office_name) from current_cwh_inventories ccwh";
$qResult = sqlsrv_query($esalesconn, $qureyOffices);
if($qResult)
{
    while ( $row = sqlsrv_fetch_array( $qResult, SQLSRV_FETCH_ASSOC ) )
    {
        $offices[] = $row[ 'region_office_name' ];
    }
}

sqlsrv_close($esalesconn);
?>
<html>
<head>
    <title>
        SMC Report
    </title>
    <!------ Include the above in your HEAD tag ---------->
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" id="bootstrap-dt-css">
    <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" id="bootstrap-datetime-css">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
    <script src="js/moment.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <!--script src="js/bootstrap-datetimepicker.min.js"></script-->
</head>

<body>
<div class="container">
    <h2>National Stock </h2>
    <h3>Updated At: <?php echo $queryDateStr; ?></h3>

    <div class="row">
        <form method="post">
            <div class="col-xs-12">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="transaction_date">Date</label>
                        <input type="text" class="form-control" id="transaction_date" name="transaction_date" value="<?php echo $transDate; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="product_category">Product Category</label>
                        <select id="product_category" name="product_category" class="form-control">
                            <option value="">All</option>
                            <?php
                                foreach($productCategories as $pc) {
                            ?>
                                    <option value="<?php echo $pc ?>" <?php echo ($pc==$selectedCat)?"selected":'' ?> ><?php echo $pc ?></option>
                            <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="product_brand">Product Brand</label>
                        <select id="product_brand" name="product_brand" class="form-control">
                            <option value="">All</option>
                            <?php
                                foreach($productBrands as $pb) {
                            ?>
                                    <option value="<?php echo $pb?>" <?php echo $pb==$selectedBrand?"selected":''?>><?php echo $pb?></option>
                            <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="product_type">Product Type</label>
                        <select id="product_type" name="product_type" class="form-control">
                            <option value="">All</option>
                            <?php
                                foreach( $productTypes as $pt) {
                            ?>
                                    <option value="<?php echo $pt?>" <?php echo $pt==$selectedProductType?"selected":''?>><?php echo $pt?></option>
                            <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="store_type">Store Type</label>
                        <select id="store_type" name="store_type" class="form-control">
                            <option value="">All</option>
                            <?php
                            foreach( $storeTypes as $stt) {
                                ?>
                                <option value="<?php echo $stt?>" <?php echo $stt==$selectedStoreType?"selected":''?>><?php echo $stt?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="store_name">Store</label>
                        <select id="store_name" name="store_name" class="form-control">
                            <option value="">All</option>
                            <?php
                            foreach( $stores as $st) {
                                ?>
                                <option value="<?php echo $st?>" <?php echo $st==$selectedStore?"selected":''?>><?php echo $st?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="region_office_name">Region Office</label>
                        <select id="region_office_name" name="region_office_name" class="form-control">
                            <option value="">All</option>
                            <?php
                            foreach( $offices as $office) {
                                ?>
                                <option value="<?php echo $office?>" <?php echo $office==$selectedRegionOffice?"selected":''?>><?php echo $office?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <div class="form-group col-md-3">
                    <button class="btn btn-primary" type="submit">Filter</button>
                </div>
            </div>

        </form>
    </div>
<table id="cwhinv" class="table table-striped table-bordered" style="width:100%">
<thead>
<tr>
    <th>Product</th>
    <th>Product Type</th>
    <th>Product Category</th>
    <th>Product Brand</th>
    <th>CWH</th>
    <th>ASO</th>
    <th>SO</th>
    <th>National</th>
</tr>
</thead>
<tbody>
<?php for ($i=0; $i<count($data);$i++) { ?>
<tr>
    <td><?php echo $data[$i]['product_name']?></td>
    <td><?php echo $data[$i]['product_type']?></td>
    <td><?php echo $data[$i]['product_category']?></td>
    <td><?php echo $data[$i]['product_brand']?></td>
    <td style="text-align: right"><?php echo $data[$i]['cwh_qty']?></td>
    <td style="text-align: right"><?php echo $data[$i]['aso_qty']?></td>
    <td style="text-align: right"><?php echo $data[$i]['so_qty']?></td>
    <td style="text-align: right"><?php echo $data[$i]['national_qty']?></td>
</tr>
<?php } ?>
</tfoot>
</table>
</div> <!-- ./container -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#cwhinv').DataTable();
        $('#transaction_date').datepicker({
            dateFormat:"yy-mm-dd"
        });


        $("#product_category").change(function () {
            var optHtml = '<option value="">All</option>';
            if($("#product_category").val() != '' ){
                let query = "select distinct (product_brand) from current_cwh_inventories where product_category = '"+ $("#product_category").val() + "'";
                let col = 'product_brand';
                $.get("getdata.php?query_data="+query+"&col_ind="+col,function(data){
                    data = $.parseJSON(data);
                    if(data.length) {
                        for(var i=0;i<data.length;i++){
                            optHtml += '<option value="'+data[i]+'">'+data[i]+'</option>';
                        }
                    }
                    $("#product_brand").html(optHtml);
                });
            }
            else {
                $("#product_brand").html(optHtml);
            }
        });


        $("#store_type").change(function () {
            var optHtml = '<option value="">All</option>';
            if($("#store_type").val() != '' ){
                let query = "select distinct (store_name) from current_cwh_inventories where store_type = '"+ $("#store_type").val() + "'";
                let col = 'store_name';
                $.get("getdata.php?query_data="+query+"&col_ind="+col,function(data){
                    data = $.parseJSON(data);
                    if(data.length) {
                        for(var i=0;i<data.length;i++){
                            optHtml += '<option value="'+data[i]+'">'+data[i]+'</option>';
                        }
                    }
                    $("#store_name").html(optHtml);
                });
            }
            else {
                $("#store_name").html(optHtml);
            }
        });

    } );
</script>
</body>
</html>
