<?php
require 'db_connection.php';
date_default_timezone_set('Asia/Dhaka');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '0');
$uniq_id=uniqid();
$path = 'load_test/';
$myfile = fopen($path . "loadtest.txt", "a") or die("Unable to open file!");
$start_time = date('Y-m-d H:i:s');
echo 'here';
fwrite($myfile, "\n" . $start_time . ':' . "Request Started : " . $uniq_id);
$sql = "SELECT Sum(Round(( Round(( CASE
                             WHEN MemoDetail.price > 0 THEN MemoDetail.sales_qty
                           END ) * ( CASE
                                       WHEN ProductMeasurement.qty_in_base IS
                                            NULL THEN
                                       1
                                       ELSE ProductMeasurement.qty_in_base
                                     END ), 0) ) / ( CASE
                                                       WHEN
                            ProductMeasurementSales.qty_in_base
                            IS NULL THEN 1
                                                       ELSE
                            ProductMeasurementSales.qty_in_base
                                                     END ), 2, 1)) AS volume,
       Sum(Round(( Round(( CASE
                             WHEN MemoDetail.price = 0 THEN MemoDetail.sales_qty
                           END ) * ( CASE
                                       WHEN ProductMeasurement.qty_in_base IS
                                            NULL THEN
                                       1
                                       ELSE ProductMeasurement.qty_in_base
                                     END ), 0) ) / ( CASE
                                                       WHEN
                     ProductMeasurementSales.qty_in_base
                     IS NULL THEN 1
                                                       ELSE
                     ProductMeasurementSales.qty_in_base
                                                     END ), 2, 1)) AS bonus,
       Sum(MemoDetail.sales_qty * MemoDetail.price)                AS value,
       Count(DISTINCT Memo.memo_no)                                AS ec,
       Count(DISTINCT Memo.outlet_id)                              AS oc,
       [Memo].[office_id]                                          AS
       [Memo__office_id],
       [MemoDetail].[product_id]                                   AS
       [MemoDetail__product_id],
       Product.cyp_cal                                             AS cyp,
       Product.cyp                                                 AS cyp_v
FROM   [memos] AS [Memo]
       INNER JOIN [memo_details] AS [MemoDetail]
               ON ( [Memo].[id] = [MemoDetail].[memo_id] )
       INNER JOIN [products] AS [Product]
               ON ( [MemoDetail].[product_id] = [Product].[id] )
       LEFT JOIN [product_measurements] AS [ProductMeasurement]
              ON ( [Product].[id] = [ProductMeasurement].[product_id]
                   AND CASE
                         WHEN ( [MemoDetail].[measurement_unit_id] IS NULL
                                 OR [MemoDetail].[measurement_unit_id] = 0 )
                       THEN
                         [Product].[sales_measurement_unit_id]
                         ELSE [MemoDetail].[measurement_unit_id]
                       END = [ProductMeasurement].[measurement_unit_id] )
       LEFT JOIN [product_measurements] AS [ProductMeasurementSales]
              ON ( [Product].[id] = [ProductMeasurementSales].[product_id]
                   AND [Product].[sales_measurement_unit_id] =
                       [ProductMeasurementSales].[measurement_unit_id] )
       INNER JOIN [territories] AS [Territory]
               ON ( [Memo].[territory_id] = [Territory].[id] )
       INNER JOIN [outlets] AS [Outlet]
               ON ( [Memo].[outlet_id] = [Outlet].[id] )
       INNER JOIN [markets] AS [Market]
               ON ( [Memo].[market_id] = [Market].[id] )
       INNER JOIN [thanas] AS [Thana]
               ON ( [Memo].[thana_id] = [Thana].[id] )
       INNER JOIN [districts] AS [District]
               ON ( [Thana].[district_id] = [District].[id] )
       INNER JOIN [divisions] AS [Division]
               ON ( [District].[division_id] = [Division].[id] )
       INNER JOIN [offices] AS [Office]
               ON ( [Memo].[office_id] = [Office].[id] )
WHERE  [Memo].[memo_date] BETWEEN '2022-07-01' AND '2023-02-22'
       AND [Memo].[gross_value] >= 0
       AND [Memo].[status] != 0
       AND [Memo].[office_id] IN ( 15, 19, 26, 18,
                                   16, 25, 27, 28,
                                   29, 22, 24, 23, 44 )
       AND [MemoDetail].[product_id] IN ( 27, 28, 29, 458,
                                          23, 30, 457, 34,
                                          24, 35, 36, 74,
                                          72, 37, 20, 38,
                                          39, 40, 41, 404,
                                          405, 43, 141, 51,
                                          52, 451, 53, 450,
                                          89, 148, 149, 135,
                                          136, 137, 138, 142,
                                          143, 144, 145, 146,
                                          353, 354, 355, 356,
                                          357, 505, 506, 507,
                                          508, 509, 254, 403,
                                          65, 63, 66, 606,
                                          64, 605, 466, 564,
                                          467, 565, 67, 68,
                                          69, 70, 84, 85,
                                          86, 47, 266, 267,
                                          497, 415, 414, 256,
                                          257, 545, 546, 393,
                                          396, 392, 394, 406,
                                          395, 454, 452, 455,
                                          453, 382, 383, 384,
                                          46, 60, 62, 61,
                                          48, 473, 50, 49,
                                          339, 71, 253, 465,
                                          411, 623, 624, 628,
                                          637, 638, 639, 640,
                                          641, 642, 643, 644,
                                          647, 648, 654, 655,
                                          656, 657, 677, 678,
                                          680, 681 )
GROUP  BY [Memo].[office_id],
          [MemoDetail].[product_id],
          cyp_cal,
          cyp  ";
/* echo $sql . '<br>'; */
$result_data = sqlsrv_query($conn, $sql);
if( $result_data === false ) {
    print( print_r( sqlsrv_errors() ) );
	exit;
}
$row_count_data = sqlsrv_has_rows($result_data);
echo 'Have row : '.$row_count_data;
while( $row = sqlsrv_fetch_array( $result_data, SQLSRV_FETCH_ASSOC )) {
    //$row = sqlsrv_fetch_array( $result_data, SQLSRV_FETCH_ASSOC );
    print_r($row);
	break;
}
$end_time = date('Y-m-d H:i:s');
$crrentSysDate = new DateTime($start_time);
$userDefineDate = $crrentSysDate->format('m/d/y h:i:s a');

$start = date_create($userDefineDate);
$end = date_create(date('m/d/y h:i:s a', strtotime($end_time)));

$diff = date_diff($start, $end);
fwrite($myfile, "\n" . $end_time . ':' . "Request closed  total row:$row_count_data:  -- " . $diff->i . " Min " . $diff->s . " Sec -- "  . $uniq_id);
fclose($myfile);
exit;
