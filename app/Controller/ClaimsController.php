<?php
App::uses('AppController', 'Controller');
/**
 * Challans Controller
 *
 * @property ReturnChallan $Claim
 * @property PaginatorComponent $Paginator
 */
class ClaimsController extends AppController {

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
		$this->Claim->recursive = 0;
		if($this->UserAuth->getOfficeParentId() == 0)
		{
			$conditions = array(
				'Claim.transaction_type_id' => 4,
				//'ReturnChallan.inventory_status_id' => 1,
			);
		}else{
			$conditions = array(
				'Claim.transaction_type_id' => 4,
			//	'ReturnChallan.inventory_status_id' => 1,
				'Claim.sender_store_id' => $this->UserAuth->getStoreId()
			);
		}			
		$this->paginate = array(
			'conditions' => $conditions,
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
		
		$this->set('page_title','Claim Details');
		if (!$this->Claim->exists($id)) {
			throw new NotFoundException(__('Invalid Claim'));
		}
		
		if ($this->request->is('post')) {			
			// update claim status
			$claim['id'] = $id;
			$claim['status'] = 2;
			$claim['received_date'] = date('Y-m-d',strtotime($this->request->data['Claim']['received_date']));
			$claim['updated_at'] = $this->current_datetime();
			$claim['updated_by'] = $this->UserAuth->getUserId();
			$this->Claim->save($claim);
			
			$this->Session->setFlash(__('Claim has been received.'), 'flash/success');
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
			'order' => array('Product.order'=>'asc')
			//'recursive' => 0
			)
		);		
		
		$office_paren_id = $this->UserAuth->getOfficeParentId();
		$this->set(compact('claim','claimdetail','office_paren_id'));
	}
	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null) {

		$this->set('page_title','Claim Details');
		$this->loadModel('ChallanDetail');
		$this->loadModel('Challan');
		if (!$this->Claim->exists($id)) {
			throw new NotFoundException(__('Invalid Claim'));
		}


		if ($this->request->is('post')) {

			if(empty($this->request->data['product_id']))
			{
				$this->Session->setFlash(__('Claim not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}else{



				$this->ClaimDetail->create();


					//$udata['id'] = $this->Claim->id;
					//$udata['claim_no'] = 'CLM'.(10000 + $this->Claim->id);
					//$this->Claim->save($udata);
					if(!empty($this->request->data['product_id'])){
						$data_array = array();

						foreach($this->request->data['product_id'] as $key=>$val)
						{
							$data['ClaimDetail']['claim_id'] =$this->request->data['Claim']['claim_id'];
							$data['ClaimDetail']['product_id'] = $val;
							$data['ClaimDetail']['claim_qty'] = $this->request->data['claim_qty'][$key];
							$data['ClaimDetail']['batch_no'] = ($this->request->data['batch_no'][$key] != '' ?$this->request->data['batch_no'][$key] : Null);
							$data['ClaimDetail']['expire_date'] = ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null);
							$data['ClaimDetail']['claim_type'] = $this->request->data['claim_type'][$key];
							$data_array[] = $data;

						}

						$this->ClaimDetail->deleteAll(array('ClaimDetail.claim_id' => $this->request->data['Claim']['claim_id']), false);
						// insert challan data
						$this->ClaimDetail->saveAll($data_array);




					$this->Session->setFlash(__('Claim has been Submitted.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
			}
		}
		$options = array(
			'conditions' => array('Claim.' . $this->Claim->primaryKey => $id),
			'recursive' => 0
		);
		$claim = $this->Claim->find('first', $options);

		$claimdetail = $this->ClaimDetail->find('all', array(
			'conditions' => array('ClaimDetail.claim_id' => $claim['Claim']['id']),
			'fields' => 'ClaimDetail.*,Product.product_code,Product.name,Product.id',
			'order' => array('Product.order'=>'asc'),
			'recursive' => 2
			)
		);

		$challanDetails = $this->ChallanDetail->find('all', array(
				'conditions' => array('ChallanDetail.challan_id' => $claim['Claim']['challan_id']),
				'fields' => 'Challan.challan_no,Product.product_code,Product.name,Product.id',
				
				'recursive' => 0
			)
		);
		
		foreach($challanDetails as $challanDetail)
		{
			$products[$challanDetail['Product']['id']]=$challanDetail['Product']['name'];
		}

		//$products = $this->Product->find('list',array('order' => array('name'=>'asc')));
		$claimType=array('0'=>'Short','1'=>'Excess');
		//$this->data['Leaf']['tree_id'] = $id;
		$office_paren_id = $this->UserAuth->getOfficeParentId();
		$this->set(compact('claim','claimdetail','office_paren_id','products','claimType'));
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
			
				$this->request->data['Claim']['transaction_type_id'] = 31;
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
			'conditions' => array('store_type_id' => 1),
			'order' => array('name'=>'asc')
		));
		$claimType=array('0'=>'Short','1'=>'Excess');
		$this->loadModel('Challan');
		$challans = $this->Challan->find('list',array(
			'conditions' => array('Challan.inventory_status_id' => 1,'Challan.transaction_type_id' => 1,'Challan.receiver_store_id' => $this->UserAuth->getStoreId()),
			'fields' => array('Challan.id','Challan.challan_no'),
			'order' => array('Challan.id' => 'DESC'),
			'recursive' => -1
		));
		
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

	///////////for drop down making

	public function get_challan_detail_list(){
		$this->loadModel('ChallanDetail');
		$rs = array(array('id' => '', 'title' => '---- Select Batch -----'));


		$challanDetail = $this->ChallanDetail->find('all', array(
				'conditions' => array('ChallanDetail.challan_id' => $this->request->data['challan_id']),
				'fields' => array('ChallanDetail.product_id as id', 'Product.name as title'),
				'order' => array('Product.order'=>'asc'),
				'recursive' => 0
			)
		);


		$data_array = Set::extract($challanDetail, '{n}.0');
		/* echo "<pre>";
		print_r($challanDetail);
		print_r($data_array);
		exit;*/
		if(!empty($challanDetail)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}


	public function get_challan_wise_batch_list() {
		$this->loadModel('ChallanDetail');
		$rs = array(array('id' => '', 'title' => '---- Select Batch -----'));


		$batchList = $this->ChallanDetail->find('all', array(
				'conditions' => array('ChallanDetail.challan_id' =>$this->request->data['challan_id'],
										'ChallanDetail.product_id' =>$this->request->data['product_id'],
									),
				'fields' => array('ChallanDetail.batch_no as id', 'ChallanDetail.batch_no as title','ChallanDetail.challan_qty as challan_qty'),
				'recursive' => 0
			)
		);



		$data_array = Set::extract($batchList, '{n}.0');
		/* echo "<pre>";
		print_r($batchList);
		print_r($data_array);
		exit;*/
		if(!empty($batchList)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	public function get_challan_wise_exp_date_list()
	{
		$this->loadModel('ChallanDetail');
		$rs = array(array('id' => '', 'title' => '---- Select Expire Date ----'));


		$batchList = $this->ChallanDetail->find('all', array(
				'conditions' => array('ChallanDetail.challan_id' =>$this->request->data['challan_id'],
										'ChallanDetail.product_id' =>$this->request->data['product_id'],
										'ChallanDetail.batch_no' =>$this->request->data['batch_no'],
									),
				'fields' => array('ChallanDetail.expire_date as id', 'ChallanDetail.expire_date as title'),
				'recursive' => 0
			)
		);



		$data_array = Set::extract($batchList, '{n}.0');
		/* echo "<pre>";
		print_r($batchList);
		print_r($data_array);
		exit;*/
		if(!empty($batchList)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	public function get_challan_wise_pro_qty_list()
	{
		$this->loadModel('ChallanDetail');



		$challanQuantity = $this->ChallanDetail->find('all', array(
				'conditions' => array('ChallanDetail.challan_id' =>$this->request->data['challan_id'],
										'ChallanDetail.product_id' =>$this->request->data['product_id'],
										'ChallanDetail.batch_no' =>$this->request->data['batch_no'],
										'ChallanDetail.expire_date' =>$this->request->data['expire_date'],
									),
				'fields' => array('ChallanDetail.challan_qty'),
				'recursive' => 0
			)
		);

		//$data_array = Set::extract($challanQuantity, '{n}.ChallanDetail.challan_qty');

		echo $challanQuantity[0]['ChallanDetail']['challan_qty'];

		$this->autoRender = false;
	}
}
