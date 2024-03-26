<?php
require 'db_connection.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '0');
require('php-excel-reader/excel_reader2.php');
require('SpreadsheetReader.php');

$Reader = new SpreadsheetReader('upload/db/dist_xl_preparation.xlsx');

$i = 0;
sqlsrv_begin_transaction($conn);

$full_sql = "";
try {
	foreach ($Reader as $Row) {
		$i++;
		if ($i == 1)
			continue;
		$product_name = "";
		$batch_no = "";
		$expire_date = "";
		$qty = "";
		$office_name = "";
		$so_name = "";
		$inner_sql = "";


		$office_name = $Row[0];
		$db_name = $Row[1];
		$product_name = $Row[2];

		$s_qty = $Row[3];
		$b_qty = $Row[4];

		$product_info = array();
		$office_info = array();
		$db_info = array();

		$product_info = get_product_info($conn, $product_name);
		if (!empty($product_info)) {
			$product_id = $product_info['product_id'];
			$product_base_qty = $product_info['product_base_qty'];
			$maintain_batch = $product_info['maintain_batch'];
			$is_maintain_expire_date = $product_info['is_maintain_expire_date'];
		} else {
			$inner_sql = "";
			$full_sql = "";
			throw new Exception();
		}

		$office_info = get_office_info($conn, $office_name);
		if (!empty($office_info)) {
			$office_id = $office_info['office_id'];
		} else {
			$inner_sql = "";
			$full_sql = "";
			throw new Exception();
		}

		$db_info = get_db_info($conn, $db_name, $office_id);


		if (!empty($db_info)) {
			$db_id = $db_info['db_id'];
			$store_id = $db_info['store_id'];
		} else {
			$inner_sql = "";
			$full_sql = "";
			throw new Exception();
			break;
		}
		$inner_sql = "INSERT INTO db_stock_in(office_id,db_id,store_id,product_id,sound_qty,bonus_qty)VALUES($office_id,$db_id,$store_id,$product_id,$s_qty,$b_qty)";
		$full_sql .= $inner_sql;
		if ($i % 100 == 0) {

			try {
				sqlsrv_query($conn, $full_sql);
			} catch (Exception $e) {
				throw new Excepton();
			}
			$inner_sql = "";
			$full_sql = "";
		}
	}
	try {
		sqlsrv_query($conn, $full_sql);
	} catch (Exception $e) {
		throw new Excepton();
	}
	sqlsrv_commit($conn);
} catch (Exception  $e) {
	echo $i;
	echo '<pre>';
	print_r($e);
	sqlsrv_rollback($conn);
	echo 'failed';
	exit;
}
echo 'Success';
exit;




function get_product_info($conn, $product_name)
{

	$sql = "select p.id as product_id,p.maintain_batch as maintain_batch,p.is_maintain_expire_date as is_maintain_expire_date, pm.qty_in_base as product_base_qty from products p left join product_measurements pm on p.id=pm.product_id and p.sales_measurement_unit_id=pm.measurement_unit_id where p.name=?";
	/* echo $sql . '<br>'; */
	$result_data = sqlsrv_query($conn, $sql, array($product_name));
	$row_count_data = sqlsrv_has_rows($result_data);
	$data = array();
	if ($row_count_data === true) {
		$data = array();

		while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {
			$data['product_id'] = $row_data['product_id'];
			$data['product_base_qty'] = $row_data['product_base_qty'];
			$data['maintain_batch'] = $row_data['maintain_batch'];
			$data['is_maintain_expire_date'] = $row_data['is_maintain_expire_date'];
		}
	}


	return $data;
}

function get_office_info($conn, $office_name)
{
	$sql = "select id from offices where office_name='$office_name'";
	$result_data = sqlsrv_query($conn, $sql);
	$row_count_data = sqlsrv_has_rows($result_data);
	$data = array();
	if ($row_count_data === true) {
		$data = array();
		while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {
			$data['office_id'] = $row_data['id'];
		}
	}


	return $data;
}


function get_db_info($conn, $db_name, $office_id)
{
	$sql = "select top 1 ds.id as store_id,dd.id as db_id from dist_distributors dd 
		inner join dist_stores ds on ds.dist_distributor_id=dd.id
	where dd.name like ? and dd.office_id=?";

	$result_data = sqlsrv_query($conn, $sql, array('%' . trim($db_name) . '%', $office_id));
	// echo $db_name . '<br>';
	$row_count_data = sqlsrv_has_rows($result_data);
	$data = array();
	if ($row_count_data === true) {
		$data = array();
		while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {
			/* print_r($row_data);
			exit; */
			$data['store_id'] = $row_data['store_id'];
			$data['db_id'] = $row_data['db_id'];
		}
	}

	return $data;
}
