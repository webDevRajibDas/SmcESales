<?php
App::uses('AppController', 'Controller');
/**
 * Challans Controller
 *
 * @property Challan $Challan
 * @property PaginatorComponent $Paginator
 */
class NcpChallansController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Filter.Filter');
	public $uses = array('Challan','ChallanDetail','Store','Product','CurrentInventory');
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		
		$this->set('page_title','NCP Challan List');
		
		$this->Challan->recursive = 0;	
		$this->paginate = array(
			'conditions' => array(
				'Challan.inventory_status_id' => 1,
				'AND'=>array(
						array(
							'OR' => array(
								array('Challan.transaction_type_id' => 6), // 6= CWH TO ASO (NCP Return)	
								array('Challan.transaction_type_id' => 16), //16= CWH to ASO (Receive NCP Return)
							)
						),
					array(
						'OR'=>array(
							array('Challan.sender_store_id' => $this->UserAuth->getStoreId()),
							array('Challan.receiver_store_id' => $this->UserAuth->getStoreId())
							)
						),
						$this->UserAuth->getOfficeParentId() != 0? array(
							array('Challan.status !='=>0)
						):''
					)
			),
			'recursive' => 0,
			'order' => array('Challan.id' => 'desc')						
		);		
		$this->set('challans',$this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {		
		
		$this->set('page_title','NCP Challan Details');
		if (!$this->Challan->exists($id)) {
			throw new NotFoundException(__('Invalid challan'));
		}
		
		if ($this->request->is('post')) {
			$data_array = array();
			$insert_data_array = array();
			$update_data_array = array();
			foreach($this->request->data['product_id'] as $key=>$val)
			{
				if($this->request->data['receive_quantity'][$key] != '' AND $this->request->data['receive_quantity'][$key] <= $this->request->data['quantity'][$key])
				{
					$receive_quantity = $this->request->data['receive_quantity'][$key];
				}else{
					$receive_quantity = $this->request->data['quantity'][$key];
				}
				
				// ------------ stock update --------------------			
				$inventory_info = $this->CurrentInventory->find('first',array(
					'conditions' => array(
										'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
										'CurrentInventory.inventory_status_id' => 1,
										//'CurrentInventory.transaction_type_id' => 1,
										'CurrentInventory.product_id' => $val,
										'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
										'CurrentInventory.expire_date' => $this->request->data['expire_date'][$key]
									),
					'recursive' => -1				
								));
					
				if(!empty($inventory_info))
				{
					$update_data['id'] = $inventory_info['CurrentInventory']['id'];
					$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] + $receive_quantity;
					$update_data['transaction_type_id'] = 16; // CWH to ASO (NCP)
					$update_data['updated_at']=$this->current_datetime();
					$update_data['transaction_date'] = date('Y-m-d',strtotime($this->request->data['Challan']['received_date']));	
					$update_data_array[] = $update_data;
				}else{
					$insert_data['store_id'] = $this->UserAuth->getStoreId();
					$insert_data['inventory_status_id'] = 1;
					$insert_data['product_id'] = $val;
					$insert_data['batch_number'] = $this->request->data['batch_no'][$key];
					$insert_data['expire_date'] = $this->request->data['expire_date'][$key];
					$insert_data['qty'] = $receive_quantity;
					$insert_data['updated_at'] = $this->current_datetime();
					$insert_data['transaction_type_id'] = 16; // 16=CWH to ASO (Receive NCP Return)	
					$update_data['transaction_date'] = date('Y-m-d',strtotime($this->request->data['Challan']['received_date']));	
					$insert_data_array[] = $insert_data;
				}					
				//----------------- End Stock update ---------------------
					
				
				$data['ChallanDetail']['id'] = $this->request->data['id'][$key];
				$data['ChallanDetail']['received_qty'] = $receive_quantity;
				$data_array[] = $data;
			}
			// insert inventory data
			$this->CurrentInventory->saveAll($insert_data_array);
			
			// Update inventory data
			$this->CurrentInventory->saveAll($update_data_array);
			
			// update received quantity
			$this->ChallanDetail->saveAll($data_array);
			
			// update challan status 
			$chalan['id'] = $id;
			$chalan['status'] = 2;
			$chalan['transaction_type_id'] = 16; //16=CWH to ASO (Receive NCP Return)	
			$chalan['received_date'] = date('Y-m-d',strtotime($this->request->data['Challan']['received_date']));
			$chalan['updated_by'] = $this->UserAuth->getUserId();
			$this->Challan->save($chalan);
			
			$this->Session->setFlash(__('Challan has been received.'), 'flash/success');
			$this->redirect(array('action' => 'index'));			
		}
				
		$options = array(			
			'conditions' => array(
				'Challan.' . $this->Challan->primaryKey => $id
			),
			'recursive' => 0
		);
		$challan = $this->Challan->find('first', $options);
		$challandetail = $this->ChallanDetail->find('all', array(
			'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),			
			'fields' => 'ChallanDetail.*,Product.product_code,Product.name,MeasurementUnit.name',
			'recursive' => 0
			)
		);
		
		$office_paren_id = $this->UserAuth->getOfficeParentId();
		$this->set(compact('challan','challandetail','office_paren_id'));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','New NCP Challan');
		
		$this->loadModel('ReturnChallan');
		$this->loadModel('ReturnChallanDetail');
		
		if ($this->request->is('post')) {
			
			if(empty($this->request->data['product_id']))
			{
				$this->Session->setFlash(__('NCP Challan not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}else{
				$this->Challan->validator()->remove('challan_referance_no', 'isUnique');
				$this->request->data['Challan']['transaction_type_id'] = 6; //6= CWH TO ASO (NCP Return) 
				$this->request->data['Challan']['inventory_status_id'] = 1; // Sound inventory  
				$this->request->data['Challan']['challan_date'] = $this->current_date(); 
				$this->request->data['Challan']['challan_referance_no'] = $this->request->data['Challan']['challan_id'];
				$this->request->data['Challan']['sender_store_id'] = $this->UserAuth->getStoreId();
				$this->request->data['Challan']['created_at'] = $this->current_datetime(); 
				$this->request->data['Challan']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['Challan']['status'] = 0;
				$this->request->data['Challan']['updated_at'] = $this->current_datetime(); 
				$this->request->data['Challan']['updated_by'] = $this->UserAuth->getUserId();
				//pr($this->request->data['Challan']);
				//die();
				$this->Challan->create();
				if ($this->Challan->save($this->request->data)) {
					
					$udata['id'] = $this->Challan->id;								
					$udata['challan_no'] = 'NCPCH'.(10000 + $this->Challan->id);								
					$this->Challan->save($udata); 
					
					if(!empty($this->request->data['product_id'])){
						$data_array = array();
						$update_challan_data_array = array();
						foreach($this->request->data['product_id'] as $key=>$val)
						{						
							$data['ChallanDetail']['challan_id'] = $this->Challan->id;
							$data['ChallanDetail']['product_id'] = $val;
							$data['ChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
							$data['ChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
							$data['ChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
							//$data['ChallanDetail']['expire_date'] = ($this->request->data['expire_date'][$key] !='' ? Date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : '');
							$date=(($this->request->data['expire_date'][$key] != ' ' && $this->request->data['expire_date'][$key]!='null' && $this->request->data['expire_date'][$key] !='')? explode('-',$this->request->data['expire_date'][$key]) : '');
                             if(!empty($date[1])){
                                $date[0]=date('m',strtotime($date[0]));
                                $a_date = date('y-m-d',mktime(0,0,0,$date[0],1,$date[1]));
                                $data['ChallanDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
                            }
                            else {
                                $data['ChallanDetail']['expire_date'] = '';
                            }
							$data['ChallanDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
							$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
							$data_array[] = $data;
							
							//------------------------
							/*
							$challan_info = $this->ReturnChallanDetail->find('first',array(
								'conditions' => array(
													'ReturnChallanDetail.challan_id' => $this->request->data['Challan']['challan_id'],
													'ReturnChallanDetail.product_id' => $val
												),
								'recursive' => -1				
							));	
							
							$update_challan_data['id'] = $challan_info['ReturnChallanDetail']['id'];
							$update_challan_data['transaction_type_id']=6;
							$update_challan_data['remaining_qty'] = $challan_info['ReturnChallanDetail']['remaining_qty'] - $this->request->data['quantity'][$key];
							$update_challan_data_array[] = $update_challan_data;*/
						}	
						
						$this->ChallanDetail->saveAll($data_array); 
						
						// Update Chalan quantity data
						//$this->ReturnChallanDetail->saveAll($update_challan_data_array);
					}
					
					// Challan close
					/*
					$do_udata['id'] = $this->request->data['Challan']['challan_id'];								
					$do_udata['is_close'] = ($this->request->data['Challan']['is_close'] == 1 ? $this->request->data['Challan']['is_close'] : NULL );								
					$this->ReturnChallan->save($do_udata);*/
					
					$this->Session->setFlash(__('Challan has been drafted.'), 'flash/success');
					$this->redirect(array('action' => 'edit',$this->Challan->id));
				}else {					
					$this->Session->setFlash(__('NCP Challan not created.'), 'flash/error');
					$this->redirect(array('action' => 'index'));			
				}
			}
		}
		
		$receiver_store = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 2),
			'order' => array('name'=>'asc')
		));
		
		
		//$products = $this->Product->find('list',array('conditions'=>array('is_active'=>1),'order' => array('name'=>'asc')));		
		$this->set(compact('receiver_store'));
	}


/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Challan->id = $id;
		if (!$this->Challan->exists()) {
			throw new NotFoundException(__('Invalid challan'));
		}
		if ($this->Challan->delete()) {
			$this->flash(__('Challan deleted'), array('action' => 'index'));
		}
		$this->flash(__('Challan was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}

    /**
	 * admin_edit method
	 */

	public function admin_edit($id = null) {
		$this->set('page_title','New NCP Challan');
		
		$this->loadModel('ReturnChallan');
		$this->loadModel('ReturnChallanDetail');

		$ncp_challan_id = $id;
		$ncp_challan_draft_info = $this->Challan->find('all',array(
				'conditions' => array('Challan.id' => $ncp_challan_id),
				'recursive' => 2
			));

		$challan_list_array = $this->ReturnChallan->find('all', array(
			'fields' => array('ReturnChallan.id as id', 'ReturnChallan.challan_no as title'),
				'conditions' => array(
					'ReturnChallan.is_close' => NULL,
					'ReturnChallan.transaction_type_id' => 15,//ASO to CWH (Receive NCP Return)
					'ReturnChallan.inventory_status_id' => 2,
					'ReturnChallan.receiver_store_id' => $this->UserAuth->getStoreId(),
					'ReturnChallan.sender_store_id' => $ncp_challan_draft_info[0]['Challan']['receiver_store_id']				
				),
				'recursive' => -1
		));

		foreach ($challan_list_array as $value) {
			$challan_list[$value[0]['id']] = $value[0]['title'];
		}

        $product_list_array = $this->ReturnChallanDetail->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'conditions' => array(
					'ReturnChallanDetail.challan_id' => $ncp_challan_draft_info[0]['Challan']['challan_referance_no']				
				),
			'recursive' => 0
		));

		foreach ($product_list_array as $value) {
			$product_list[$value['Product']['id']] = $value['Product']['name'];
		}
		
		if ($this->request->is('post')) {

			$this->ChallanDetail->deleteAll(array('ChallanDetail.challan_id'=>$ncp_challan_id));
			
			if(empty($this->request->data['product_id']))
			{
				$this->Session->setFlash(__('NCP Challan not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}else{
				$this->Challan->validator()->remove('challan_referance_no', 'isUnique');
				
				$this->request->data['Challan']['transaction_type_id'] = 6; //6= CWH TO ASO (NCP Return) 
				$this->request->data['Challan']['inventory_status_id'] = 1; // Sound inventory 
				$this->request->data['Challan']['challan_date'] = $this->current_date(); 
				$this->request->data['Challan']['challan_referance_no'] = $this->request->data['Challan']['challan_id'];
				$this->request->data['Challan']['sender_store_id'] = $this->UserAuth->getStoreId();			
				$this->request->data['Challan']['updated_at'] = $this->current_datetime(); 
				$this->request->data['Challan']['updated_by'] = $this->UserAuth->getUserId();
				$this->request->data['Challan']['id'] = $ncp_challan_id;
				if (array_key_exists('draft', $this->request->data)) {
					$this->request->data['Challan']['status'] = 0;
				}else{
					$this->request->data['Challan']['status'] = 1;
				}
				
				
				if ($this->Challan->save($this->request->data)) {
					
					$udata['id'] = $ncp_challan_id;								
					$udata['challan_no'] = 'NCPCH'.(10000 + $ncp_challan_id);								
					$this->Challan->save($udata); 
					
					if(!empty($this->request->data['product_id'])){
						$data_array = array();
						$update_challan_data_array = array();
						foreach($this->request->data['product_id'] as $key=>$val)
						{						
							$data['ChallanDetail']['challan_id'] = $ncp_challan_id;
							$data['ChallanDetail']['product_id'] = $val;
							$data['ChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
							$data['ChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
							$data['ChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
							//$data['ChallanDetail']['expire_date'] = ($this->request->data['expire_date'][$key] !='' ? Date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : '');
							$date=(($this->request->data['expire_date'][$key] != ' ' && $this->request->data['expire_date'][$key]!='null' && $this->request->data['expire_date'][$key] !='')? explode('-',$this->request->data['expire_date'][$key]) : '');
							if(!empty($date[2])){	
								$data['ChallanDetail']['expire_date'] = $this->request->data['expire_date'][$key];
								
							}
							else
							{
								if(!empty($date[1])){
									$date[0]=date('m',strtotime($date[0]));
									$a_date = date('y-m-d',mktime(0,0,0,$date[0],1,$date[1]));
									$data['ChallanDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
								}
								else {
									$data['ChallanDetail']['expire_date'] = '';
								}
							}
							$data['ChallanDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
							$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
							$data_array[] = $data;
							
							//------------------------
							
							$challan_info = $this->ReturnChallanDetail->find('first',array(
								'conditions' => array(
													'ReturnChallanDetail.challan_id' => $this->request->data['Challan']['challan_id'],
													'ReturnChallanDetail.product_id' => $val
												),
								'recursive' => -1				
							));	
							
							$update_challan_data['id'] = $challan_info['ReturnChallanDetail']['id'];
							$update_challan_data['transaction_type_id']=6; //CWH to ASO Return
							$update_challan_data['remaining_qty'] = $challan_info['ReturnChallanDetail']['remaining_qty'] - $this->request->data['quantity'][$key];
							$update_challan_data_array[] = $update_challan_data;
						}	
						
						$this->ChallanDetail->saveAll($data_array); 
						
						// Update Chalan quantity data
						if (array_key_exists('save', $this->request->data)) {
							$this->ReturnChallanDetail->saveAll($update_challan_data_array);
						}
					}
					
					// Challan close
					if (array_key_exists('save', $this->request->data)) {
						$do_udata['id'] = $this->request->data['Challan']['challan_id'];								
						$do_udata['is_close'] = ($this->request->data['Challan']['is_close'] == 1 ? $this->request->data['Challan']['is_close'] : NULL );								
						$this->ReturnChallan->save($do_udata);
					}
					
					$this->Session->setFlash(__('Challan has been created.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}else {					
					$this->Session->setFlash(__('NCP Challan not created.'), 'flash/error');
					$this->redirect(array('action' => 'index'));			
				}
			}
		}
		
		$receiver_store = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 2),
			'order' => array('name'=>'asc')
		));
		
		
		//$products = $this->Product->find('list',array('conditions'=>array('is_active'=>1),'order' => array('name'=>'asc')));		
		$this->set(compact('receiver_store', 'ncp_challan_draft_info', 'challan_list', 'product_list'));
	}
	
	
	/*----------------------- Chainbox Data ---------------------------*/
	
	public function get_challan_list()
	{				
		$this->loadModel('ReturnChallan');
		$rs = array(array('id' => '', 'title' => '---- Select Challan -----'));
		$receiver_store_id = $this->request->data['receiver_store_id'];
        $challan_list = $this->ReturnChallan->find('all', array(
			'fields' => array('ReturnChallan.id as id', 'ReturnChallan.challan_no as title'),
				'conditions' => array(
					'ReturnChallan.is_close' => NULL,
					'ReturnChallan.transaction_type_id' => 15,//ASO to CWH (Receive NCP Return)
					'ReturnChallan.inventory_status_id' => 2,
					'ReturnChallan.receiver_store_id' => $this->UserAuth->getStoreId(),
					'ReturnChallan.sender_store_id' => $receiver_store_id				
				),
				'recursive' => -1
		));
		$data_array = Set::extract($challan_list, '{n}.0');
		if(!empty($challan_list)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	
	
	
	public function get_challan_product_list()
	{				
		$this->loadModel('ReturnChallanDetail');
		$rs = array(array('id' => '', 'title' => '---- Select Product -----'));
		$challan_id = $this->request->data['challan_id'];
        $do_list = $this->ReturnChallanDetail->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'conditions' => array(
					'ReturnChallanDetail.challan_id' => $challan_id				
				),
			'order' => array('Product.order'=>'asc'),
			'recursive' => 0
		));
		
		if(!empty($do_list))
		{
			$array = array();
			foreach($do_list as $val){
				$data['id'] = $val['Product']['id'];
				$data['title'] = $val['Product']['name'];
				$array[] = $data;
			}
			echo json_encode(array_merge($rs,$array));
		}else{
			echo json_encode($rs);
		}		
		$this->autoRender = false;
	}
	
	
	public function get_inventory_details()
	{	
		$this->loadModel('ReturnChallanDetail');
		$challan_id = $this->request->data['challan_id'];
		$product_id = $this->request->data['product_id'];
		
		$challan_info = $this->ReturnChallanDetail->find('first', array(
			'fields' => array('ReturnChallanDetail.challan_qty','ReturnChallanDetail.remaining_qty'),
			'conditions' => array(
				'ReturnChallanDetail.challan_id' => $challan_id,
				'ReturnChallanDetail.product_id' => $product_id		
			),
			'recursive' => -1
		));		
		$data['qty'] = $challan_info['ReturnChallanDetail']['challan_qty'];		
		$data['remaining_qty'] = $challan_info['ReturnChallanDetail']['remaining_qty'];		
		echo json_encode($data);				
		$this->autoRender = false;
	}
	
}
