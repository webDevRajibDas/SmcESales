<?php
App::uses('AppController', 'Controller');
App::uses('CurlUtility', 'Lib');
//App::import('Vendor', 'dompdf', array('file' => 'dompdf/dompdf_config.inc.php'));
//App::import('Vendor', 'dompdf/dompdf_config.inc.php');

/**
* Controller
*
* @property ApiDataRetrives $ApiDataRetrives
* @property PaginatorComponent $Paginator
*/
class ApiDataSapRetrivesController extends AppController {

	private $token = array(
		'so_challan'	=> "4d1b596d8f39d81aa6095b14c6fbe0a7",
		'common_token'		=> "4d1b596d8f39d81aa6095b14c6fbe0a7",
	);
	private $sap_user_id = 2;
	private $store_by_plant = array();
	
	public function CheckApiAuth($token,$api){
		if($this->token[$api] === $token){
			return true;
		}else{
			return false;
		}
	}
	private function get_territories_by_sap_code($sap_territory_code){
		$this->loadModel('Territory');
		$territories = $this->Territory->find('all', array(
			'fields' => array(
				'Territory.id',
				'Territory.name'
			),
			'conditions' => array('Territory.sap_territory_code' => $sap_territory_code),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => -1
		));
		return $territories[0]['Territory'];
	}
	private function get_so_id_by_sap_code($so_id){
		$this->loadModel('SalesPerson');

		$SalesOfficer = $this->SalesPerson->find('all', array(
			'fields' => array('SalesPerson.id', 'SalesPerson.territory_id'),
			'conditions' => array(
				'SalesPerson.sap_customer_code' => $so_id
			),
			'recursive' => -1
		));
		return $SalesOfficer[0]['SalesPerson'];
	}
	private function get_store_by_plant($plant){
		$this->loadModel('Office');
		$this->loadModel('Store');

		if(count($this->store_by_plant)>0){
			return $this->store_by_plant[$plant];
		}else{
			$stores = $this->Office->find('all', array(
				'fields' => array('Store.id', 'Office.sap_office_code', 'Store.name'),
				'conditions' => array(
					'Office.sap_office_code NOT' => null,
					'Store.store_type_id' => 2,
				),
				'joins' => array(
					array(
						'table' => 'stores',
						'alias' => 'Store',
						'type' => 'LEFT',
						'conditions' => array(
							'Office.id = Store.office_id'
						)
					)
				),
				'recursive'	=> -1
			));
			foreach($stores as $row){
				$this->store_by_plant[$row['Office']['sap_office_code']]=$row['Store']['id'];
			}
			return $this->store_by_plant[$plant];
		}
	}
	public function so_daily_sales($date = NULL, $status=NULL){

		$this->loadModel('SoDailySale');
		if(!isset($status)){
			$status = 1;
		}
		if(!isset($date)){
			$date = '2023-06-06';
		}
		$list = $this->SoDailySale->find('all', array(
			'conditions' => array(
				'SoDailySale.sales_date' => $date,
				'SoDailySale.status' => $status
			),
			'recursive'	=> 1
		));

		//echo '<pre>';print_r($list);exit;

		foreach( $list as $val){
			
			$summary=array();

			foreach($val['SoDailySaleSumary'] as $samari){

				$summary[] = array(
					'ProductCode'=>$samari['product_code'],
					'Quantity'=>$samari['qty'],
					'UoM'=>$samari['measurement_unit'],
					'UnitPrice'=>$samari['price'],
					'VAT'=>$samari['vat'],
					'DiscountAmount'=>$samari['discount_ammount'],
					'FOCFlag'=>' ',
				);

			}
			
			if(empty($summary)){
				continue;
			}

			$array1 = array(
				'CustomerCode'=>$val['SoDailySale']['so_sap_code'],
				'CustomerPONo'=>($status==2?'ORDCHG':''),
				'Territory'=>$val['SoDailySale']['sap_territory_code'],
				'Plant'=>$val['SoDailySale']['plant'],
				'StorageLoc'=>$val['SoDailySale']['storageloc'],
				'SalesDocDate'=> date('Y-m-d', strtotime($val['SoDailySale']['sales_date'])),
				'OrderType'=>'ZSEL',
				'item'=>$summary
			);

			$array2['order'][] = $array1;


		}

		$array3 = array(
			'Orders'=>$array2
		);
		//$this->dd($array3);

		$post_val = json_encode($array3);
		$this->dd($post_val );
		//echo $post_val;exit;

		//echo '<pre>';print_r($array3);exit;

		
		$header = array(
			'Content-Type:application/json',
			//'Username: smc_esales',
			//'Password: ES@les#2023'
		);

		$username = 'smc_esales';
		$password = 'ES@les#2023';
		$order_create_url = 'https://smcsap.smc-bd.org:42220/RESTAdapter/essocreat21';
		
		$response = CurlUtility::post($order_create_url, $post_val, $username, $password);

		//$url = curl_init($order_create_url);
		//
		//curl_setopt($url,CURLOPT_HTTPHEADER, $header);
		//curl_setopt($url, CURLOPT_USERPWD, $username . ":" . $password);
		//curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
		//curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($url,CURLOPT_POSTFIELDS, $post_val);
		//curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
		//
		//$resultdata = curl_exec($url);
		//
		//curl_close($url);
		//$response =  json_decode(json_encode(simplexml_load_string($response)));
		$this->dd($response);
		//echo '<pre>';print_r($response);
		//exit;
	}
	public function so_daily_sales_modify(){
		$json_data = $this->request->input('json_decode', true);
		//$this->dd($json_data);
		if(isset($json_data['date'])){
			$date = $json_data['date'];
		}else{
			$date = '2023-06-05';
		}
		$this->so_daily_sales($date, 2);
	}
	public function get_so_challan(){
		$this->loadModel('Challan');
		$this->loadModel('ChallanDetail');
		$this->loadModel('Store');
		$this->loadModel('Product');
		$this->loadModel('MeasurementUnit');
		
		$json_data = $this->request->input('json_decode', true);

		if ($this->request->is('post')) {
			if($this->CheckApiAuth($json_data['_token'],'so_challan')){
				$challans = $json_data['Challan_Info'];
				foreach($challans as $key => $challan){
					$sender_store_id = $this->get_store_by_plant($challan['plant']);
					//Get Receiver Store Code//
					$stores = $this->Store->find('first', array(
						'conditions' => array('Store.sap_store_code' => $challan['receiver_store_code']),
						'fields' => array('Store.id'),
						'recursive' => -1
					));
					
					$receiver_store_id = $stores['Store']['id'];
					//echo $sender_store_id."<br/>".$receiver_store_id;exit;
					
					$challan_ins_data['Challan']	= array(
						'challan_date'				=> date('Y-m-d', strtotime($challan['challan_date'])),
						'transaction_type_id'		=> 1,
						'inventory_status_id'		=> 1,
						'sender_store_id'			=> $sender_store_id,
						'receiver_store_id'			=> $receiver_store_id,
						'transaction_type_id'		=> 2, // ASO to SO (Product Issue) //
						'challan_referance_no'		=> $challan['challan_no'],
						//'driver_name'				=> $challan['driver'],
						//'carried_by'				=> $challan['carried_by'],
						//'truck_no'				=> $challan['truck_no'],
						'remarks'					=> $challan['remarks'],
						'created_by'				=> $this->sap_user_id,
						'created_at'				=> $this->current_datetime(),
						'status'					=> 1
					);
					if ($this->Challan->saveAll($challan_ins_data)) {
						$udata['id'] = $this->Challan->id;
						$udata['challan_no'] = 'AOC-' . (10000 + $this->Challan->id);
						$this->Challan->save($udata);
						$details_ins_data = array();
						foreach($challan['challan_details_info'] as $ckey=>$details_data){
							//Get product id//
							$products = $this->Product->find('first', array(
								'conditions' => array('Product.sap_product_code' => $details_data['product_code']),
								'fields' => array('Product.id','Product.is_virtual','Product.parent_id'),
								'recursive' => -1
							));
							$product_id = $products['Product']['id'];

							//Get measurement_unit_id//
							$measurement_units = $this->MeasurementUnit->find('first', array(
								'conditions' => array('MeasurementUnit.sap_measurement_unit_code' => $details_data['uom_code']),
								'fields' => array('MeasurementUnit.id'),
								'recursive' => -1
							));
							$measurement_unit_id = $measurement_units['MeasurementUnit']['id'];
							$details_ins_data[] = array(
								'ChallanDetail'	=>	array(
									'challan_id'			=> $udata['id'],
									'product_id'			=> $product_id,
									'measurement_unit_id'	=> $measurement_unit_id,
									'challan_qty'			=> $details_data['challan_qty'],
									'batch_no'				=> $details_data['batch_number'],
									'expire_date'			=> $details_data['expire_date'],
									'inventory_status_id'	=> 1,
									'source'				=> $details_data['product_source_code'],
									'remarks'				=> $details_data['item_remarks']
								)
							);
						}
						if($this->ChallanDetail->saveMany($details_ins_data, array('deep' => true))){
							$status = array(
								'code'	=> "200",
								'message'	=> "Challan saved!"
							);
						}else{
							$status = array(
								'code'	=> "500",
								'message'	=> "Products information not valid!",
							);
						}
					}else{
						$status = array(
							'code'	=> "500",
							'message'	=> "It's may duplicate request!",
						);
					}
				}
			}else{
				$status = array(
					'code'	=> "500",
					'message'	=> "Authentication failed!",
				);
			}
		}else{
			$status = array(
				'code'	=> "500",
				'message'	=> "Invalid request!",
			);
		}
		$this->set(array(
			'res' => $status,
			'_serialize' => array('res')
		));
	}
	public function get_so_challan_callback(){
		$this->loadModel('Challan');
		$data = $this->Challan->find('all', array(
			'conditions' => array(
				'Challan.challan_date <=' => '2023-08-13',
				'Challan.challan_referance_no IS NOT NULL',
				'Challan.status' => 1
			)
		));
		$this->dd($data);
		$this->autoRender=false;
	}
	public function post_so_return(){
		$this->loadModel('ReturnChallan');
		//$username = 'smc_esales';
		//$password = 'ES@les#2023';
		//$order_create_url = 'https://smcsap.smc-bd.org:42220/RESTAdapter/essocreat21';
		
		$challan_data = $this->ReturnChallan->find('all', array(
			'conditions'	=> array('ReturnChallan.challan_date' => '2023-05-01'),
			'recursive'		=> 1
		));
		$res_data = array();
		foreach($challan_data as $challan){
			//$this->dd($challan['ReturnChallan']
			//$this->dd($challan['ReturnChallanDetail']);
			$challan_details = NULL;
			foreach($challan['ReturnChallanDetail'] as $ChallanDetail){
				$challan_details[] = array(
					'product_code'			=> $ChallanDetail['product_id'],
					'uom_code'				=> $ChallanDetail['measurement_unit_id'],
					'expire_date'			=> $ChallanDetail['expire_date'],
					//'product_source_code'	=> $ChallanDetail['product_id'],
					'challan_qty'			=> $ChallanDetail['challan_qty'],
					'remarks'				=> $ChallanDetail['remarks'],
				);
			}
			$res_data[] = array(
				'challan_no'			=> $challan['ReturnChallan']['challan_no'],
				'sender_store_code'		=> $challan['ReturnChallan']['sender_store_id'],
				'receiver_store_code'	=> $challan['ReturnChallan']['receiver_store_id'],
				'challan_date'			=> $challan['ReturnChallan']['challan_date'],
				'remarks'				=> $challan['remarks'],
				'challan_details'		=> $challan_details,
				
			);
		}
		$this->dd($res_data);
		//$response = CurlUtility::post($order_create_url, $post_val, $username, $password);
	}
	public function so_bank_deposit(){
		$this->set(array(
			'res' => $status,
			'_serialize' => array('res')
		));
	}
	public function db_bank_deposit(){
		$this->set(array(
			'res' => $status,
			'_serialize' => array('res')
		));
	}
	public function customer_data(){
		$json_data = $this->request->input('json_decode', true);
		if ($this->request->is('post')) {
			if($this->CheckApiAuth($json_data['_token'],'common_token')){
				if(isset($json_data['TAG']) && count($json_data['TAG'])>0){
					foreach($json_data['TAG'] as $opp){
						$status[] = $this->{$opp}($json_data[$opp]);
					}
				}
			}
		}
		$this->set(array(
			'res' => $status,
			'_serialize' => array('res')
		));
	}
	private function Designation($data = array()){
		$status = NULL;
		$this->loadModel('Designation');
		foreach($data as $row){
			$check_exist_data = $this->Designation->find('list', array(
				'conditions'	=> array('Designation.sap_code' => $row['designation_code'])
			));
			if(count($check_exist_data)>0){
				try{
					$this->Designation->updateAll(array(
						'Designation.designation_name'	=> "'".$row['designation_name']."'",
						'Designation.updated_at' 		=> "'".$this->current_datetime()."'",
						'Designation.updated_by' 		=> "'".$this->sap_user_id."'",
					), array('Designation.sap_code' 	=> $row['designation_code']));
					
					$status['Designation']['Success'][] = $row['designation_code'];
				}
				catch (Exception $e) {
					$status['Designation']['Failed'][] = $row['designation_code'];
				}
			}else{
				try{
					$designation_ins_data['Designation']	= array(
						'designation_name'		=> $row['designation_name'],
						'sap_code'				=> $row['designation_code'],
						'created_by'			=> $this->sap_user_id,
						'created_at'			=> $this->current_datetime()
					);
					$this->Designation->saveAll($designation_ins_data);
					
					$status['Designation']['Success'][] = $row['designation_code'];
				}
				catch (Exception $e) {
					$status['Designation']['Failed'][] = $row['designation_code'];
				}
			}
		}
		return $status;
	}
	private function SO_SPO($data = array()){
		$status = NULL;
		$this->loadModel('SapSoSpo');
		foreach($data as $row){
			$check_exist_data = $this->SapSoSpo->find('list', array(
				'conditions'	=> array('SapSoSpo.so_code' => $row['so_code'])
			));
			if(count($check_exist_data)>0){
				try{
					$this->SapSoSpo->updateAll(array(
						'SapSoSpo.name'					=> "'".$row['name']."'",
						'SapSoSpo.userid_employee_no'	=> "'".$row['userid_employee_no']."'",
						'SapSoSpo.designation_code'		=> "'".$row['designation_code']."'",
						'SapSoSpo.office_code'			=> "'".$row['office_code']."'",
						'SapSoSpo.mobile_no'			=> "'".$row['mobile_no']."'",
						'SapSoSpo.so_spo'				=> "'".$row['so_spo']."'",
						'SapSoSpo.is_active'			=> "'".$row['is_active']."'",
						'SapSoSpo.updated_at'			=> "'".$this->current_datetime()."'",
						'SapSoSpo.updated_by'			=> "'".$this->sap_user_id."'",
					), array('SapSoSpo.so_code'		=> $row['so_code']));
					
					$status['SapSoSpo']['Success'][] = $row['so_code'];
				}
				catch (Exception $e) {
					$status['SO_SPO']['Failed'][] = $row['so_code'].$e;
				}
			}else{
				try{
					$SapSoSpo_ins_data['SapSoSpo']	= array(
						'so_code'				=> $row['so_code'],
						'name'					=> $row['name'],
						'userid_employee_no'	=> $row['userid_employee_no'],
						'designation_code'		=> $row['designation_code'],
						'mobile_no'				=> $row['mobile_no'],
						'office_code'			=> $row['office_code'],
						'so_spo'				=> $row['so_spo'],
						'is_active'				=> $row['is_active'],
						'created_by'			=> $this->sap_user_id,
						'created_at'			=> $this->current_datetime()
					);
					$this->SapSoSpo->saveAll($SapSoSpo_ins_data);
					
					$status['SO_SPO']['Success'][] = $row['so_code'];
				}
				catch (Exception $e) {
					$status['SO_SPO']['Failed'][] = $row['so_code'];
				}
			}
		}
		//$this->dd($status);
		return $status;
	}
	private function Distributor($data = array()){
		$status = NULL;
		$this->loadModel('SapDistributor');
		foreach($data as $row){
			$check_exist_data = $this->SapDistributor->find('list', array(
				'conditions'	=> array('SapDistributor.distributor_code' => $row['distributor_code'])
			));
			if(count($check_exist_data)>0){
				try{
					$this->SapDistributor->updateAll(array(
						'SapDistributor.distributor_name'		=> "'".$row['distributor_name']."'",
						'SapDistributor.office_code'			=> "'".$row['office_code']."'",
						'SapDistributor.db_address'				=> "'".$row['db_address']."'",
						'SapDistributor.mobile_number'			=> "'".$row['mobile_number']."'"
					), array('SapDistributor.distributor_code'			=> $row['distributor_code']));
					
					$status['Distributor']['Success'][] = $row['distributor_code'];
				}
				catch (Exception $e) {
					$status['Distributor']['Failed'][] = $row['distributor_code'];
				}
			}else{
				try{
					$SapDistributor_ins_data['SapDistributor']	= array(
						'distributor_code'		=> $row['distributor_code'],
						'distributor_name'		=> $row['distributor_name'],
						'db_address'			=> $row['db_address'],
						'mobile_number'			=> $row['mobile_number'],
						'office_code'			=> $row['office_code'],
						'created_by'			=> $this->sap_user_id,
						'created_at'			=> $this->current_datetime()
					);
					$this->SapDistributor->saveAll($SapDistributor_ins_data);
					
					$status['Distributor']['Success'][] = $row['distributor_code'];
				}
				catch (Exception $e) {
					$status['Distributor']['Failed'][] = $row['distributor_code'];
				}
			}
		}
		//$this->dd($status);
		return $status;
	}
	private function Distributor_Replace($data = array()){
		$this->loadModel('ReplaceDistributor');
		foreach($data as $row){
			$ins_data[] = array(
				'ReplaceDistributor'	=> array(
					'from_db_id'		=> $row['from_db_sap_code'],
					'to_db_id'			=> $row['to_db_sap_code'],
					'effective_date'	=> $row['effective_date'],
					'created_at'		=> date('Y-m-d H:i:s')
				)
			);
		}
		try{
			$this->ReplaceDistributor->saveAll($ins_data);
			$status['ReplaceDistributor']['Success'][] = $row['distributor_code'];
		}
		catch (Exception $e) {
			$status['ReplaceDistributor']['Failed'][] = $row['distributor_code'];
		}
		return $status;
	}
	public function SO_Territory_Assign($data = array()){
		$this->loadModel('SoAssignTerritory');
		foreach($data as $row){
			$so_id = $this->get_so_id_by_sap_code($row['so_code']);
			$from_territory_id = $this->get_territories_by_sap_code($row['from_territory_code']);
			$to_territory_id = $this->get_territories_by_sap_code($row['to_territory_code']);
			
			if($from_territory_id['id'] == $so_id['territory_id']){
				$ins_data[] = array(
					'SoAssignTerritory'	=>	array(
						'so_id'						=> $so_id['id'],
						'from_territory_id'			=> $from_territory_id['id'],
						'To_territory_id'			=> $to_territory_id['id'],
						'effective_date'			=> $row['effective_date'],
						'created_at'				=> date('Y-m-d H:i:s')
					)
				);
			}
		}
		try{
			$this->SoAssignTerritory->saveAll($ins_data);
			$status['SoAssignTerritory']['Success'][] = $row['distributor_code'];
		}
		catch (Exception $e) {
			$status['SoAssignTerritory']['Failed'][] = $row['distributor_code'];
		}
		return $status;
	}
	
	public function sales_return(){
		$data_array = '{
			  "Orders": {
				"order": [
				  {
					"CustomerCode": "EE00051967",
					"CustomerPONo": "",
					"Territory": "193",
					"Plant": "2029",
					"StorageLoc": "S193",
					"SalesDocDate": "2023-06-07",
					"OrderType": "ZEXP",
					"item": [
					  {
						"ProductCode": "140054",
						"Quantity": "32.00",
						"UoM": "DIS",
						"UnitPrice": "680.00",
						"VAT": ".00",
						"DiscountAmount": ".00",
						"FOCFlag": " "
					  },
					  {
						"ProductCode": "140054",
						"Quantity": "0.50",
						"UoM": "DIS",
						"UnitPrice": "690.00",
						"VAT": ".00",
						"DiscountAmount": ".00",
						"FOCFlag": " "
					  },
					  {
						"ProductCode": "140043",
						"Quantity": "12.00",
						"UoM": "DIS",
						"UnitPrice": "168.00",
						"VAT": ".00",
						"DiscountAmount": ".00",
						"FOCFlag": " "
					  },
					  {
						"ProductCode": "140041",
						"Quantity": "27.00",
						"UoM": "DIS",
						"UnitPrice": "204.00",
						"VAT": ".00",
						"DiscountAmount": ".00",
						"FOCFlag": " "
					  }
					]
				  },
				  {
					"CustomerCode": "EE00050248",
					"CustomerPONo": "",
					"Territory": "160",
					"Plant": "2027",
					"StorageLoc": "S160",
					"SalesDocDate": "2023-06-07",
					"OrderType": "ZEXP",
					"item": [
					  {
						"ProductCode": "140054",
						"Quantity": "1.00",
						"UoM": "DIS",
						"UnitPrice": "690.00",
						"VAT": ".00",
						"DiscountAmount": ".00",
						"FOCFlag": " "
					  },
					  {
						"ProductCode": "140044",
						"Quantity": "1.00",
						"UoM": "DIS",
						"UnitPrice": "320.00",
						"VAT": ".00",
						"DiscountAmount": ".00",
						"FOCFlag": " "
					  },
					  {
						"ProductCode": "140043",
						"Quantity": "3.00",
						"UoM": "DIS",
						"UnitPrice": "175.00",
						"VAT": ".00",
						"DiscountAmount": ".00",
						"FOCFlag": " "
					  }
					]
				  },
				  {
					"CustomerCode": "EE00051976",
					"CustomerPONo": "",
					"Territory": "161",
					"Plant": "2027",
					"StorageLoc": "S161",
					"SalesDocDate": "2023-06-07",
					"OrderType": "ZEXP",
					"item": [
					  {
						"ProductCode": "140054",
						"Quantity": "5.00",
						"UoM": "DIS",
						"UnitPrice": "690.00",
						"VAT": ".00",
						"DiscountAmount": ".00",
						"FOCFlag": " "
					  },
					  {
						"ProductCode": "140044",
						"Quantity": "1.00",
						"UoM": "DIS",
						"UnitPrice": "312.00",
						"VAT": ".00",
						"DiscountAmount": ".00",
						"FOCFlag": " "
					  },
					  {
						"ProductCode": "140044",
						"Quantity": "2.00",
						"UoM": "DIS",
						"UnitPrice": "320.00",
						"VAT": ".00",
						"DiscountAmount": ".00",
						"FOCFlag": " "
					  }
					]
				  },
				]
			  }
			}';
			$post_val = $data_array;
			$this->dd($post_val);
			
			
			$header = array(
				'Content-Type:application/json',
			);

			$username = 'smc_esales';
			$password = 'ES@les#2023';
			$order_create_url = 'https://smcsap.smc-bd.org:42220/RESTAdapter/esret21';
			
			$response = CurlUtility::post($order_create_url, $post_val, $username, $password);

			//$url = curl_init($order_create_url);
			//
			//curl_setopt($url,CURLOPT_HTTPHEADER, $header);
			//curl_setopt($url, CURLOPT_USERPWD, $username . ":" . $password);
			//curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
			//curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($url,CURLOPT_POSTFIELDS, $post_val);
			//curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
			//
			//$resultdata = curl_exec($url);
			//
			//curl_close($url);
			//$response =  json_decode(json_encode(simplexml_load_string($response)));
			$this->dd($response);
	}
}
