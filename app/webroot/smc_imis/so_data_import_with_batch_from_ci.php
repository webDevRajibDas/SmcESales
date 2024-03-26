<?php	
	require 'db_connection.php';
    ini_set('memory_limit', '-1');
	require('php-excel-reader/excel_reader2.php');
	require('SpreadsheetReader.php');
	
	$Reader = new SpreadsheetReader('aug_bogra_abul.xlsx');
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
		
	
		
		$product_name=trim($Row[0]);
		$batch_no=$Row[1];
		$expire_date=date('Y-m-d',strtotime($Row[2]));		
		$qty=trim($Row[3]);
		$office_name=trim($Row[4]);
		$so_name=trim($Row[5]);
		
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
		}
		else 
		{
			$inner_sql="";
			$full_sql="";
			echo "office name not found : $office_name";
			break;
		}
		
		$so_info=get_so_info($conn,$so_name);		
		if(!empty($so_info))
				{
				$office_so_id=$so_info['office_id'];	
				$designation_id=$so_info['designation_id'];
				$territory_id=$so_info['territory_id'];
				$store_id=$so_info['store_id'];												
				}
				else 
				{
					$inner_sql="";
			        $full_sql="";
					echo "SO name not found : $so_name";
					break;
				}
		
		
	   
	   $updated_at='2018-08-31 23:59:59';
	   $transaction_date='2018-08-31';
	   $his_sql="INSERT INTO current_inventory_history (store_id,inventory_status_id,product_id,qty,updated_at,balance,inventory_entry_type,transaction_date,transaction_type_id)
	   values ($store_id,1,$product_id,0,'$updated_at',0,2,'$transaction_date',5)";
	   echo $his_sql;
	   $result=sqlsrv_query($conn, $his_sql);
	   if(!$result)
	   {
	   		echo '<pre>';
			die( print_r( sqlsrv_errors(), true));
	   }
	   // all batch 0 qty
	   // 
	   $zero_sql="ALTER TABLE current_inventories DISABLE TRIGGER current_inventory_history_data_insert_for_update;UPDATE current_inventories set qty=0 where product_id=$product_id and store_id=$store_id  and inventory_status_id=1;ALTER TABLE current_inventories ENABLE TRIGGER current_inventory_history_data_insert_for_update;";
	   echo $zero_sql;
	   $result=sqlsrv_query($conn, $zero_sql);
	   if(!$result)
	   {
	   		echo '<pre>';
			die( print_r( sqlsrv_errors(), true));
	   }
	   if($maintain_batch || $is_maintain_expire_date)
	   {
		   // distribute quantity 
		   $batch_list_info=array();
		   $batch_list_info=get_batch_list_info($conn,$product_id,$store_id,$office_id);
		   if(!empty($batch_list_info))
		   {
			    $batch_count=0;							   				
				$equal_qty=0;
				$reminder=0;
				$first_batch=0;
				
			    $batch_count=count($batch_list_info);							   				
				$equal_qty=floor($qty/$batch_count);
				$reminder=fmod($qty,$batch_count);
				$first_batch=$reminder+$equal_qty;
				$x=0;
				
				foreach($batch_list_info as $each_batch_list)
				{
					$batch_qty=0;
					$inner_sql="";
					$batch_no=$each_batch_list['batch_no'];
					$expire_date=$each_batch_list['expire_date'];
					if($x)
					{
						$batch_qty=$equal_qty;						
					}
					else 
					{
						$batch_qty=$first_batch;	
					}
					
					// $inner_sql="insert into current_inventories (store_id,inventory_status_id,product_id,batch_number,expire_date,qty,updated_at,transaction_date,transaction_type_id) 
					//  values ($store_id,1,$product_id,'$batch_no','$expire_date',$batch_qty,'$updated_at','$transaction_date',5)";

					$inner_sql=" UPDATE current_inventories set qty=$batch_qty,updated_at='$updated_at',transaction_date='$transaction_date',transaction_type_id=5 where product_id=$product_id and store_id=$store_id and batch_number='$batch_no' and expire_date='$expire_date' and inventory_status_id=1";
						  
					$full_sql=$full_sql.$inner_sql.";";
				 $x++;	
				}
		   }
		   else 
		   {
			  /*$inner_sql="insert into current_inventories (store_id,inventory_status_id,product_id,batch_number,expire_date,qty,updated_at,transaction_date,transaction_type_id) 
					 values ($store_id,1,$product_id,'$batch_no','$expire_date',$qty,'$updated_at','$transaction_date',5)";*/

					 $inner_sql=" UPDATE current_inventories set qty=$qty,updated_at='$updated_at',transaction_date='$transaction_date',transaction_type_id=5 where product_id=$product_id and store_id=$store_id  and inventory_status_id=1";
						  
			  $full_sql=$full_sql.$inner_sql.";"; 
		   }
		   
	   }
	   else
	   {
	   		$inner_sql=" UPDATE current_inventories set qty=$qty,updated_at='$updated_at',transaction_date='$transaction_date',transaction_type_id=5 where product_id=$product_id and store_id=$store_id  and inventory_status_id=1";
	   		$full_sql=$full_sql.$inner_sql.";"; 
	   }
	   
	}
	echo $full_sql;
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
			echo '<pre>';
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
		
		//$sql = "select batch_no,expire_date,store_id from so_batch_info_details where product_id=$product_id and office_id=$office_id";	
		$sql="select batch_number,expire_date from current_inventories 
					where batch_number not like '%op%' and product_id=$product_id and store_id=$store_id
					group by batch_number,expire_date,id order by id desc";
	    $result_data = sqlsrv_query($conn, $sql);		
		$row_count_data = sqlsrv_has_rows($result_data);
		if(!$row_count_data)
		{
			$sql="select batch_number,expire_date from current_inventories 
					where product_id=$product_id and store_id=$store_id
					group by batch_number,expire_date";
	    	$result_data = sqlsrv_query($conn, $sql);	
			$row_count_data = sqlsrv_has_rows($result_data);
		}
		$full_data=array();	
		if($row_count_data === true)
		{	
	        $data=array();		
	        								
			while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) {									
	        $data['batch_no'] = $row_data['batch_number'];
	        $expired_date_arr=(array)$row_data['expire_date'];																                              										
	        $data['expire_date'] = date("Y-m-d",strtotime($expired_date_arr['date']));								
	        $data['store_id'] = $store_id;
	        $full_data[]=$data;								
	       }
		}  
							   
		/*echo '<pre>';
		print_r($full_data);exit;*/					
		return $full_data;
	}	
?>