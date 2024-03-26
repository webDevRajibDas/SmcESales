<?php	
	require 'db_connection.php';
    ini_set('memory_limit', '-1');
	require('php-excel-reader/excel_reader2.php');
	require('SpreadsheetReader.php');
	
	$Reader = new SpreadsheetReader('aug_bogra_aso.xlsx');
	$i=0;
	$full_sql="";
	foreach ($Reader as $Row)
	{
		$i++;
		if($i==1)
			continue;
		
		
		$product_name="";
		$batch_no="";
		$expire_date="";		
		$qty="";
		$office_name="";
		$so_name="";
		$inner_sql="";
		
		
		$product_name=$Row[1];
		$batch_no=$Row[2];
		$expire_date=date('Y-m-d',strtotime($Row[3]));		
		$qty=$Row[4];
		$office_name=$Row[0];
		// $so_name=$Row[5];
		
		$product_info=array();
		$office_info=array();
		$territory_info=array();
		
		$product_info=get_product_info($conn,$product_name);
		
		
		
		if(!empty($product_info))
		{
			$product_id=$product_info['product_id'];
			$product_base_qty=$product_info['product_base_qty'];
			$maintain_batch=$product_info['maintain_batch'];
			$is_maintain_expire_date=$product_info['is_maintain_expire_date'];
		}
		else 
		{
			$inner_sql="";
			$full_sql="";
			echo "product name not found :$product_name line no: $i";
			break;
		}
								
		$office_info=get_office_info($conn,$office_name);
				
		
		if(!empty($office_info))
		{
			$office_id=$office_info['office_id'];
        	$store_id=$office_info['store_id'];		
		}
		else 
		{
			$inner_sql="";
			$full_sql="";
			echo "office name not found : $office_name";
			break;
		}
		
		
		
	   
	   $updated_at='2018-08-31 23:59:59';
	   $transaction_date='2018-08-31';
	   
	   if($maintain_batch || $is_maintain_expire_date)
	   {
		   
			  $inner_sql="insert into current_inventories (store_id,inventory_status_id,product_id,batch_number,expire_date,qty,updated_at,transaction_date,transaction_type_id) 
					 values ($store_id,1,$product_id,'$batch_no','$expire_date',$qty,'$updated_at','$transaction_date',4)";
						  
			  $full_sql=$full_sql.$inner_sql.";"; 
		   
	   }
	   else
	   {
	   		 $inner_sql="insert into current_inventories (store_id,inventory_status_id,product_id,batch_number,expire_date,qty,updated_at,transaction_date,transaction_type_id) 
					 values ($store_id,1,$product_id,'','',$qty,'$updated_at','$transaction_date',4)";
						  
			  $full_sql=$full_sql.$inner_sql.";"; 
	   }
	   
	}
	
	if($full_sql!="")
	{		
		$result = sqlsrv_query($conn, $full_sql);
        if($result)	
		{
			 echo "SO Data is uploaded successfully";
			 //echo $full_sql;
		}	
		else
		{
			die( print_r( sqlsrv_errors(), true));
		}		
	}
	else 
	{
		echo "SO Data not valid";
	}
	
	 
	
	
	function get_product_info($conn,$product_name)
	{
		
		$sql = "select p.id as product_id,p.maintain_batch as maintain_batch,p.is_maintain_expire_date as is_maintain_expire_date, pm.qty_in_base as product_base_qty from products p left join product_measurements pm on p.id=pm.product_id and p.sales_measurement_unit_id=pm.measurement_unit_id where p.name='$product_name'";
	    $result_data = sqlsrv_query($conn, $sql);		
		$row_count_data = sqlsrv_has_rows($result_data);
        $data=array();
							if($row_count_data === true)
							{	
                                $data=array();
                                							
								while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {
			                    $data['product_id'] = $row_data['product_id'];	
								$data['product_base_qty'] = $row_data['product_base_qty'];
								$data['maintain_batch'] = $row_data['maintain_batch'];	
								$data['is_maintain_expire_date'] = $row_data['is_maintain_expire_date'];
								
		                       }
							}  
							   
							
		return $data;
		
	}
	
	function get_office_info($conn,$office_name)
	{
		$sql = "select id from offices where office_name='$office_name'";		
	    $result_data = sqlsrv_query($conn, $sql);		
		$row_count_data = sqlsrv_has_rows($result_data);
        $data=array();
							if($row_count_data === true)
							{	
                                $data=array();						
								while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {
			                    $data['office_id'] = $row_data['id']; 
                                $data['store_id'] = get_store_id($conn,$data['office_id'],2); 								
		                       }
							}  
							   
							
		return $data;
	}
	
	
	function get_so_info($conn,$so_name)
	{
		$sql = "select top 1 designation_id,office_id,territory_id from sales_people where name='$so_name'";
		
	    $result_data = sqlsrv_query($conn, $sql);		
		$row_count_data = sqlsrv_has_rows($result_data);
        $data=array();
							if($row_count_data === true)
							{	
                                $data=array();						
								while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {
			                    $data['designation_id'] = $row_data['designation_id'];	
                                $data['office_id'] = $row_data['office_id'];	
                                $data['territory_id'] = $row_data['territory_id'];	
                                $data['store_id'] = get_store_id($conn,$data['territory_id'],3);								
		                       }
							}  
							   
							
		return $data;
	}	
	
	function get_store_id($conn,$territory_id,$type)
	{
		if($type==2)
		{
			$sql = "select id from stores where office_id=$territory_id and store_type_id=$type";
		}
		else 
		{
			$sql = "select id from stores where territory_id=$territory_id and store_type_id=$type";
		}
		
	    $result_data = sqlsrv_query($conn, $sql);		
		$row_count_data = sqlsrv_has_rows($result_data);
        $store_id="";	
							if($row_count_data === true)
							{	
                                $store_id="";				
								while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {
			                    $store_id = $row_data['id'];	                                						
		                       }
							}  
							   
							
		return $store_id;
	}



function get_batch_list_info($conn,$product_id,$store_id,$office_id)
	{
		
		$sql = "select batch_no,expire_date,store_id from so_batch_info_details where product_id=$product_id and office_id=$office_id";		
	    $result_data = sqlsrv_query($conn, $sql);		
		$row_count_data = sqlsrv_has_rows($result_data);
        $data=array();
		$full_data=array();	
							if($row_count_data === true)
							{	
                                $data=array();		
                                								
								while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {									
			                    $data['batch_no'] = $row_data['batch_no'];
                                $expired_date_arr=(array)$row_data['expire_date'];																                              										
                                $data['expire_date'] = date("Y-m-d",strtotime($expired_date_arr['date']));								
                                $data['store_id'] = $row_data['store_id'];
                                $full_data[]=$data;								
		                       }
							}  
							   
							
		return $full_data;
	}	
?>