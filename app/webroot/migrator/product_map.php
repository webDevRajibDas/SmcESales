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

$dssconnectionInfo = array("UID"=>$dssUid,
                           "PWD"=>$dssPwd,
                           "Database"=>$dssDatabaseName);

$dssconn = sqlsrv_connect( $dssServerName, $dssconnectionInfo);
if( $dssconn ) {
}else{
    die( print_r( sqlsrv_errors(), true));
}


//save here

function checkExistMap($esalesconn, $id){
    $queryn = "select * from [product_map] where esales_product_id = '$id'";
    $checkExistMapQuery = sqlsrv_query($esalesconn, $queryn);
    $dss_id = -1;

    if($checkExistMapQuery) {
        if (sqlsrv_has_rows($checkExistMapQuery)) {
            while ($obj = sqlsrv_fetch_object($checkExistMapQuery)) {
                $dss_id = $obj->dss_sku_id;
                break;
            }
        }
    }

    return $dss_id;
}

function createNewMap($esalesconn,$esalesId,$dssId){
    $tsql= "INSERT INTO [product_map] (
            esales_product_id,
            dss_sku_id)
            VALUES
            (?, ?)";
    $data = array($esalesId,$dssId);
    if (!sqlsrv_query($esalesconn, $tsql, $data)){
        var_dump($data) ;
        die(print_r(sqlsrv_errors(),true));
    }
}

function updateMap($esalesconn,$esalesId,$dssId){
    $sql = "UPDATE product_map SET
			dss_sku_id = ?
			WHERE esales_product_id  = ?";
    $params = array($dssId, $esalesId);
    if (!sqlsrv_query( $esalesconn, $sql, $params)){
        die(print_r(sqlsrv_errors(),true));
    }
}

function deleteMap($esalesconn,$esalesId){
    $sql = "delete from product_map
			WHERE esales_product_id  = ?";
    $params = array($esalesId);
    if (!sqlsrv_query( $esalesconn, $sql, $params)){
        die(print_r(sqlsrv_errors(),true));
    }
}
//save here
$message = '';
if(isset($_POST['submit'])){

    $esalesProductValues = $_POST[ 'esales_product_id'];
    $dssProductValues = $_POST[ 'dss_product_id'];

    for( $i = 0; $i<count($esalesProductValues); $i++) {
        $dssMapId = checkExistMap($esalesconn, $esalesProductValues[ $i]);

        //echo $dssMapId.'      '.$esalesProductValues[$i].'   '.$dssProductValues[$i].'<br/>';
        if( $dssMapId == -1 && $dssProductValues[ $i] != "" ) {
            createNewMap($esalesconn, $esalesProductValues[ $i], $dssProductValues[ $i]);
        }
        else if( $dssMapId != -1 && $dssProductValues[ $i] == "" ){
            deleteMap($esalesconn, $esalesProductValues[ $i]);
        }
        else if( $dssMapId != -1 && $dssMapId != $dssProductValues[ $i] ){
            updateMap($esalesconn, $esalesProductValues[ $i], $dssProductValues[ $i]);
        }
    }

    $message = '<div class="alert alert-success">Saved Successfully</div>';
}


$esalesProducts = [];
$qureyProducts = "SELECT p.id,p.product_code,p.name,pm.esales_product_id,pm.dss_sku_id
FROM products p
LEFT JOIN product_map pm ON (p.id = pm.esales_product_id)
ORDER BY p.name";
$qResult = sqlsrv_query($esalesconn, $qureyProducts);
while ($row = sqlsrv_fetch_array($qResult, SQLSRV_FETCH_ASSOC)) {
    $esalesProducts[] = $row;
}

$dssProducts = [];
$qureyProductsDSS = "SELECT sku.SKUID,sku.Code,sku.Name FROM SKUs sku
where (sku.InventoryTypeID = 43 or sku.InventoryTypeID = 47)
ORDER BY sku.Name";
$qResult = sqlsrv_query($dssconn, $qureyProductsDSS);
while ($row = sqlsrv_fetch_array($qResult, SQLSRV_FETCH_ASSOC)) {
    $dssProducts[] = $row;
}

sqlsrv_close($dssconn);
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
    <h2>Product Mapper</h2>
    <div class="row">
        <div class="col-xs-12"><?=$message?></div>
        <div class="clearfix"></div>
        <div class="col-xs-12">
            <form method="post">
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Esales Product</th><th>DSS Product</th></tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach( $esalesProducts as $esalesProduct) {
                    ?>
                            <tr>
                                <td>
                                    <?= $esalesProduct[ 'product_code']." - ".$esalesProduct[ 'name']?>
                                    <input type="hidden" name="esales_product_id[]" value="<?= $esalesProduct[ 'id']?>"/>
                                </td>
                                <td>
                                    <select name="dss_product_id[]" class="form-control">
                                        <option value="">Select</option>
                                        <?php
                                        foreach( $dssProducts as $dssProduct)
                                        {
                                            ?>
                                            <option value="<?= $dssProduct[ 'SKUID']?>" <?=( $esalesProduct[ 'dss_sku_id']== $dssProduct[ 'SKUID']?"selected":"")?>><?= $dssProduct[ 'Name']?></option>
                                            <?php
                                        }
                                        ?>

                                    </select>
                                </td>
                            </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                </table>
                <button class="btn btn-primary" name="submit" type="submit">Save</button>
            </form>
        </div>
    </div>
</body>
</html>
