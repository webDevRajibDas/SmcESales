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
    $queryn = "select * from [store_map] where esales_store_id = '$id'";
    $checkExistMapQuery = sqlsrv_query($esalesconn, $queryn);
    $dss_id = -1;

    if($checkExistMapQuery) {
        if (sqlsrv_has_rows($checkExistMapQuery)) {
            while ($obj = sqlsrv_fetch_object($checkExistMapQuery)) {
                $dss_id = $obj->dss_store_id;
                break;
            }
        }
    }

    return $dss_id;
}

function createNewMap($esalesconn,$esalesId,$dssId){
    $tsql= "INSERT INTO [store_map] (
            esales_store_id,
            dss_store_id)
            VALUES
            (?, ?)";
    $data = array($esalesId,$dssId);
    if (!sqlsrv_query($esalesconn, $tsql, $data)){
        var_dump($data) ;
        die(print_r(sqlsrv_errors(),true));
    }
}

function updateMap($esalesconn,$esalesId,$dssId){
    $sql = "UPDATE store_map SET
			dss_store_id = ?
			WHERE esales_store_id  = ?";
    $params = array($dssId, $esalesId);
    if (!sqlsrv_query( $esalesconn, $sql, $params)){
        die(print_r(sqlsrv_errors(),true));
    }
}

function deleteMap($esalesconn,$esalesId){
    $sql = "delete from store_map
			WHERE esales_store_id  = ?";
    $params = array($esalesId);
    if (!sqlsrv_query( $esalesconn, $sql, $params)){
        die(print_r(sqlsrv_errors(),true));
    }
}
//save here
$message = '';
if(isset($_POST['submit'])){

    $esalesStoreValues = $_POST[ 'esales_store_id'];
    $dssStoreValues = $_POST[ 'dss_store_id'];

    for( $i = 0; $i<count($esalesStoreValues); $i++) {
        $dssMapId = checkExistMap($esalesconn, $esalesStoreValues[ $i]);

        //echo $dssMapId.'      '.$esalesStoreValues[$i].'   '.$dssStoreValues[$i].'<br/>';
        if( $dssMapId == -1 && $dssStoreValues[ $i] != "" ) {
            createNewMap($esalesconn, $esalesStoreValues[ $i], $dssStoreValues[ $i]);
        }
        else if( $dssMapId != -1 && $dssStoreValues[ $i] == "" ){
            deleteMap($esalesconn, $esalesStoreValues[ $i]);
        }
        else if( $dssMapId != -1 && $dssMapId != $dssStoreValues[ $i] ){
            updateMap($esalesconn, $esalesStoreValues[ $i], $dssStoreValues[ $i]);
        }
    }

    $message = '<div class="alert alert-success">Saved Successfully</div>';
}


$esalesStores = [];
$qureyStores = "SELECT s.id as id,s.name as store_name,st.name as store_type,sm.esales_store_id,sm.dss_store_id
FROM stores s
INNER JOIN store_types st ON ( s.store_type_id = st.id )
LEFT JOIN store_map sm ON (s.id = sm.esales_store_id)
WHERE st.id != 3
ORDER BY st.id ASC, s.name ASC";
$qResult = sqlsrv_query($esalesconn, $qureyStores);
while ($row = sqlsrv_fetch_array($qResult, SQLSRV_FETCH_ASSOC)) {
    $esalesStores[] = $row;
}

$dssStores = [];
$qureyStoresDSS = "SELECT StoreID,Name FROM Stores ORDER BY Name ASC";
$qResult = sqlsrv_query($dssconn, $qureyStoresDSS);
while ($row = sqlsrv_fetch_array($qResult, SQLSRV_FETCH_ASSOC)) {
    $dssStores[] = $row;
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
    <h2>Store Mapper</h2>
    <div class="row">
        <div class="col-xs-12"><?=$message?></div>
        <div class="clearfix"></div>
        <div class="col-xs-12">
            <form method="post">
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Esales Store</th><th>DSS Store</th></tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach($esalesStores as $esalesStore) {
                    ?>
                            <tr>
                                <td>
                                    <?=$esalesStore['store_type'].': '.$esalesStore['store_name']?>
                                    <input type="hidden" name="esales_store_id[]" value="<?=$esalesStore['id']?>"/>
                                </td>
                                <td>
                                    <select name="dss_store_id[]" class="form-control">
                                        <option value="">Select</option>
                                        <?php
                                        foreach($dssStores as $dssStore)
                                        {
                                            ?>
                                            <option value="<?=$dssStore['StoreID']?>" <?=($esalesStore['dss_store_id']==$dssStore['StoreID']?"selected":"")?>><?=$dssStore['Name']?></option>
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
