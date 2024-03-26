<?php	
	require 'db_connection.php';
    ini_set('memory_limit', '-1');
	require('php-excel-reader/excel_reader2.php');
	require('SpreadsheetReader.php');
	
	$Reader = new SpreadsheetReader('upload/mymensingh_data.xlsx');
	$i=0;
	$full_sql="";
	$office_id="";
	$db_id="";
	$sr_ir="";
	foreach ($Reader as $Row)
	{
		$i++;
		if($i==1)
			continue;
		pr($Row);
		if($Row[0])
		{
			$office_array=get_office_info($conn,$Row[0]);
			$office_id=$office_array['office_id'];
		}
		echo $office_id;
		if($Row[2])
		{
			$db_id=get_db($conn,$Row[2],$office_id);
			if(!$db_id)
			{
				$db_name=$Row[2];
				$db_address='';
				$db_mo_no='';
				$db_id= insert_db($conn,$db_name,$db_address,$db_mo_no,$office_id);
			}
		}

		if($Row[3])
		{
			$sr_id=get_sr($conn,$Row[3],$office_id,$db_id);
			if(!$sr_id)
			{
				$sr_name=$Row[2];
				$sr_mo_no='';
				$sr_id = insert_sr($conn,$sr_name,$sr_mo_no,$office_id,$db_id);
			}
		}
		
		
	}
	
	if($full_sql!="")
	{		
        if($result)	
		{
			 echo "SO Data is uploaded successfully";
			 //echo $full_sql;
		}			
	}
	else 
	{
		echo "SO Data not valid";
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
	function get_db($conn,$db_name,$office_id)
	{
		$sql = "select id from dist_distributors where office_id=$office_id AND name='$db_name' AND is_active=1";
		// echo $sql;
	    $result_data = sqlsrv_query($conn, $sql);		
		$row_count_data = sqlsrv_has_rows($result_data);
        $data=array();
							if($row_count_data === true)
							{	
                                // $data=array();						
								while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) 
								{
			                    	$db_id = $row_data['id']; 								
		                       	}
							}  
							   
							
		return $db_id;
	}
	function insert_db($conn,$db_name,$db_address,$db_mo_no,$office_id)
	{
		$created_by=1;
		$created_at=date("Y-m-d H:i:s");
		$sql="INSERT INTO dist_distributors (name,office_id,address,mobile_number,is_active,created_at,created_by,updated_at,updated_by) VALUES ('$db_name',$office_id,'$db_address','$db_mo_no',1,'$created_at',$created_by,'$created_at',$created_by)";
		$result_data = sqlsrv_query($conn, $sql);
		if($result_data)
		{
			$db_id='';
			$sql="SELECT `id` FROM `dist_distributors` ORDER BY `id` DESC TOP 1";
			$result_data = sqlsrv_query($conn, $sql);
			while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) 
			{
				$db_id = $row_data['id']; 								
			}
			return $db_id;
		}
		else
		{
			return false;
		}
	}

	function get_sr($conn,$sr_name,$office_id,$db_id)
	{
		$sql = "select id from dist_sales_representatives where office_id=$office_id AND name='$sr_name' AND is_active=1 AND dist_distributor_id=$db_id";
		// echo $sql;
		$result_data = sqlsrv_query($conn, $sql);		
		$row_count_data = sqlsrv_has_rows($result_data);
		$data=array();
		if($row_count_data === true)
		{						
			while ($row_data = sqlsrv_fetch_array($result_data, SQLSRV_FETCH_ASSOC)) 
			{
				$db_id = $row_data['id']; 								
			}
		}  


		return $db_id;
	}
	function pr($data)
	{
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}
?>