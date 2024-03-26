<?php
App::uses('AppController', 'Controller');
/**
 * Challans Controller
 *
 * @property ReturnChallan $Claim
 * @property PaginatorComponent $Paginator
 */
class ClaimsToAsoController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Filter.Filter');
	public $uses = array('Claim','Store','Product','ClaimDetail');
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Claim List');	
		if($this->UserAuth->getOfficeParentId() == 0)
		{
			$conditions=array(
			'AND'=>array(
				array(
				'OR'=>array(
					array('Claim.transaction_type_id' => 24),
					array('Claim.transaction_type_id' => 25)
				))
				)
				);
		}
		else
		{
			$conditions=array(
			'AND'=>array(
				array(
				'OR'=>array(
					array('Claim.transaction_type_id' => 24),
					array('Claim.transaction_type_id' => 25)
				)),
				array(
				'Claim.receiver_store_id' => $this->UserAuth->getStoreId()),
				)
				);
		}			
		$this->paginate = array(
			'conditions' =>$conditions ,
					
			'recursive' => 0,
			'order' => array('Claim.id' => 'desc')
		);			
		$this->set('Claims',$this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {	
		$this->LoadModel('CurrentInventory');	
		
		$this->set('page_title','Claim Details');
		if (!$this->Claim->exists($id)) {
			throw new NotFoundException(__('Invalid Claim'));
		}
		
		if ($this->request->is('post')) {			
			// update claim status
			$claim['id'] = $id;
			$claim['status'] = 2;
			$claim['transaction_type_id']=25; // 25= so to aso (claim received)
			$claim['received_date'] = date('Y-m-d',strtotime($this->request->data['Claim']['received_date']));
			$claim['updated_at'] = $this->current_datetime();
			$claim['updated_by'] = $this->UserAuth->getUserId();
			
			if($this->Claim->save($claim)){
				$claimdetail = $this->ClaimDetail->find('all', array(
					'conditions' => array('ClaimDetail.claim_id' => $id),
					'recursive' => -1
				));
				// stock update against calim
				$data_array=array();
				foreach($claimdetail as $data){
					$inventory=$this->CurrentInventory->find('first',array(
						'conditions'=>array('CurrentInventory.store_id'=>$this->UserAuth->getStoreId(),'CurrentInventory.product_id'=>$data['ClaimDetail']['product_id'],'CurrentInventory.batch_number'=>$data['ClaimDetail']['batch_no'],'CurrentInventory.expire_date'=>$data['ClaimDetail']['expire_date']),
						'recursive'=>-1
					));

					$sales_unit=0;
            		$sales_unit=$this->get_sales_unit_by_product_id($data['ClaimDetail']['product_id']);
            		$p_quantity= $this->unit_convert($data['ClaimDetail']['product_id'], $sales_unit,$data['ClaimDetail']['claim_qty']);
					$update_data=array();
					$update_data['id']=$inventory['CurrentInventory']['id'];
					if($data['ClaimDetail']['claim_type'] == 0)
					{
						$update_data['qty']=$inventory['CurrentInventory']['qty']+ $p_quantity;
					}
					else 
					{
						$update_data['qty']=$inventory['CurrentInventory']['qty']- $p_quantity;
					}
					$update_data['transaction_type_id'] = 25; // Claims (so to aso received)
					$update_data['transaction_date'] = date('Y-m-d',strtotime($this->request->data['Claim']['received_date']));	
					$data_array[]=$update_data;
				}
				if($this->CurrentInventory->saveAll($data_array)){
					$this->Session->setFlash(__('Claim has been received.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
				else{
					$this->Session->setFlash(__('Claim Not received.'), 'flash/error');
				}
			}else{
					$this->Session->setFlash(__('Claim Not received.'), 'flash/error');
				}			
		}
			
		$options = array(
			'conditions' => array('Claim.' . $this->Claim->primaryKey => $id),
			'recursive' => 0
		);
		$claim = $this->Claim->find('first', $options);

		$claimdetail = $this->ClaimDetail->find('all', array(
			'conditions' => array('ClaimDetail.claim_id' => $claim['Claim']['id']),
			'fields' => 'ClaimDetail.*,Product.product_code,Product.name',
			'recursive' => 0
			)
		);		
		
		$office_paren_id = $this->UserAuth->getOfficeParentId();
		$this->set(compact('claim','claimdetail','office_paren_id'));
	}


public function admin_view_for_approver($id = null) {		
		
		$this->set('page_title','Claim Details');
		if (!$this->Claim->exists($id)) {
			throw new NotFoundException(__('Invalid Claim'));
		}
		
		if ($this->request->is('post')) {			
			// update claim status
			$claim['id'] = $id;
			$claim['isApproved'] =1;
			$claim['updated_at'] = $this->current_datetime();
			$claim['updated_by'] = $this->UserAuth->getUserId();
			$this->Claim->save($claim);
			
			$this->Session->setFlash(__('Claim has been Approved.'), 'flash/success');
			$this->redirect(array('action' => 'index'));			
		}
			
		$options = array(
			'conditions' => array('Claim.' . $this->Claim->primaryKey => $id),
			'recursive' => 0
		);
		$claim = $this->Claim->find('first', $options);

		$claimdetail = $this->ClaimDetail->find('all', array(
			'conditions' => array('ClaimDetail.claim_id' => $claim['Claim']['id']),
			'fields' => 'ClaimDetail.*,Product.product_code,Product.name',
			'recursive' => 0
			)
		);	
			
		
		$office_paren_id = $this->UserAuth->getOfficeParentId();
		$this->set(compact('claim','claimdetail','office_paren_id'));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','New Claim');
		
		if ($this->request->is('post')) {
			
			if(empty($this->request->data['product_id']))
			{
				$this->Session->setFlash(__('Claim not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}else{
			
				$this->request->data['Claim']['transaction_type_id'] = 24;
				//$this->request->data['Claim']['inventory_status_id'] = 1;
				
				$this->request->data['Claim']['challan_date'] = $this->current_date();
				$this->request->data['Claim']['sender_store_id'] = $this->UserAuth->getStoreId();
				$this->request->data['Claim']['status'] = 1;
				$this->request->data['Claim']['created_at'] = $this->current_datetime();
				$this->request->data['Claim']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['Claim']['updated_at'] = $this->current_datetime();
				$this->request->data['Claim']['updated_by'] = $this->UserAuth->getUserId();

				$this->Claim->create();
				if ($this->Claim->save($this->request->data)) {
					
					$udata['id'] = $this->Claim->id;
					$udata['claim_no'] = 'CLM'.(10000 + $this->Claim->id);
					$this->Claim->save($udata);
					if(!empty($this->request->data['product_id'])){
						$data_array = array();

						foreach($this->request->data['product_id'] as $key=>$val)
						{
							$data['ClaimDetail']['claim_id'] = $this->Claim->id;
							$data['ClaimDetail']['product_id'] = $val;
							$data['ClaimDetail']['claim_qty'] = $this->request->data['claim_qty'][$key];
							$data['ClaimDetail']['batch_no'] = ($this->request->data['batch_no'][$key] != '' ?$this->request->data['batch_no'][$key] : Null);
							$data['ClaimDetail']['expire_date'] = ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null);
							$data['ClaimDetail']['claim_type'] = $this->request->data['claim_type'][$key];
							$data_array[] = $data;

						}

						// insert challan data
						$this->ClaimDetail->saveAll($data_array);

						
					}
					
					$this->Session->setFlash(__('Claim has been Submitted.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
			}
		}
					
		$receiverStore = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 1,'office_id' => $this->UserAuth->getOfficeParentId()),
			'order' => array('name'=>'asc')
		));
		$claimType=array('short'=>'Short','Excess'=>'Excess');
		$challans = $this->Claim->Challan->find('list',array('conditions'=>array('receiver_store_id'=>$this->UserAuth->getUserId())));
		//$products = $this->Product->find('list',array('order' => array('name'=>'asc')));
		$this->set(compact('receiverStore','challans','claimType'));
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
		$this->Claim->id = $id;
		if (!$this->Claim->exists()) {
			throw new NotFoundException(__('Invalid Claim'));
		}
		if ($this->Claim->delete()) {
			$this->flash(__('Claim deleted'), array('action' => 'index'));
		}
		$this->flash(__('Claim was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}

	public function get_sales_unit_by_product_id($id)
	{
		$this->loadModel('Product');
		$product=$this->Product->find('first',array(
			'conditions'=>array('Product.id'=>$id),
			'recursive'=>-1
			));
		return $product['Product']['sales_measurement_unit_id'];
	}

}
