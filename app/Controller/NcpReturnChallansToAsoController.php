<?php
App::uses('AppController', 'Controller');
/**
 * Challans Controller
 *
 * @property Challan $ReturnChallan
 * @property PaginatorComponent $Paginator
 */
class NcpReturnChallansToAsoController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Filter.Filter');
	public $uses = array('ReturnChallan','Store','Product','CurrentInventory','ReturnChallanDetail');
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {

		$this->set('page_title','Ncp Ret. Challan List');
	//	$this->ReturnChallan->recursive = -1;
		//$this->loadModel('ReturnChallan');
		$this->paginate = array(
			'conditions' => array(
				'ReturnChallan.inventory_status_id' => 2,
				'AND'=>array(
					array(
						'OR' => array(
							array('ReturnChallan.transaction_type_id' => 10), //SO TO ASO (NCP Return)
							array('ReturnChallan.transaction_type_id' => 13), //SO TO ASO (Receive NCP Return)
							)
						),
					array(
						'OR'=>array(
							array('ReturnChallan.sender_store_id' => $this->UserAuth->getStoreId()),//$this->UserAuth->getStoreId()
							array('ReturnChallan.receiver_store_id' => $this->UserAuth->getStoreId()),
							)
						)
					)
			),
			'recursive' => 0,
			'order' => array('ReturnChallan.id' => 'desc')
		);
		$this->LoadModel('Store');
		$storeCondition = array('Store.office_id' => $this->UserAuth->getOfficeId(),'Store.store_type_id'=>3);
		$store_list = $this->Store->find('all', 
		array(
			'conditions' => $storeCondition, 
			'fields' => array('Store.id', 'Store.name', 'Store.territory_id'),
			//'order' => array('Store.name' => 'asc'),
        	'recursive' => -1)
		);
		
		$senderStores = array();
		foreach($store_list as $key => $value)
		{
			$senderStores[$value['Store']['id']] = $value['Store']['name'].' ('.$this->getSOName($value['Store']['territory_id']).')';
		}
		$this->set(compact('senderStores'));
		$this->set('returnChallans',$this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {		
		
		$this->set('page_title','Ncp Ret.Challan Details');
		if (!$this->ReturnChallan->exists($id)) {
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
					$quantity = $receive_quantity;
					
				}else{
					$receive_quantity = $this->request->data['quantity'][$key];
					$quantity = $receive_quantity;
				}
				$inventory_info=array();
				// ------------ stock update --------------------			
				$inventory_info = $this->CurrentInventory->find('first',array(
					'conditions' => array(
										'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
										'CurrentInventory.inventory_status_id' => 2,
										'CurrentInventory.product_id' => $val,
										'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
										'CurrentInventory.expire_date' => $this->request->data['expire_date'][$key]
									),
					'recursive' => -1				
				));
				//pr($inventory_info);
				//echo $this->CurrentInventory->getLastQuery().'<br>';	
				if(!empty($inventory_info))
				{
					$update_data['id'] = $inventory_info['CurrentInventory']['id'];
					$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] + $quantity;
					$update_data['updated_at'] = $this->current_datetime();
					$update_data['transaction_type_id'] = 13; // SO TO ASO (Receive NCP Return)
					$update_data['transaction_date'] = date('Y-m-d',strtotime($this->request->data['ReturnChallan']['received_date']));	
					// $update_data_array[] = $update_data;
					$this->CurrentInventory->save($update_data);
					unset($update_data);
				}else{
					$insert_data['store_id'] = $this->UserAuth->getStoreId();
					$insert_data['inventory_status_id'] = 2; //NCP inventory
					$insert_data['product_id'] = $val;
					$insert_data['batch_number'] = $this->request->data['batch_no'][$key];
					$insert_data['expire_date'] = $this->request->data['expire_date'][$key];
					$insert_data['qty'] = $quantity;
					$insert_data['updated_at'] = $this->current_datetime();
					$insert_data['transaction_type_id'] = 13; // SO TO ASO (Receive NCP Return)
					$insert_data['transaction_date'] = date('Y-m-d',strtotime($this->request->data['ReturnChallan']['received_date']));	
					// $insert_data_array[] = $insert_data;
					//pr($insert_data);
					$this->CurrentInventory->create();
					$this->CurrentInventory->save($insert_data);
					unset($insert_data);
				}
								
				//----------------- End Stock update ---------------------
			
				$data['ReturnChallanDetail']['id'] = $this->request->data['id'][$key];
				$data['ReturnChallanDetail']['received_qty'] = $receive_quantity;
				$data['ReturnChallanDetail']['remaining_qty'] = $receive_quantity;
				$data_array[] = $data;
			}
			
			// insert inventory data
			/*$this->CurrentInventory->saveAll($insert_data_array);
			
			// Update inventory data
			$this->CurrentInventory->saveAll($update_data_array);*/
			
			// update received quantity
			$this->ReturnChallanDetail->saveAll($data_array);
			
			// update challan status 
			$returnChalan['id'] = $id;
			$returnChalan['status'] = 2;
			$returnChalan['received_date'] = date('Y-m-d',strtotime($this->request->data['ReturnChallan']['received_date']));
			$returnChalan['updated_at'] = $this->current_datetime();
			$returnChalan['updated_by'] = $this->UserAuth->getUserId();
			$returnChalan['transaction_type_id'] = 13; // SO TO ASO (Receive NCP Return)
			$this->ReturnChallan->save($returnChalan);

			$this->Session->setFlash(__('Return Challan has been received.'), 'flash/success');
			$this->redirect(array('action' => 'index'));
			
		}
		

		//$this->loadModel('ReturnChallan');
		$options = array(			
			'conditions' => array(
				'ReturnChallan.' . $this->ReturnChallan->primaryKey => $id
			),
			'recursive' => 0
		);
		$returnChallan = $this->ReturnChallan->find('first', $options);
		$returnChallanDetail = $this->ReturnChallanDetail->find('all', array(
			'conditions' => array('ReturnChallanDetail.challan_id' => $returnChallan['ReturnChallan']['id']),
			'fields' => 'ReturnChallanDetail.*,Product.product_code,Product.name,MeasurementUnit.name',
			'order' => array('Product.order'=>'asc'),
			'recursive' => 0
			)
		);

		//pr($returnChallan);
		//exit;
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$this->set(compact('returnChallan','returnChallanDetail','office_parent_id'));
	}

/**
 * admin_add method
 *
 * @return void
 */


	public function admin_add() {
		$this->set('page_title','Ncp Ret Challan Add');

		if ($this->request->is('post')) {

			if(empty($this->request->data['product_id']))
			{
				$this->Session->setFlash(__('Return Challan to ASO not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}else{ 

				$this->request->data['ReturnChallan']['transaction_type_id'] = 10; //SO TO ASO (NCP Return)
				$this->request->data['ReturnChallan']['inventory_status_id'] = 2; //NCP Return

				$this->request->data['ReturnChallan']['challan_date'] = $this->current_date();
				$this->request->data['ReturnChallan']['sender_store_id'] = $this->UserAuth->getStoreId();
				//$this->request->data['ReturnChallan']['sender_store_id'] =33; //khamarbari store
				$this->request->data['ReturnChallan']['status'] = 1;
				$this->request->data['ReturnChallan']['created_at'] = $this->current_datetime();
				$this->request->data['ReturnChallan']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['ReturnChallan']['updated_at'] = $this->current_datetime();
				$this->request->data['ReturnChallan']['updated_by'] = 0;
				$this->ReturnChallan->create();
				if ($this->ReturnChallan->save($this->request->data)) {

					$udata['id'] = $this->ReturnChallan->id;
					$udata['challan_no'] = 'RNCHASO'.(10000 + $this->ReturnChallan->id);
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
							$data['ReturnChallanDetail']['expire_date'] = ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null);
							$data['ReturnChallanDetail']['inventory_status_id'] = 2;
							$data['ReturnChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
							$data_array[] = $data;

							// ------------ stock update --------------------
							$inventory_info = $this->CurrentInventory->find('first',array(
								'conditions' => array(
									//'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
									'CurrentInventory.store_id' =>33,   //khamarbari store
									'CurrentInventory.inventory_status_id' => 1, //2-ncp 1-sound
									'CurrentInventory.product_id' => $val,
									'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
									'CurrentInventory.expire_date' => ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null)
								),
								'recursive' => -1
							));


							$deduct_quantity = $this->request->data['quantity'][$key];

							$update_data['id'] = $inventory_info['CurrentInventory']['id'];
							$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] - $deduct_quantity;
							$update_data['transaction_date'] = $this->current_date();	
							$update_data_array[] = $update_data;
						}
						// insert challan data
						$this->ReturnChallanDetail->saveAll($data_array);
						// Update inventory data
						$this->CurrentInventory->saveAll($update_data_array);

					}

					$this->Session->setFlash(__('Product Returned has been created.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
			}
		}

		$receiverStore = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 2,'office_id' => 15),//$this->UserAuth->getOfficeParentId()
			'order' => array('name'=>'asc')
		));

		$products = $this->Product->find('list',array('order' => array('name'=>'asc')));
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
			$this->flash(__('Challan deleted'), array('action' => 'index'));
		}
		$this->flash(__('Challan was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}

	public function getSOName($territory_id=0)
	{
		if($territory_id)
		{
			$this->loadModel('Territory');
			$territory_info = $this->Territory->find('first', 
				array(
					'conditions' => array('Territory.id' => $territory_id),
					'fields' => array('SalesPerson.name'),
					'recursive' => 0
				)
			);
			//pr($territory_info);
			//exit;
			if($territory_info['SalesPerson']['name']){
				return $territory_info['SalesPerson']['name'];
			}else{
				return 'NA';
			}
		}
		else
		{
			return 'NA';
		}
	}
}
