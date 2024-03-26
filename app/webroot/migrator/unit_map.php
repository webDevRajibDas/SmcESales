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

function checkExistMap($esalesconn, $id){
    $queryn = "select * from [unit_measurement_map] where esales_measurement_unit_id = '$id'";
    $checkExistMapQuery = sqlsrv_query($esalesconn, $queryn);
    $dss_id = -1;

    if($checkExistMapQuery) {
        if (sqlsrv_has_rows($checkExistMapQuery)) {
            while ($obj = sqlsrv_fetch_object($checkExistMapQuery)) {
                $dss_id = $obj->dss_unit_id;
                break;
            }
        }
    }

    return $dss_id;
}

function createNewMap($esalesconn,$esalesId,$dssId){
    $tsql= "INSERT INTO [unit_measurement_map] (
            esales_measurement_unit_id,
            dss_unit_id)
            VALUES
            (?, ?)";
    $data = array($esalesId,$dssId);
    if (!sqlsrv_query($esalesconn, $tsql, $data)){
        var_dump($data) ;
        die(print_r(sqlsrv_errors(),true));
    }
}

function updateMap($esalesconn,$esalesId,$dssId){
    $sql = "UPDATE unit_measurement_map SET
			dss_unit_id = ?
			WHERE esales_measurement_unit_id  = ?";
    $params = array($dssId, $esalesId);
    if (!sqlsrv_query( $esalesconn, $sql, $params)){
        die(print_r(sqlsrv_errors(),true));
    }
}

function deleteMap($esalesconn,$esalesId){
    $sql = "delete from unit_measurement_map
			WHERE esales_measurement_unit_id  = ?";
    $params = array($esalesId);
    if (!sqlsrv_query( $esalesconn, $sql, $params)){
        die(print_r(sqlsrv_errors(),true));
    }
}
//save here
$message = '';
if(isset($_POST['submit'])){

    $esalesUnitValues = $_POST['esales_unit_id'];
    $dssUnitValues = $_POST['dss_unit_id'];

    for( $i = 0; $i<count($esalesUnitValues);$i++) {
        $dssMapId = checkExistMap($esalesconn,$esalesUnitValues[$i]);

        //echo $dssMapId.'      '.$esalesUnitValues[$i].'   '.$dssUnitValues[$i].'<br/>';
        if( $dssMapId == -1 && $dssUnitValues[$i] != "" ) {
            createNewMap($esalesconn,$esalesUnitValues[$i],$dssUnitValues[$i]);
        }
        else if( $dssMapId != -1 && $dssUnitValues[$i] == "" ){
            deleteMap($esalesconn,$esalesUnitValues[$i]);
        }
        else if( $dssMapId != -1 && $dssMapId != $dssUnitValues[$i] ){
            updateMap($esalesconn,$esalesUnitValues[$i],$dssUnitValues[$i]);
        }
    }

    $message = '<div class="alert alert-success">Saved Successfully</div>';
}

$esalesUnits = [];
$qureyUnits = "SELECT mu.id,mu.name,umm.esales_measurement_unit_id,umm.dss_unit_id
FROM measurement_units mu
LEFT JOIN unit_measurement_map umm ON (mu.id = umm.esales_measurement_unit_id)
ORDER BY mu.name";
$qResult = sqlsrv_query($esalesconn, $qureyUnits);
while ($row = sqlsrv_fetch_array($qResult, SQLSRV_FETCH_ASSOC)) {
    $esalesUnits[] = $row;
}

$dssUnits = [];
$qureyUnitsDSS = "SELECT UnitID,Name FROM Unit ORDER BY Name ASC";
$qResult = sqlsrv_query($dssconn, $qureyUnitsDSS);
while ($row = sqlsrv_fetch_array($qResult, SQLSRV_FETCH_ASSOC)) {
    $dssUnits[] = $row;
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
    <h2>Unit Mapper</h2>
    <div class="row">
        <div class="col-xs-12"><?=$message?></div>
        <div class="clearfix"></div>
        <div class="col-xs-12">
            <form method="post">
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Esales Unit</th><th>DSS Unit</th></tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach($esalesUnits as $esalesUnit) {
                    ?>
                            <tr>
                                <td>
                                    <?=$esalesUnit['name']?>
                                    <input type="hidden" name="esales_unit_id[]" value="<?=$esalesUnit['id']?>"/>
                                </td>
                                <td>
                                    <select name="dss_unit_id[]" class="form-control">
                                        <option value="">Select</option>
                                        <?php
                                        foreach($dssUnits as $dssUnit)
                                        {
                                            ?>
                                            <option value="<?=$dssUnit['UnitID']?>" <?=($esalesUnit['dss_unit_id']==$dssUnit['UnitID']?"selected":"")?>><?=$dssUnit['Name']?></option>
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
