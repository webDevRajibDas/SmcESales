<?php
App::uses('AppController', 'Controller');
/**
 * Challans Controller
 *
 * @property ReturnChallan $ReturnChallan
 * @property PaginatorComponent $Paginator
 */

class NcpReturnChallansController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Filter.Filter');
	public $uses = array('ReturnChallan','ReturnChallanDetail','Store','Product', 'MeasurementUnit', 'Territory','CurrentInventory');
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','NCP Return Challan List');		
		$this->ReturnChallan->recursive = 0;
		if($this->UserAuth->getOfficeParentId() == 0)
		{
			$conditions = array(
				//'ReturnChallan.transaction_type_id' => 8,
				'ReturnChallan.inventory_status_id' => 2,
				'OR' => array(
					array('ReturnChallan.transaction_type_id' => 8), //ASO TO CWH (NCP Return)	
					array('ReturnChallan.transaction_type_id' => 15), //ASO to CWH (Receive NCP Return)
					
				),
			);
		}else{
			$conditions = array(
				
				'ReturnChallan.inventory_status_id' => 2,
				'OR' => array(
					array('ReturnChallan.transaction_type_id' => 8), //ASO TO CWH (NCP Return)	
					array('ReturnChallan.transaction_type_id' => 15), //ASO to CWH (Receive NCP Return)
					
				),
				'ReturnChallan.sender_store_id' => $this->UserAuth->getStoreId()				
			);
		}			
		$this->paginate = array(
			'conditions' => $conditions,
			'recursive' => 0,
			'order' => array('ReturnChallan.id' => 'desc')						
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
		$this->set('page_title','Ncp Ret. challan Details');
		if (!$this->ReturnChallan->exists($id)) {
			throw new NotFoundException(__('Invalid challan'));
		}
		
		if ($this->request->is('post')) {			
			// update challan status 
			


			$challan['id'] = $id;
			$challan['status'] = 2;
			$challan['received_date'] = date('Y-m-d',strtotime($this->request->data['ReturnChallan']['received_date']));
			$challan['updated_at'] = $this->current_datetime();
			$challan['updated_by'] = $this->UserAuth->getUserId();
			$challan['transaction_type_id'] = 15; // 15 = ASO to CWH (Receive NCP Return)		
			$this->ReturnChallan->save($challan);
			
			$this->Session->setFlash(__('Return Challan has been received.'), 'flash/success');
			$this->redirect(array('action' => 'index'));			
		}
			
		$options = array(
			'conditions' => array('ReturnChallan.' . $this->ReturnChallan->primaryKey => $id),
			'recursive' => 0
		);
		$challan = $this->ReturnChallan->find('first', $options);
				
		$challandetail = $this->ReturnChallanDetail->find('all', array(
			'conditions' => array('ReturnChallanDetail.challan_id' => $challan['ReturnChallan']['id']),			
			'fields' => 'ReturnChallanDetail.*,Product.product_code,Product.name,MeasurementUnit.name',
			'order' => array('Product.order'=>'asc'),
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
		$this->set('page_title','New NCP ret.Challan');
		
		if ($this->request->is('post')) {
			
			if(empty($this->request->data['product_id']))
			{
				$this->Session->setFlash(__('NCP Return Challan not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}else{
				
				$this->request->data['ReturnChallan']['transaction_type_id'] = 8; // 8 = ASO TO CWH (NCP Return)	
				$this->request->data['ReturnChallan']['inventory_status_id'] = 2; //NCP inventory
				
				$this->request->data['ReturnChallan']['challan_date'] = $this->current_date(); 
				$this->request->data['ReturnChallan']['sender_store_id'] = $this->UserAuth->getStoreId();
				$this->request->data['ReturnChallan']['status'] = 0;
				$this->request->data['ReturnChallan']['created_at'] = $this->current_datetime(); 
				$this->request->data['ReturnChallan']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['ReturnChallan']['updated_at'] = $this->current_datetime(); 
				$this->request->data['ReturnChallan']['updated_by'] = $this->UserAuth->getUserId();
				$this->ReturnChallan->create();
				if ($this->ReturnChallan->save($this->request->data)) {
					
					$udata['id'] = $this->ReturnChallan->id;								
					$udata['challan_no'] = 'RNCH'.(10000 + $this->ReturnChallan->id);
					$this->ReturnChallan->save($udata); 
					if(!empty($this->request->data['product_id'])){
						$data_array = array();
						$update_data_array = array();
						$so_update_data_array = array();
						$insert_data_array = array();
						foreach($this->request->data['product_id'] as $key=>$val)
						{

							$product_details = $this->Product->find('first', array(
								'fields' => array('id', 'is_virtual', 'parent_id'),
								'conditions' => array('Product.id' => $val),
								'recursive' => -1
							));

							//$data['ReturnChallanDetail']['product_id'] = $val;

							if ($product_details['Product']['is_virtual'] == 1) {
								$data['ReturnChallanDetail']['virtual_product_id'] = $product_details['Product']['id'];
								$data['ReturnChallanDetail']['product_id'] = $product_details['Product']['parent_id'];
							} else {
								$data['ReturnChallanDetail']['virtual_product_id'] = 0;
								$data['ReturnChallanDetail']['product_id'] = $product_details['Product']['id'];
							}


							$data['ReturnChallanDetail']['challan_id'] = $this->ReturnChallan->id;
							
							$data['ReturnChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
							$data['ReturnChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
							$data['ReturnChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
							/*$data['ReturnChallanDetail']['expire_date'] = ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null);*/
							$data['ReturnChallanDetail']['inventory_status_id'] = 2;
							$data['ReturnChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
							$data['ReturnChallanDetail']['remaining_qty'] = $this->request->data['quantity'][$key];
							if($this->request->data['expire_date'][$key] != ' ' && $this->request->data['expire_date'][$key] != 'null' && $this->request->data['expire_date'][$key] !=''){
								$data['ReturnChallanDetail']['expire_date'] = date('Y-m-d', strtotime($this->request->data['expire_date'][$key]));
							}
							else{
								$data['ReturnChallanDetail']['expire_date'] = '';
							}
							$data_array[] = $data;
							
							// ------------ stock update --------------------			
							/*
							$inventory_info = $this->CurrentInventory->find('first',array(
								'conditions' => array(
													'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
													'CurrentInventory.inventory_status_id' => 2,
													'CurrentInventory.product_id' => $val,
													'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
													'CurrentInventory.expire_date' => ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null)
												),
								'recursive' => -1				
							));						
						
							$deduct_quantity = $this->request->data['quantity'][$key];
							
							$update_data['id'] = $inventory_info['CurrentInventory']['id'];
							$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] - $deduct_quantity;
							$update_data['transaction_type_id'] = 8; // ASO to CWH (NCP Return)	
							$update_data_array[] = $update_data;*/
						}								
						// insert challan data
						$this->ReturnChallanDetail->saveAll($data_array); 					
						// Update inventory data
						//$this->CurrentInventory->saveAll($update_data_array);					
						
					}
					
					$this->Session->setFlash(__('NCP challan has been drafted.'), 'flash/success');
					$this->redirect(array('action' => 'edit',$this->ReturnChallan->id));
				}
			}
		}
			
        /*			
		$receiverStore = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 1,'office_id' => $this->UserAuth->getOfficeParentId()),
			'order' => array('name'=>'asc')
		));
		*/
		
		$receiverStore = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 1),
			'order' => array('name'=>'asc')
		));
			
		//$products = $this->Product->find('list',array('order' => array('name'=>'asc')));		
		$products_from_ci = $this->CurrentInventory->find('all', array('fields' => array('DISTINCT CurrentInventory.product_id'),
            'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(),'CurrentInventory.qty > ' => 0,'CurrentInventory.inventory_status_id'=>2)
        ));
        
        $product_ci=array();
        foreach ($products_from_ci as $each_ci) {
            $product_ci[]=$each_ci['CurrentInventory']['product_id'];
        }
        
        $product_ci_in=implode(",",$product_ci);        
        $products = $this->Product->find('list', array('conditions' => array('Product.id' => $product_ci),'order' => array('order' => 'asc')));
		
		$this->set(compact('receiverStore','products'));
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
		$this->ReturnChallan->id = $id;
		if (!$this->ReturnChallan->exists()) {
			throw new NotFoundException(__('Invalid challan'));
		}
		if ($this->ReturnChallan->delete()) {
			$this->flash(__('ReturnChallan deleted'), array('action' => 'index'));
		}
		$this->flash(__('ReturnChallan was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}

    public function admin_edit($id = null) {
		$this->set('page_title','Edit NCP Return Challan');

		$return_challan_id = $id;
		$return_challan_info = $this->ReturnChallan->find('all', array(
				'conditions' => array('ReturnChallan.id' => $return_challan_id),
				'recursive' => 1
			));

		$this->loadModel('ProductPrice');
		$product_price_list = $this->ProductPrice->find('list',array(
				'fields' => array('product_id','general_price')
			));
			
			
		
		foreach ($return_challan_info[0]['ReturnChallanDetail'] as $key => $value) {
			
			
			if ($value['virtual_product_id'] && $value['virtual_product_id'] > 0) {
				$value['product_id'] = $value['virtual_product_id'];
			}
			
	        $current_inventory_info = $this->CurrentInventory->find('first', array(
	          'conditions'=>array(
	          	  'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
	              'CurrentInventory.product_id' => $value['product_id'],
	              'CurrentInventory.batch_number' => $value['batch_no'],
	              'CurrentInventory.expire_date' => $value['expire_date'],
	              'CurrentInventory.inventory_status_id' => 2
	            ),
	          'recursive' => -1
	        ));
	        //$return_challan_info[0]['ReturnChallanDetail'][$key]['stock_qty'] = $this->unit_convertfrombase($value['product_id'],$value['Product']['return_measurement_unit_id'],$current_inventory_info['CurrentInventory']['qty']);
			
			$product_info = $this->Product->find('first', array(
				'conditions' => array('Product.id' => $value['product_id']),
				'recursive' => -1
			));
			$measurement_unit_info = $this->MeasurementUnit->find('first', array(
				'conditions' => array('MeasurementUnit.id' => $value['measurement_unit_id']),
				'recursive' => -1
			));
			$return_challan_info[0]['ReturnChallanDetail'][$key]['Product'] = $product_info['Product'];
			$return_challan_info[0]['ReturnChallanDetail'][$key]['MeasurementUnit'] = $measurement_unit_info['MeasurementUnit'];
			
	        $return_challan_info[0]['ReturnChallanDetail'][$key]['stock_qty'] = $current_inventory_info['CurrentInventory']['qty'];
	      }
		if ($this->request->is('post')) {

            $this->ReturnChallanDetail->deleteAll(array('ReturnChallanDetail.challan_id'=>$return_challan_id));
			
			if(empty($this->request->data['product_id']))
			{
				$this->Session->setFlash(__('NCP Return Challan not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}else{
				
				$this->request->data['ReturnChallan']['transaction_type_id'] = 8; // 8 = ASO TO CWH (NCP Return)	 
				$this->request->data['ReturnChallan']['inventory_status_id'] = 2;
				
				$this->request->data['ReturnChallan']['challan_date'] = $this->current_date(); 
				$this->request->data['ReturnChallan']['sender_store_id'] = $this->UserAuth->getStoreId(); 
				$this->request->data['ReturnChallan']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['ReturnChallan']['updated_at'] = $this->current_datetime(); 
				$this->request->data['ReturnChallan']['updated_by'] = $this->UserAuth->getUserId();
				$this->request->data['ReturnChallan']['id'] = $return_challan_id;
				if (array_key_exists('draft', $this->request->data)) {
					$this->request->data['ReturnChallan']['status'] = 0;
				}else{
					$this->request->data['ReturnChallan']['status'] = 1;
				}
				if ($this->ReturnChallan->save($this->request->data)) {
					
					$udata['id'] = $return_challan_id;								
					$udata['challan_no'] = 'RNCH'.(10000 + $return_challan_id);
					$this->ReturnChallan->save($udata); 
					if(!empty($this->request->data['product_id'])){
						$data_array = array();
						$update_data_array = array();
						$so_update_data_array = array();
						$insert_data_array = array();
						foreach($this->request->data['product_id'] as $key=>$val)
						{

							$product_details = $this->Product->find('first', array(
								'fields' => array('id', 'is_virtual', 'parent_id'),
								'conditions' => array('Product.id' => $val),
								'recursive' => -1
							));

							//$data['ReturnChallanDetail']['product_id'] = $val;
							if ($product_details['Product']['is_virtual'] == 1) {
								$data['ReturnChallanDetail']['virtual_product_id'] = $product_details['Product']['id'];
								$data['ReturnChallanDetail']['product_id'] = $product_details['Product']['parent_id'];
							} else {
								$data['ReturnChallanDetail']['virtual_product_id'] = 0;
								$data['ReturnChallanDetail']['product_id'] = $product_details['Product']['id'];
							}

							$data['ReturnChallanDetail']['challan_id'] = $return_challan_id;
							
							$data['ReturnChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
							$data['ReturnChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
							$data['ReturnChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
							//$data['ReturnChallanDetail']['expire_date'] = ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null);
							$data['ReturnChallanDetail']['inventory_status_id'] = 2;
							$data['ReturnChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
							$data['ReturnChallanDetail']['remaining_qty'] = $this->request->data['quantity'][$key];
							if($this->request->data['expire_date'][$key] != ' ' && $this->request->data['expire_date'][$key] != 'null' && $this->request->data['expire_date'][$key] !=''){
								$data['ReturnChallanDetail']['expire_date'] = date('Y-m-d', strtotime($this->request->data['expire_date'][$key]));
							}
							else{
								$data['ReturnChallanDetail']['expire_date'] = '';
							}
							$data_array[] = $data;
							
							// ------------ stock update --------------------	

							$inventory_info = $this->CurrentInventory->find('first',array(
								'conditions' => array(
													'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
													'CurrentInventory.inventory_status_id' => 2,
													'CurrentInventory.product_id' => $val,
													'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
													'CurrentInventory.expire_date' => $data['ReturnChallanDetail']['expire_date']
												),
								'recursive' => -1				
							));				
						
							$deduct_quantity = $this->request->data['quantity'][$key];
							
							$update_data['id'] = $inventory_info['CurrentInventory']['id'];
							$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] - $deduct_quantity;
							$update_data['transaction_type_id'] = 8; // ASO to CWH (NCP Return)	
							
							$update_data['transaction_date'] = $this->current_date();
							
							
							$update_data_array[] = $update_data;
						}

						if(array_key_exists('draft', $this->request->data)){
							// insert challan data
							$this->ReturnChallanDetail->saveAll($data_array); 
						}else{
							// insert challan data
							$this->ReturnChallanDetail->saveAll($data_array); 					
							// Update inventory data
							$this->CurrentInventory->saveAll($update_data_array);
						}												
						
					}
					
					$this->Session->setFlash(__('NCP challan has been updated.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
			}
		}
		
        /*		
		$receiverStore = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 1,'office_id' => $this->UserAuth->getOfficeParentId()),
			'order' => array('name'=>'asc')
		));	
		*/
		
		$receiverStore = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 1),
			'order' => array('name'=>'asc')
		));	
		
		
		//$products = $this->Product->find('list',array('order' => array('name'=>'asc')));
$products_from_ci = $this->CurrentInventory->find('all', array('fields' => array('DISTINCT CurrentInventory.product_id'),
            'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(),'CurrentInventory.qty > ' => 0,'CurrentInventory.inventory_status_id'=>2)
        ));
        
        $product_ci=array();
        foreach ($products_from_ci as $each_ci) {
            $product_ci[]=$each_ci['CurrentInventory']['product_id'];
        }
        
        $product_ci_in=implode(",",$product_ci);        
        $products = $this->Product->find('list', array('conditions' => array('Product.id' => $product_ci),'order' => array('order' => 'asc')));		
		$this->set(compact('receiverStore','products','return_challan_info','product_price_list'));
	}
}
