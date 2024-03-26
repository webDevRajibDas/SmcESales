<?php
App::uses('AppController', 'Controller');
/**
 * Challans Controller
 *
 * @property ReturnChallan $ReturnChallan
 * @property PaginatorComponent $Paginator
 */
class DamageReturnChallansController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Filter.Filter');
	public $uses = array('ReturnChallan','ReturnChallanDetail','Store','Product','Territory','CurrentInventory');
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Damage Return Challan List');
		$this->ReturnChallan->recursive = 0;
		if($this->UserAuth->getOfficeParentId() == 0)
		{
			$conditions = array(
				'ReturnChallan.transaction_type_id' => 4,
				'ReturnChallan.inventory_status_id' => 3,
			);
		}else{
			$conditions = array(
				'ReturnChallan.transaction_type_id' => 4,
				'ReturnChallan.inventory_status_id' => 3,
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
		
		$this->set('page_title','Damage ret.challan Details');
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
		$this->set('page_title','New Damage Ret.Challan');
		
		if ($this->request->is('post')) {
			
			if(empty($this->request->data['product_id']))
			{
				$this->Session->setFlash(__('DO Challan not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}else{
				
				$this->request->data['ReturnChallan']['transaction_type_id'] = 4; 
				$this->request->data['ReturnChallan']['inventory_status_id'] = 3;
				
				$this->request->data['ReturnChallan']['challan_date'] = $this->current_date(); 
				$this->request->data['ReturnChallan']['sender_store_id'] = $this->UserAuth->getStoreId();
				$this->request->data['ReturnChallan']['status'] = 1;
				$this->request->data['ReturnChallan']['created_at'] = $this->current_datetime(); 
				$this->request->data['ReturnChallan']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['ReturnChallan']['updated_at'] = $this->current_datetime(); 
				$this->request->data['ReturnChallan']['updated_by'] = 0;
				$this->ReturnChallan->create();
				if ($this->ReturnChallan->save($this->request->data)) {
					
					$udata['id'] = $this->ReturnChallan->id;								
					$udata['challan_no'] = 'RCH'.(10000 + $this->ReturnChallan->id);								
					$this->ReturnChallan->save($udata); 
					if(!empty($this->request->data['product_id'])){
						$data_array = array();
						$update_data_array = array();
						$so_update_data_array = array();
						$insert_data_array = array();
						foreach($this->request->data['product_id'] as $key=>$val)
						{
							$data['ReturnChallanDetail']['challan_id'] = $this->ReturnChallan->id;
							$data['ReturnChallanDetail']['product_id'] = $val;
							$data['ReturnChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
							$data['ReturnChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
							$data['ReturnChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
							$data['ReturnChallanDetail']['expire_date'] = ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null);
							$data['ReturnChallanDetail']['inventory_status_id'] = 1;
							$data['ReturnChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
							$data_array[] = $data;
							
							// ------------ stock update --------------------			
							$inventory_info = $this->CurrentInventory->find('first',array(
								'conditions' => array(
													'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
													'CurrentInventory.inventory_status_id' => 3,
													'CurrentInventory.product_id' => $val,
													'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
													'CurrentInventory.expire_date' => ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null)
												),
								'recursive' => -1				
							));						
						
							$deduct_quantity = $this->unit_convert($val,$this->request->data['measurement_unit'][$key],$this->request->data['quantity'][$key]);
							
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
					
					$this->Session->setFlash(__('Product issue has been completed.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
			}
		}
					
		$receiverStore = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 1,'office_id' => $this->UserAuth->getOfficeParentId()),
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
			$this->flash(__('ReturnChallan deleted'), array('action' => 'index'));
		}
		$this->flash(__('ReturnChallan was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
}
