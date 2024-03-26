<?php
App::uses('AppController', 'Controller');

App::import('Vendor', 'php-excel-reader/excel_reader2');
ini_set('max_execution_time', 999999); //300 seconds = 5 minutes


/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
//Configure::write('debug',2);
class SapCodeUploadsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('Division', 'District', 'Brand', 'SalesPerson', 'Variant', 'ProductCategory', 'Store','Product', 'MemoDetail', 'MeasurementUnit',  'Office', 'Territory');
    
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
	 
	public function admin_so_sap_code(){
		
		ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
		
		$target_dir = WWW_ROOT.'files/masterdata/';
		
		$filelocation = $target_dir.'somaster.xls';
	
		$data_ex = new Spreadsheet_Excel_Reader($filelocation, true);

		$temp = $data_ex->dumptoarray();
		
		$update_data = array();
		
		//echo '<pre>';print_r($temp);exit;

		foreach ($temp as $key => $val) 
		{               
			if($key > 2){
			
				$id = str_replace(" ", "", $val[3]);
				$sap_code = str_replace(" ", "", $val[1]);

				if(!empty($id) AND $id > 0){
					//echo $username;exit;
					//$sql = " select * from users where username='$username' ";
					//echo $sql;exit;
					$user_info = $this->SalesPerson->find('first', array(
						'conditions'=>array(
							'SalesPerson.id'=>$id
						),
						'recursive'=>-1
					));
					
					if(empty($user_info)){
						$array_1[]= $sap_code;
						continue;
					}
					
					
					$id = $user_info['SalesPerson']['id'];
					$data_u['id'] = $id;
					$data_u['sap_customer_code'] = $sap_code;
				
					$update_data[] = $data_u;

				}

			}
		}

		if ($update_data) {
			//echo '<pre>';print_r($array_1);exit;
			//echo '<pre>';print_r( $update_data );exit;
			$this->SalesPerson->saveAll($update_data);
			exit;
		}

	}

    public function admin_division(){
		
		ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
		
		$target_dir = WWW_ROOT.'files/masterdata/';
		
		$filelocation = $target_dir.'divisionew.xls';
	
		$data_ex = new Spreadsheet_Excel_Reader($filelocation, true);

		$temp = $data_ex->dumptoarray();
		
		$update_data = array();
		

		foreach ($temp as $key => $val) 
		{               
			if($key > 1){
			
				$id = str_replace(" ", "", $val[1]);
				$sap_code = str_replace(" ", "", $val[2]);

				if(!empty($id)){

					$data_u['id'] = $id;
					$data_u['sap_division_code'] = $sap_code;
				
					$update_data[] = $data_u;

				}

			}
		}

		if ($update_data) {

			$this->Division->saveAll($update_data);
			exit;
		}

	}

    public function admin_district(){
		
		ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
		
		$target_dir = WWW_ROOT.'files/masterdata/';
		
		$filelocation = $target_dir.'district.xls';
	
		$data_ex = new Spreadsheet_Excel_Reader($filelocation, true);

		$temp = $data_ex->dumptoarray();
		
		$update_data = array();
		
		

		foreach ($temp as $key => $val) 
		{               
			if($key > 1){
			
				$id = str_replace(" ", "", $val[1]);
				$sap_code = str_replace(" ", "", $val[2]);

				if(!empty($id)){

					$data_u['id'] = $id;
					$data_u['sap_district_code'] = $sap_code;
				
					$update_data[] = $data_u;

				}

			}
		}

		if ($update_data) {

			$this->District->saveAll($update_data);
			exit;
		}

	}

    public function admin_office(){
		
		ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
		
		$target_dir = WWW_ROOT.'files/masterdata/';
		
		$filelocation = $target_dir.'areasalesoffice.xls';
	
		$data_ex = new Spreadsheet_Excel_Reader($filelocation, true);

		$temp = $data_ex->dumptoarray();
		
		$update_data = array();
		

		foreach ($temp as $key => $val) 
		{               
			if($key > 1){
			
				$id = str_replace(" ", "", $val[1]);
				$sap_code = str_replace(" ", "", $val[2]);

				if(!empty($id)){

					$data_u['id'] = $id;
					$data_u['sap_office_code'] = $sap_code;
				
					$update_data[] = $data_u;

				}

			}
		}

		if ($update_data) {

			$this->Office->saveAll($update_data);
			exit;
		}

	}

    public function admin_territory(){
		
		ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
		
		$target_dir = WWW_ROOT.'files/masterdata/';
		
		$filelocation = $target_dir.'territorycodesap.xls';
	
		$data_ex = new Spreadsheet_Excel_Reader($filelocation, true);

		$temp = $data_ex->dumptoarray();
		
		$update_data = array();
		
		echo '<pre>';print_r($temp);exit;

		foreach ($temp as $key => $val) 
		{               
			if($key > 1){
			
				$id = str_replace(" ", "", $val[1]);
				$sap_code = str_replace(" ", "", $val[2]);

				if(!empty($id) and $id > 0){

					$data_u['id'] = $id;
					$data_u['sap_territory_code'] = $sap_code;
				
					$update_data[] = $data_u;

				}

			}
		}

		if ($update_data) {

			$this->Territory->saveAll($update_data);
			exit;
		}

	}


    public function admin_store(){
		
		ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
		
		$target_dir = WWW_ROOT.'files/masterdata/';
		
		$filelocation = $target_dir.'storecode.xls';
	
		$data_ex = new Spreadsheet_Excel_Reader($filelocation, true);

		$temp = $data_ex->dumptoarray();
		
		$update_data = array();
		
		//echo '<pre>';print_r($temp);exit;

		foreach ($temp as $key => $val) 
		{               
			if($key > 1){
			
				$id = str_replace(" ", "", $val[1]);
				$sap_code = str_replace(" ", "", $val[2]);

				if(!empty($id) and $id > 0){

					$data_u['id'] = $id;
					$data_u['sap_store_code'] = $sap_code;
				
					$update_data[] = $data_u;

				}

			}
		}

		if ($update_data) {

			$this->Store->saveAll($update_data);
			exit;
		}

	}

    public function admin_brand(){
		
		ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
		
		$target_dir = WWW_ROOT.'files/masterdata/';
		
		$filelocation = $target_dir.'productbrand.xls';
	
		$data_ex = new Spreadsheet_Excel_Reader($filelocation, true);

		$temp = $data_ex->dumptoarray();
		
		$update_data = array();
		//echo '<pre>';print_r($temp);exit;
		foreach ($temp as $key => $val) 
		{               
			if($key > 1){
			
				$id = str_replace(" ", "", $val[3]);
				$sap_code = str_replace(" ", "", $val[2]);

				if(!empty($id) AND $id > 0){

					$data_u['id'] = $id;
					$data_u['sap_brand_code'] = $sap_code;
				
					$update_data[] = $data_u;

				}

			}
		}

		if ($update_data) {

			$this->Brand->saveAll($update_data);
			exit;
		}

	}


    public function admin_variant(){
		
		ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
		
		$target_dir = WWW_ROOT.'files/masterdata/';
		
		$filelocation = $target_dir.'productvariant.xls';
	
		$data_ex = new Spreadsheet_Excel_Reader($filelocation, true);

		$temp = $data_ex->dumptoarray();
		
		$update_data = array();
		
		//echo '<pre>';print_r( $temp );exit;

		foreach ($temp as $key => $val) 
		{               
			if($key > 1){
			
				$id = str_replace(" ", "", $val[3]);
				$sap_code = str_replace(" ", "", $val[2]);

				if(!empty($id) AND $id > 0){

					$data_u['id'] = $id;
					$data_u['sap_variant_code'] = $sap_code;
				
					$update_data[] = $data_u;

				}

			}
		}

		if ($update_data) {

			$this->Variant->saveAll($update_data);
			exit;
		}

	}

    public function admin_product_category(){
		
		ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
		
		$target_dir = WWW_ROOT.'files/masterdata/';
		
		$filelocation = $target_dir.'pcat.xls';
	
		$data_ex = new Spreadsheet_Excel_Reader($filelocation, true);

		$temp = $data_ex->dumptoarray();
		
		$update_data = array();
		
		foreach ($temp as $key => $val) 
		{               
			if($key > 1){
			
				$id = str_replace(" ", "", $val[3]);
				$sap_code = str_replace(" ", "", $val[2]);

				if(!empty($id) AND $id > 0){

					$data_u['id'] = $id;
					$data_u['sap_product_category_code'] = $sap_code;
				
					$update_data[] = $data_u;

				}

			}
		}

		if ($update_data) {
		
			$this->ProductCategory->saveAll($update_data);
			exit;
		}

	}

    public function admin_product(){
		
		ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
		
		$target_dir = WWW_ROOT.'files/masterdata/';
		
		$filelocation = $target_dir.'product.xls';
	
		$data_ex = new Spreadsheet_Excel_Reader($filelocation, true);

		$temp = $data_ex->dumptoarray();
		
		$update_data = array();
		
		//echo '<pre>';print_r( $temp );exit;

		foreach ($temp as $key => $val) 
		{               
			if($key > 1){
			
				$sap_code = str_replace(" ", "", $val[1]);
				$pcode = str_replace(" ", "", $val[2]);

				if(!empty($pcode)){
					
					$product_info = $this->Product->find('first', array(
						'conditions'=>array(
							'Product.product_code'=>$pcode,
						),
						'recursive'=>-1
					));
					
					if(empty($product_info)){
						continue;
					}
					
					$id = $product_info['Product']['id'];

					$data_u['id'] = $id;
					$data_u['sap_product_code'] = $sap_code;
				
					$update_data[] = $data_u;

				}

			}
		}

		if ($update_data) {
			//echo '<pre>';print_r( $update_data );exit;
			$this->Product->saveAll($update_data);
			exit;
		}

	}

    public function admin_measurement_unit(){
		
		ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
		
		$target_dir = WWW_ROOT.'files/masterdata/';
		
		$filelocation = $target_dir.'productmeasurement.xls';
	
		$data_ex = new Spreadsheet_Excel_Reader($filelocation, true);

		$temp = $data_ex->dumptoarray();
		
		$update_data = array();
		
		

		foreach ($temp as $key => $val) 
		{               
			if($key > 1){
			
				///$id = str_replace(" ", "", $val[1]);
				$sap_code = str_replace(" ", "", $val[2]);
				$id = str_replace(" ", "", $val[3]);

				if(!empty($id) AND $id > 0){

					$data_u['id'] = $id;
					$data_u['sap_measurement_unit_code'] = $sap_code;
				
					$update_data[] = $data_u;

				}

			}
		}

		if ($update_data) {
			//echo '<pre>';print_r( $update_data );exit;
			$this->MeasurementUnit->saveAll($update_data);
			exit;
		}

	}
	
	public function admin_policy(){
		
		ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
		
		$target_dir = WWW_ROOT.'files/masterdata/';
		
		$filelocation = $target_dir.'policy.xls';
	
		$data_ex = new Spreadsheet_Excel_Reader($filelocation, true);

		$temp = $data_ex->dumptoarray();
		
		$update_data = array();
		
		//echo '<pre>';print_r( $temp );exit;

		foreach ($temp as $key => $val) 
		{               
			if($key > 1){
			
				$id = str_replace(" ", "", $val[1]);
				$sap_code = str_replace(" ", "", $val[2]);

				if(!empty($id)){

					$data_u['id'] = $id;
					$data_u['policy_id'] = $sap_code;
				
					$update_data[] = $data_u;

				}

			}
			
			if($key == 10){
				//break;
			}
		}

		if ($update_data) {
				//echo '<pre>';print_r( $update_data );
			$this->MemoDetail->saveAll($update_data);
			exit;
		}

	}



}
