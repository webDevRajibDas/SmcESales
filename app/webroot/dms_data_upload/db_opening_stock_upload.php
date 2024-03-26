<?php
require 'db_connection.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0);
require('php-excel-reader/excel_reader2.php');
require('SpreadsheetReader.php');
$Reader = new SpreadsheetReader('upload/test.xlsx');
$i = 0;
$full_sql = "";

foreach ($Reader as $Row) {
	$i++;
	if ($i <= 3)
		continue;

	pr($Row);
	exit;
	$product_name = "";
	$product_id = "";
	$qty = "";
	$bonus_qty = "";
	$demage_qty = "";
	$office_name = "";
	$db_name = "";
	$db_id = "";
	$inner_sql = "";
	$product_name = trim($Row[4]);
	$product_id = trim($Row[3]);

	$qty = trim($Row[7] ? $Row[7] : 0);
	$bonus_qty = trim($Row[8] ? $Row[8] : 0);
	$demage_qty = trim($Row[9] ? $Row[9] : 0);
	// $office_name=trim($Row[4]);
	$db_name = trim($Row[2]);
	$db_id = trim($Row[1]);

	$product_info = array();
	$office_info = array();
	$territory_info = array();

	$product_info = get_product_info($conn, $product_id);

	if (!empty($product_info)) {
		$product_id = $product_info['product_id'];
		$product_base_qty = $product_info['product_base_qty'];
	} else {
		$inner_sql = "";
		$full_sql = "";
		echo "product name not found :$product_name line no: $i";
		break;
	}
	$office_id = 19;
	$db_info = get_db_info($conn, $db_id, $office_id);
	// pr($db_info);exit;		
	if (!empty($db_info)) {
		$db_id = $db_info['db_id'];
		$db_store_id = $db_info['db_store_id'];
	} else {
		$inner_sql = "";
		$full_sql = "";
		echo "DB name not found : $db_name line no: $i";
		break;
	}
	$qty = ROUND(($qty * $product_base_qty));
	$bonus_qty = ROUND(($bonus_qty * $product_base_qty));
	$demage_qty = ROUND(($demage_qty * $product_base_qty));
	$other_info = array(
		'demage_qty' => $demage_qty
	);
	$transaction_date = '2023-03-01';
	// echo $product_name.'---'.$qty.'--- bonus:-'.$bonus_qty.'<br>';
	/*------------- previous data delete for this product and db -------------------------*/
	$inner_sql = "DELETE FROM dist_current_inventories WHERE store_id=$db_store_id and product_id=$product_id;";
	$inner_sql .= "DELETE FROM dist_current_inventory_history WHERE store_id=$db_store_id and product_id=$product_id;";
	$inner_sql .= "DELETE FROM dist_current_inventory_balance_logs WHERE store_id=$db_store_id and product_id=$product_id and transaction_date='$transaction_date';";
	/*------------- previous data delete for this product and db -------------------------*/

	/*---------------------- insert to new current inventory ------------- */
	$inner_sql .= "INSERT INTO dist_current_inventories (store_id,inventory_status_id,product_id,qty,transaction_date,transaction_type_id,created_at,created_by,updated_at,updated_by) values ($db_store_id,1,$product_id,($qty+$bonus_qty),'$transaction_date',12,getdate(),1,getdate(),1);";
	$inner_sql .= "INSERT INTO dist_current_inventory_balance_logs (store_id,inventory_status_id,product_id,qty,transaction_date,transaction_type_id,bonus_qty,created_at,created_by,updated_at,updated_by,other_column) values ($db_store_id,1,$product_id,$qty,'$transaction_date',12,$bonus_qty,getdate(),1,getdate(),1,'" . json_encode($other_info) . "');";
	/*---------------------- insert to new current inventory ------------- */

	$result_data = sqlsrv_query($conn, $inner_sql);
	if ($result_data) {
	} else {
		echo $inner_sql;
		echo 'Not uploaded . problem in Line :' . $i . '<br>';
		pr(sqlsrv_errors());
	}
	$inner_sql = '';
}
/* if ($full_sql) {
	$result_data = sqlsrv_query($conn, $full_sql);
	if ($result_data) {
		echo "successfully uploaded";
	} else {
		pr(sqlsrv_errors());
	}
} else {
	echo 'Problem found';
} */


echo 'Success fully uploded';
function get_product_info($conn, $product_id)
{
	$sql = "select 
		p.id as product_id,
		p.name as product_name,
		p.maintain_batch as maintain_batch,
		p.is_maintain_expire_date as is_maintain_expire_date, 
		pm.qty_in_base as product_base_qty 
	from products p 
	left join product_measurements pm on p.id=pm.product_id and p.sales_measurement_unit_id=pm.measurement_unit_id where p.id = $product_id";
	$result_data = sqlsrv_query($conn, $sql);
	$row_count_data = sqlsrv_has_rows($result_data);
	$data = array();
	if ($row_count_data === true) {
		$data = array();

		while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {
			$data['product_id'] = $row_data['product_id'];
			$data['product_name'] = $row_data['product_name'];
			$data['product_base_qty'] = $row_data['product_base_qty'] ? $row_data['product_base_qty'] : 1;
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


function get_db_info($conn, $db_id, $office_id = 0)
{
	$sql = "select top 1 * from dist_distributors where id = $db_id AND is_active=1";
	/* echo $sql;
	exit; */
	$result_data = sqlsrv_query($conn, $sql);
	$row_count_data = sqlsrv_has_rows($result_data);
	$data = array();
	if ($row_count_data === true) {
		$data = array();
		while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {
			$data['db_id'] = $row_data['id'];
			$data['db_store_id'] = get_store_id($conn, $data['db_id']);
		}
	}
	return $data;
}

function get_store_id($conn, $db_id)
{
	$sql = "select id from dist_stores where dist_distributor_id=$db_id ";


	$result_data = sqlsrv_query($conn, $sql);
	$row_count_data = sqlsrv_has_rows($result_data);
	$store_id = "";
	if ($row_count_data === true) {
		$store_id = "";
		while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {
			$store_id = $row_data['id'];
		}
	}


	return $store_id;
}



function get_batch_list_info($conn, $product_id, $store_id, $office_id)
{

	//$sql = "select batch_no,expire_date,store_id from so_batch_info_details where product_id=$product_id and office_id=$office_id";	
	$sql = "select batch_number,expire_date from current_inventories 
					where batch_number not like '%op%' and product_id=$product_id and store_id=$store_id and inventory_status_id=1
					group by batch_number,expire_date,id order by id desc";
	$result_data = sqlsrv_query($conn, $sql);
	$row_count_data = sqlsrv_has_rows($result_data);
	if (!$row_count_data) {
		$sql = "select batch_number,expire_date from current_inventories 
					where product_id=$product_id and store_id=$store_id and inventory_status_id=1";
		$result_data = sqlsrv_query($conn, $sql);
		$row_count_data = sqlsrv_has_rows($result_data);
	}
	$full_data = array();
	if ($row_count_data === true) {
		$data = array();

		while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {
			$data['batch_no'] = $row_data['batch_number'];
			$expired_date_arr = (array)$row_data['expire_date'];
			$data['expire_date'] = date("Y-m-d", strtotime($expired_date_arr['date']));
			$data['store_id'] = $store_id;
			$full_data[] = $data;
		}
	}

	/*echo '<pre>';
		print_r($full_data);exit;*/
	return $full_data;
}
function check_previous_stock($conn, $product_id, $store_id, $office_id)
{
	$sql = "select batch_number,expire_date from current_inventories 
					where product_id=$product_id and store_id=$store_id
					group by batch_number,expire_date";
	$result_data = sqlsrv_query($conn, $sql);
	$row_count_data = sqlsrv_has_rows($result_data);
	if ($row_count_data === true) {
		return true;
	} else {
		return false;
	}
}
function pr($data)
{
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}
