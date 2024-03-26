<?php
App::uses('AppController', 'Controller');
/**
 * InventoryAdjustments Controller
 *
 * @property InventoryAdjustment $InventoryAdjustment
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class InventoryAdjustmentsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session','Filter.Filter');
	public $uses = array('InventoryAdjustment', 'InventoryAdjustmentDetail', 'CurrentInventory');
	
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Inventory adjustment List');
		$this->InventoryAdjustment->recursive = 0;
		$conditions=array();
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id!=0)
		{
			$conditions=array('Store.id'=>$this->UserAuth->getStoreId());
		}
		$this->paginate = array(
			'joins'=>array(
				array(
					'table'=>'offices',
					'alias'=>'Office',
					'conditions'=>'Office.id=Store.office_id'
					)
				),
			'conditions'=>$conditions,
			'order' => array('InventoryAdjustment.id' => 'DESC'),
			);

		// pr($this->paginate());exit;
		$this->set('inventoryAdjustments', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Inventory adjustment Details');
		if (!$this->InventoryAdjustment->exists($id)) {
			throw new NotFoundException(__('Invalid inventory adjustment'));
		}
		$options = array(
		'conditions' => array('InventoryAdjustment.' . $this->InventoryAdjustment->primaryKey => $id)
		);
		$adjustment_list = $this->InventoryAdjustment->find('first', $options);
		// pr($adjustment_list);exit;
		$adjustment_with_product = array();
		$total_ajustment_product = array();
		foreach($adjustment_list['InventoryAdjustmentDetail'] as $key=>$val){
			$product_info = $this->CurrentInventory->find('all',array('conditions'=>array('CurrentInventory.id'=>$val['current_inventory_id'])));
			$adjustment_with_product['CurrentInventory'] = $product_info[0]['CurrentInventory'];
			$adjustment_with_product['Store'] = $product_info[0]['Store'];
			$adjustment_with_product['Product'] = $product_info[0]['Product'];
			$adjustment_with_product['InventoryStatuses'] = $product_info[0]['InventoryStatuses'];
			$val['product_info'] = $adjustment_with_product;
			$total_ajustment_product[] = $val;
		}
		$adjustment_list['InventoryAdjustmentDetail'] = $total_ajustment_product;
		$this->set('inventoryAdjustment',$adjustment_list);
		$this->set('office_paren_id',$this->UserAuth->getOfficeParentId());
		if($this->request->is('POST')){
			//pr($this->request->data);
			
			if(!empty($this->request->data['CurrentInventory']['current_inventory_id']))
			{
				/*$data['CurrentInventory']['transaction_type_id'] = $this->request->data['InventoryAdjustment']['transaction_type_id'];
				$data['CurrentInventory']['transaction_date'] = date('Y-m-d',strtotime($adjustment_list['InventoryAdjustment']['created_at']));
				foreach($this->request->data['CurrentInventory']['current_inventory_id'] as $current_inventory_key=>$current_inventory_val)
				{
					$current_inventory_info = $this->CurrentInventory->find('first',array(
						'conditions' => array('CurrentInventory.id'=>$current_inventory_val),
						'fields' => array('CurrentInventory.*')
					));
					$current_qty = $current_inventory_info['CurrentInventory']['qty'];
					$adjust_qty = $this->request->data['CurrentInventory']['quantity'][$current_inventory_key];
					if($this->request->data['CurrentInventory']['status'] == 2){
						$this->CurrentInventory->id = $current_inventory_val;
						$data['CurrentInventory']['qty'] = $current_qty + $adjust_qty;
					}elseif($this->request->data['CurrentInventory']['status'] == 1){
						if($current_qty < $adjust_qty)
						{
							$this->Session->setFlash(__('Adjustment quantity is gretter than current stock'), 'flash/warning');
							$this->redirect(array('action' => 'view/'.$this->request->data['CurrentInventory']['inventory_adjustment_id']));
						}
						$this->CurrentInventory->id = $current_inventory_val;
						$data['CurrentInventory']['qty'] = $current_qty - $adjust_qty;
						
					}
					// $data['transaction_date'] = $this->current_date();
					if($this->CurrentInventory->save($data)){
						$updated = 'yes';
					}
				}
				if($updated == 'yes')
				{*/
					$this->InventoryAdjustment->id = $this->request->data['CurrentInventory']['inventory_adjustment_id'];
					$adjust_data['InventoryAdjustment']['acknowledge_status'] = 1;

					
					if($this->InventoryAdjustment->save($adjust_data))
					{
						$this->Session->setFlash(__('The Inventory Adjustment has been approved'), 'flash/success');
						$this->redirect(array('action' => 'index/'.$this->request->data['CurrentInventory']['inventory_adjustment_id']));
					}
				//}
			}
		}
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Inventory adjustment');
		
		$this->loadModel('TransactionType');
		$transaction_types = $this->TransactionType->find('list', array('conditions'=>array('TransactionType.adjust'=>1,'TransactionType.active_status'=>1)));
		$transaction_typeid_with_inout = $this->TransactionType->find('list', array('conditions'=>array('TransactionType.adjust'=>1),'fields'=>array('id','inout')));

		
		if ($this->request->is('post')) 
		{
			if (empty($this->request->data['product_id'])) 
			{
                $this->Session->setFlash(__('Inventory Adjustment not created.'), 'flash/error');
                $this->redirect(array('action' => 'add'));
            } 
			$this->InventoryAdjustment->create();
			$this->request->data['InventoryAdjustment']['store_id'] = $this->UserAuth->getStoreId();
			
			$this->request->data['InventoryAdjustment']['transaction_type_id'] = $this->request->data['InventoryAdjustment']['status'];
			
			$transactions = $this->TransactionType->find('first', 
				array(
				'conditions'=>array('TransactionType.id'=>$this->request->data['InventoryAdjustment']['status']),
				'recursive' => -1
				)
			);
			
			$this->request->data['InventoryAdjustment']['status'] = $transactions['TransactionType']['inout'];
			$this->request->data['InventoryAdjustment']['approval_status'] = 1;
			
			$this->request->data['InventoryAdjustment']['remarks'] = $this->request->data['InventoryAdjustment']['remarks'];
			$this->request->data['InventoryAdjustment']['created_at'] = $this->current_datetime();
			$this->request->data['InventoryAdjustment']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->InventoryAdjustment->save($this->request->data)) {
				if(!empty($this->request->data['product_id'])){
					$data_array = array();
					$stock_update_array=array();
					foreach($this->request->data['product_id'] as $key=>$val)
					{
						if($this->request->data['expire_date'][$key] !='null' && trim($this->request->data['expire_date'][$key])!=''){
							$CurrentInventory = $this->CurrentInventory->find('first',array(
								'fields' => array('CurrentInventory.id','CurrentInventory.qty'),
								'conditions' => array(
									'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
									'CurrentInventory.product_id' => $val,
									'CurrentInventory.inventory_status_id' => $this->request->data['inventory_status_id'][$key],
									'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
									'CurrentInventory.expire_date' => ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null)
								)
							));
						}
						else {
								$CurrentInventory = $this->CurrentInventory->find('first',array(
								'fields' => array('CurrentInventory.id','CurrentInventory.qty'),
								'conditions' => array(
									'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
									'CurrentInventory.product_id' => $val,
									'CurrentInventory.inventory_status_id' => $this->request->data['inventory_status_id'][$key],
									'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key]
								)
							));
						}
						
						//adjustment
						
						/* ---- Inventory Adjustment details data : Start ---- */
						$data['InventoryAdjustmentDetail']['inventory_adjustment_id'] = $this->InventoryAdjustment->id;
						$data['InventoryAdjustmentDetail']['current_inventory_id'] = $CurrentInventory['CurrentInventory']['id'];
						$data['InventoryAdjustmentDetail']['quantity'] = $this->request->data['quantity'][$key];
						$data_array[] = $data;	
						/* ---- Inventory Adjustment details data : END ---- */

						/*----------------- Stock update : Start ---------------------------*/

						$current_qty = $CurrentInventory['CurrentInventory']['qty'];
						$current_inventory_val = $CurrentInventory['CurrentInventory']['id'];
						$adjust_qty = $this->request->data['quantity'][$key];
						if($transactions['TransactionType']['inout'] == 2)
						{
							/*$this->CurrentInventory->id = $current_inventory_val;
							$data['CurrentInventory']['qty'] = $current_qty + $adjust_qty;*/

							$inventory_data['CurrentInventory']['id']=$current_inventory_val;
							$inventory_data['CurrentInventory']['qty']= $current_qty + $adjust_qty;
						}
						elseif($transactions['TransactionType']['inout'] == 1)
						{
							if($current_qty < $adjust_qty)
							{
								$this->InventoryAdjustment->delete($this->InventoryAdjustment->id);
								$this->Session->setFlash(__('Adjustment quantity is gretter than current stock'), 'flash/warning');
								$this->redirect(array('action' => 'index'));
							}
							/*$this->CurrentInventory->id = $current_inventory_val;
							$data['CurrentInventory']['qty'] = $current_qty - $adjust_qty;*/

							$inventory_data['CurrentInventory']['id']=$current_inventory_val;
							$inventory_data['CurrentInventory']['qty']= $current_qty - $adjust_qty;

						}
						$inventory_data['CurrentInventory']['transaction_type_id']=$transactions['TransactionType']['id'];
						$inventory_data['CurrentInventory']['transaction_date'] = $this->current_date();
						$stock_update_array[]=$inventory_data;
						unset($inventory_data);
						/*----------------- Stock update : Start ---------------------------*/					
					}								
					// insert Inventory Adjustment Detail data
					$this->InventoryAdjustmentDetail->saveAll($data_array); 					
					$this->CurrentInventory->saveAll($stock_update_array); 					
				}
				$this->Session->setFlash(__('The inventory adjustment has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		}
			
		
		// get store's invntory product list
		$products = $this->CurrentInventory->find('list',array(
			'fields' => array('Product.id','Product.name'),
			'conditions' => array(
				'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
			),
			//'group' => array('CurrentInventory.product_id'),
			'order' => array('Product.order' => 'asc'),
			'recursive' => 0
		));
		
		$this->set(compact('products','transaction_types','transaction_typeid_with_inout'));		
	}
	public function get_inventory_details() {
		$product_id = $this->request->data['product_id'];

		$conditions_options['CurrentInventory.store_id'] = $this->UserAuth->getStoreId();
		$conditions_options['CurrentInventory.product_id'] = $product_id;
		if($this->request->data['batch_no']  && $this->request->data['expire_date']){
			$conditions_options['CurrentInventory.batch_number'] = ($this->request->data['batch_no'] ? $this->request->data['batch_no'] : NULL );
			//$p_expiredate = $this->request->data['expire_date'];
			//$conditions_options["(CurrentInventory.expire_date is null or expire_date = '$p_expiredate' "]=false;

			$conditions_options['CurrentInventory.expire_date'] = (!empty($this->request->data['expire_date'])&& $this->request->data['null'] ? $this->request->data['expire_date'] : NULL );

		}

		if (!empty($this->request->data['transaction_type_id'])) {
			$conditions_options['CurrentInventory.transaction_type_id'] = $this->request->data['transaction_type_id'];
		}
		if (!empty($this->request->data['inventory_status_id'])) {
			$conditions_options['CurrentInventory.inventory_status_id'] = $this->request->data['inventory_status_id'];
		}

		$batch_info = $this->CurrentInventory->find('first', array(
			'fields' => array('CurrentInventory.qty', 'Product.challan_measurement_unit_id'),
			'conditions' => array($conditions_options),
			'recursive' => 0
			));

		if (!empty($batch_info)) {
			echo $batch_info['CurrentInventory']['qty'];
		} else {
			echo '';
		}
		$this->autoRender = false;
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
		$this->InventoryAdjustment->id = $id;
		if (!$this->InventoryAdjustment->exists()) {
			throw new NotFoundException(__('Invalid inventory adjustment'));
		}
		if ($this->InventoryAdjustment->delete()) {
			$this->Session->setFlash(__('Inventory adjustment deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Inventory adjustment was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
