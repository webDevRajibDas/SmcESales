<?php
App::uses('AppController', 'Controller');
/**
 * Challans Controller
 *
 * @property Challan $Challan
 * @property PaginatorComponent $Paginator
 */
class DistNcpProductIssuesController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Filter.Filter');
	public $uses = array('Challan', 'ChallanDetail', 'Store', 'Product', 'Territory', 'CurrentInventory');
	/**
	 * admin_index method
	 *
	 * @return void
	*/
	public $ncp_trsaction_code = array(
		'transaction_type_id'	=> 37,
		'inventory_status_id'	=> 2
	);
	public function admin_index()
	{
		$this->set('page_title', 'DistNCP Product Issue List');
		$this->Challan->recursive = 0;
		
		if ($this->UserAuth->getOfficeParentId() != 0) {
			$wh_sender_store_id 	= array(
				'Challan.sender_store_id' => $this->UserAuth->getStoreId()
			);
		}
		$conditions = array(
			'Challan.transaction_type_id' => $this->ncp_trsaction_code['transaction_type_id'],
			'Challan.inventory_status_id' => $this->ncp_trsaction_code['inventory_status_id'],
			$wh_sender_store_id
		);
		$this->paginate = array(
			'conditions' => $conditions,
			'recursive' => 0,
			'order' => array('Challan.id' => 'desc')
		);
		$this->set('challans', $this->paginate());
	}

	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null)
	{

		$this->set('page_title', 'Product Issue Details');
		if (!$this->Challan->exists($id)) {
			throw new NotFoundException(__('Invalid challan'));
		}

		$options = array(
			'conditions' => array('Challan.' . $this->Challan->primaryKey => $id),
			'recursive' => 0
		);
		$challan = $this->Challan->find('first', $options);
		$challandetail = $this->ChallanDetail->find(
			'all',
			array(
				'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
				'fields' => 'ChallanDetail.*,Product.product_code,Product.name,MeasurementUnit.name',
				'order' => array('Product.order' => 'asc'),
				'recursive' => 0
			)
		);
		$this->set(compact('challan', 'challandetail'));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->set('page_title', 'New Product Issue');

		$this->loadModel('DistDistributor');
		$this->loadModel('ReturnChallan');
		$this->loadModel('ReturnChallanDetail');


		if ($this->request->is('post')) {

			if (empty($this->request->data['product_id'])) {
				$this->Session->setFlash(__('NCP product issue not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			} else {

				$this->Challan->validator()->remove('challan_referance_no', 'isUnique');
				 
				$this->request->data['Challan']['transaction_type_id'] = $this->ncp_trsaction_code['transaction_type_id'];  //17=ASO to SO (NCP Return)
				$this->request->data['Challan']['inventory_status_id'] = $this->ncp_trsaction_code['inventory_status_id'];  //NCP Inventory
				$this->request->data['Challan']['challan_date'] = $this->current_date();
				$this->request->data['Challan']['challan_referance_no'] = $this->request->data['Challan']['challan_id'];
				$this->request->data['Challan']['sender_store_id'] = $this->UserAuth->getStoreId();
				$this->request->data['Challan']['created_at'] = $this->current_datetime();
				$this->request->data['Challan']['updated_at'] = $this->current_datetime();
				$this->request->data['Challan']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['Challan']['status'] = 0;
				$this->Challan->create();
				if ($this->Challan->save($this->request->data)) {

					$udata['id'] = $this->Challan->id;
					$udata['challan_no'] = 'DNCPI-' . (10000 + $this->Challan->id);
					$this->Challan->save($udata);
					if (!empty($this->request->data['product_id'])) {
						$data_array = array();
						foreach ($this->request->data['product_id'] as $key => $val) {
							$data['ChallanDetail']['challan_id'] = $this->Challan->id;
							$data['ChallanDetail']['product_id'] = $val;
							$data['ChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
							$data['ChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
							$data['ChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
							$data['ChallanDetail']['expire_date'] = ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
							$data['ChallanDetail']['inventory_status_id'] = $this->ncp_trsaction_code['inventory_status_id'];
							$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
							$data_array[] = $data;
						}
						
						$this->ChallanDetail->saveAll($data_array);

					}


					$this->Session->setFlash(__('DB NCP Product issue has been drafted.'), 'flash/success');
					$this->redirect(array('action' => 'edit', $this->Challan->id));
				}
			}
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $designation_id = $this->Session->read('Office.designation_id');
		
        
		if ($office_parent_id != 0) {

			$distdistributors = $this->DistDistributor->find('list', array(
				'conditions' => array(
					'DistDistributor.office_id' => $this->UserAuth->getOfficeId(),
					'DistDistributor.is_active' => 1
				),
				'joins'=>array(
					array(
						'alias'=>'DistStore',
						'table'=>'dist_stores',
						'type'=>'left',
						'conditions'=>'DistStore.dist_distributor_id=DistDistributor.id'
					)
				),
				'fields'=>array('DistStore.id', 'DistDistributor.name'),
				'recursive'=>-1
			));

        }
		
		$this->set(compact('distdistributors'));

	}


	/**
	 * admin_delete method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null)
	{
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
	 * admin_edit
	 */

	public function admin_edit($id = null)
	{
		$this->set('page_title', 'Edit Product Issue');

		
		$this->loadModel('ChallanDetail');
		$this->loadModel('DistDistributor');
		$this->loadModel('DistReturnChallan');
		$this->loadModel('DistReturnChallanDetail');

		$ncp_challan_id = $id;


		$ncp_challan_draft_info = $this->Challan->find('all', array(
			'conditions' => array('Challan.id' => $ncp_challan_id),
			'recursive' => -1
		));

		$details = $this->ChallanDetail->find('all', array(
			'conditions' => array('ChallanDetail.challan_id' => $ncp_challan_id),
			'joins'=>array(
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'left',
					'conditions' => 'Product.id = ChallanDetail.product_id'
				), 
				array(
					'alias' => 'VirtualProduct',
					'table' => 'products',
					'type' => 'left',
					'conditions' => 'VirtualProduct.id = ChallanDetail.virtual_product_id'
				),
				array(
					'alias' => 'MeasurementUnit',
					'table' => 'measurement_units',
					'type' => 'left',
					'conditions' => 'MeasurementUnit.id = ChallanDetail.measurement_unit_id'
				),
			),
			'fields'=>array('ChallanDetail.*', 'Product.name', 'VirtualProduct.name', 'MeasurementUnit.name'),
			'recursive' => -1
		));


		$ncp_challan_draft_info[0]['ChallanDetail'] = $details;

		$challan_list_array = $this->DistReturnChallan->find('all', array(
			'fields' => array('DistReturnChallan.id as id', 'DistReturnChallan.challan_no as title'),
			'conditions' => array(
				'DistReturnChallan.is_close' => NULL,
				'DistReturnChallan.transaction_type_id' => $this->ncp_trsaction_code['transaction_type_id'],
				'DistReturnChallan.inventory_status_id' => $this->ncp_trsaction_code['inventory_status_id'],
				'DistReturnChallan.receiver_store_id' => $this->UserAuth->getStoreId(),
				'DistReturnChallan.sender_store_id' => $ncp_challan_draft_info[0]['Challan']['receiver_store_id']
			),
			'recursive' => -1
		));

		foreach ($challan_list_array as $value) {
			$challan_list[$value[0]['id']] = $value[0]['title'];
		}

		$product_list_array = $this->DistReturnChallanDetail->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'conditions' => array(
				'DistReturnChallanDetail.challan_id' => $ncp_challan_draft_info[0]['Challan']['challan_referance_no']
			),
			'joins'=>array(
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'left',
					'conditions' => 'Product.id = DistReturnChallanDetail.product_id'
				), 
			),
			'recursive' => 0
		));

		foreach ($product_list_array as $value) {
			$product_list[$value['Product']['id']] = $value['Product']['name'];
		}

		foreach ($ncp_challan_draft_info[0]['ChallanDetail'] as $key => $value) {
			$current_inventory_info = $this->CurrentInventory->find('first', array(
				'conditions' => array(
					'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
					'CurrentInventory.product_id' => $value['ChallanDetail']['product_id'],
					'CurrentInventory.batch_number' => $value['ChallanDetail']['batch_no'],
					'CurrentInventory.expire_date' => $value['ChallanDetail']['expire_date'],
					'CurrentInventory.inventory_status_id' => $this->ncp_trsaction_code['inventory_status_id']
				),
				'recursive' => -1
			));
			//$ncp_challan_draft_info[0]['ChallanDetail'][$key]['stock_qty'] = $this->unit_convertfrombase($value['product_id'],$value['Product']['challan_measurement_unit_id'],$current_inventory_info['CurrentInventory']['qty']);
			$ncp_challan_draft_info[0]['ChallanDetail'][$key]['ChallanDetail']['stock_qty'] = $current_inventory_info['CurrentInventory']['qty'];
		}
		//pr($product_list);die();

		if ($this->request->is('post')) {

			$this->ChallanDetail->deleteAll(array('ChallanDetail.challan_id' => $ncp_challan_id));

			if (empty($this->request->data['product_id'])) {
				$this->Session->setFlash(__('NCP product issue not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			} else {

				$this->Challan->validator()->remove('challan_referance_no', 'isUnique');

				$this->request->data['Challan']['transaction_type_id'] = $this->ncp_trsaction_code['transaction_type_id'];  //17=ASO to SO (NCP Return)
				$this->request->data['Challan']['inventory_status_id'] = $this->ncp_trsaction_code['inventory_status_id'];  //NCP Inventory
				$this->request->data['Challan']['challan_date'] = $this->current_date();
				$this->request->data['Challan']['challan_referance_no'] = $this->request->data['Challan']['challan_id'];
				$this->request->data['Challan']['sender_store_id'] = $this->UserAuth->getStoreId();
				$this->request->data['Challan']['created_at'] = $this->current_datetime();
				$this->request->data['Challan']['updated_at'] = $this->current_datetime();
				$this->request->data['Challan']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['Challan']['id'] = $ncp_challan_id;
				if (array_key_exists('draft', $this->request->data)) {
					$this->request->data['Challan']['status'] = 0;
				} else {
					$this->request->data['Challan']['status'] = 1;
				}

				if ($this->Challan->save($this->request->data)) {

					$udata['id'] = $ncp_challan_id;
					$udata['challan_no'] = 'DBNCPPI' . (10000 + $ncp_challan_id);
					$this->Challan->save($udata);
					if (!empty($this->request->data['product_id'])) {
						$data_array = array();
						foreach ($this->request->data['product_id'] as $key => $val) {
							$data['ChallanDetail']['challan_id'] = $ncp_challan_id;
							$data['ChallanDetail']['product_id'] = $val;
							$data['ChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
							$data['ChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
							$data['ChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
							$data['ChallanDetail']['expire_date'] = ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
							$data['ChallanDetail']['inventory_status_id'] = $this->ncp_trsaction_code['inventory_status_id'];
							$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
							$data_array[] = $data;

							// ------------ stock update --------------------	

							$inventory_info = $this->CurrentInventory->find('first', array(
								'conditions' => array(
									'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
									'CurrentInventory.inventory_status_id' => $this->ncp_trsaction_code['inventory_status_id'],
									'CurrentInventory.product_id' => $val,
									'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
									'CurrentInventory.expire_date' => ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null)
								),
								'recursive' => -1
							));

							$deduct_quantity = $this->request->data['quantity'][$key];

							$update_data['id'] = $inventory_info['CurrentInventory']['id'];
							$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] - $deduct_quantity;
							$update_data['transaction_type_id'] = $this->ncp_trsaction_code['transaction_type_id'];
							$update_data['transaction_date'] = $this->current_date();

							$update_data_array[] = $update_data;

							// ------------ ReturnChallanDetail  update --------------------			
							$challan_info = $this->DistReturnChallanDetail->find('all', array(
								'conditions' => array(
									'DistReturnChallanDetail.challan_id' => $this->request->data['Challan']['challan_id'],
									'DistReturnChallanDetail.product_id' => $val
								),
								'recursive' => -1
							));
							$challan_qty_for_returnchallan_update = $this->request->data['quantity'][$key];
							foreach ($challan_info as $re_ch_info) {
								$update_challan_data['id'] = $re_ch_info['DistReturnChallanDetail']['id'];
								if ($re_ch_info['DistReturnChallanDetail']['remaining_qty'] < $challan_qty_for_returnchallan_update) {
									$update_challan_data['remaining_qty'] = 0;
									$challan_qty_for_returnchallan_update = $challan_qty_for_returnchallan_update - $re_ch_info['DistReturnChallanDetail']['remaining_qty'];
								} else {
									$update_challan_data['remaining_qty'] = $re_ch_info['DistReturnChallanDetail']['remaining_qty'] - $challan_qty_for_returnchallan_update;
									$challan_qty_for_returnchallan_update = $re_ch_info['DistReturnChallanDetail']['remaining_qty'] - $challan_qty_for_returnchallan_update;
								}
								$update_challan_data_array[] = $update_challan_data;
							}
						}
						// insert challan data
						$this->ChallanDetail->saveAll($data_array);

						if (array_key_exists('save', $this->request->data)) {
							// Update inventory data
							$this->CurrentInventory->saveAll($update_data_array);

							// Update Chalan quantity data
							$this->DistReturnChallanDetail->saveAll($update_challan_data_array);


							/*****************Create Chalan and *****************/
                            $this->loadModel('DistChallan');
                            $this->loadModel('DistChallanDetail');
                            $this->loadModel('Territory');

							$challaninfo = $this->Challan->find('first', array(
								'conditions' => array('Challan.id' => $ncp_challan_id),
								'recursive' => -1
							));
					
							$challanDetails = $this->ChallanDetail->find('all', array(
								'conditions' => array('ChallanDetail.challan_id' => $ncp_challan_id),
								'fields'=>array('ChallanDetail.*'),
								'recursive' => -1
							));

							$dbinfo = $this->DistDistributor->find('first', array(
								'conditions' => array(
									'DistStore.id' => $challaninfo['Challan']['receiver_store_id']
								),
								'joins'=>array(
									array(
										'alias'=>'DistStore',
										'table'=>'dist_stores',
										'type'=>'left',
										'conditions'=>'DistStore.dist_distributor_id=DistDistributor.id'
									)
								),
								'fields'=>array('DistDistributor.id', 'DistDistributor.office_id'),
								'recursive'=>-1
							));

							$territory_info=$this->Territory->find('first',array(
								'conditions'=> array(
									'Territory.office_id'=>$dbinfo['DistDistributor']['office_id'],
									'Territory.name LIKE'=> '%Corporate Territory%',
								),
							));
                           
                            $challan['office_id'] = $dbinfo['DistDistributor']['office_id'];
                            $challan['memo_id'] = 0;
                            $challan['memo_no'] = 0;
                            $challan['challan_no'] = $challaninfo['Challan']['challan_no'];
                            $challan['receiver_dist_store_id'] = $challaninfo['Challan']['receiver_store_id'];
                            $challan['receiving_transaction_type'] = 2;
                            $challan['received_date'] = '';
                            $challan['challan_date'] = $challaninfo['Challan']['challan_date'];
                            $challan['dist_distributor_id'] = $dbinfo['DistDistributor']['id'];
                            $challan['challan_referance_no'] = '';
                            $challan['challan_type'] = "";
                            $challan['remarks'] = $challaninfo['Challan']['remarks'];
                            $challan['status'] = 1;
                            $challan['so_id'] = $territory_info['SalesPerson']['id'];
                            $challan['is_close'] = 0;
                            $challan['inventory_status_id'] = $this->ncp_trsaction_code['inventory_status_id'];
                            $challan['transaction_type_id'] = $this->ncp_trsaction_code['transaction_type_id'];
                            $challan['sender_store_id'] = $challaninfo['Challan']['sender_store_id'];
                            $challan['created_at'] = $this->current_datetime();
                            $challan['created_by'] = $this->UserAuth->getUserId();
                            $challan['updated_at'] = $this->current_datetime();
                            $challan['updated_by'] = $this->UserAuth->getUserId();
                            
                            $this->DistChallan->create();
                            
                            if ($this->DistChallan->save($challan)) {

                                $dist_challan_id = $this->DistChallan->getLastInsertId();
                               
								$data_array = array();

								foreach ($challanDetails as $key => $val) {

									$v = $val['ChallanDetail'];
												
									$data['DistChallanDetail']['challan_id'] = $dist_challan_id;
									$data['DistChallanDetail']['product_id'] = $v['product_id'];
									$data['DistChallanDetail']['measurement_unit_id'] = $v['measurement_unit_id'];
									$data['DistChallanDetail']['challan_qty'] = $v['challan_qty'];
									$data['DistChallanDetail']['received_qty'] = $v['challan_qty'];
									$data['DistChallanDetail']['batch_no'] = $v['batch_no'];
									$data['DistChallanDetail']['price'] = 0;
									$data['DistChallanDetail']['is_bonus'] = 0;
									$data['DistChallanDetail']['source'] = "";
									$data['DistChallanDetail']['remarks'] = '';
									$data['DistChallanDetail']['expire_date'] = $v['expire_date'];
									$data['DistChallanDetail']['inventory_status_id'] = $this->ncp_trsaction_code['inventory_status_id'];  
									//$data['DistChallanDetail']['virtual_product_id'] = $v['virtual_product_id'];  
											
									$data_array[] = $data;
									
								}
								
								$this->DistChallanDetail->saveAll($data_array);
                                
                            }
                        

                        	/************* end Challan *************/

						}
					}


					// Challan close
					if (array_key_exists('save', $this->request->data)) {
						$do_udata['id'] = $this->request->data['Challan']['challan_id'];
						$do_udata['is_close'] = ($this->request->data['Challan']['is_close'] == 1 ? $this->request->data['Challan']['is_close'] : NULL);
						//$this->DistReturnChallan->save($do_udata);
					}

					$this->Session->setFlash(__('Product issue has been created.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
			}
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();
    

		if ($office_parent_id != 0) {

			$distdistributors = $this->DistDistributor->find('list', array(
				'conditions' => array(
					'DistDistributor.office_id' => $this->UserAuth->getOfficeId(),
					'DistDistributor.is_active' => 1
				),
				'joins'=>array(
					array(
						'alias'=>'DistStore',
						'table'=>'dist_stores',
						'type'=>'left',
						'conditions'=>'DistStore.dist_distributor_id=DistDistributor.id'
					)
				),
				'fields'=>array('DistStore.id', 'DistDistributor.name'),
				'recursive'=>-1
			));

        }
		
		$this->set(compact('distdistributors'));

		//echo '<pre>';print_r($ncp_challan_draft_info);exit;


		$this->set(compact('ncp_challan_draft_info', 'challan_list', 'product_list'));

	}

	/*----------------------- Chainbox Data ---------------------------*/

	public function get_challan_list()
	{
	
		$this->loadModel('DistReturnChallan');

		$dist_store_id = $this->request->data['receiver_store_id'];

		//echo $dist_store_id['DistStore']['id'];exit;

		$rs = array(array('id' => '', 'title' => '---- Select Challan -----'));

		$challan_list = $this->DistReturnChallan->find('all', array(
			'fields' => array('DistReturnChallan.id as id', 'DistReturnChallan.challan_no as title'),
			'conditions' => array(
				'DistReturnChallan.is_close' => NULL,
				'DistReturnChallan.transaction_type_id' => $this->ncp_trsaction_code['transaction_type_id'],
				'DistReturnChallan.inventory_status_id' => $this->ncp_trsaction_code['inventory_status_id'],
				'DistReturnChallan.receiver_store_id' => $this->UserAuth->getStoreId(),
				'DistReturnChallan.status' => 2,
				'DistReturnChallan.sender_store_id' => $dist_store_id
			),
			'recursive' => -1
		));
		//$this->dd($this->DistReturnChallan->getLastquery());
		$data_array = Set::extract($challan_list, '{n}.0');
		if (!empty($challan_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}


	public function get_challan_product_list()
	{
		$this->loadModel('DistReturnChallanDetail');
		$rs = array(array('id' => '', 'title' => '---- Select Product -----'));
		$challan_id = $this->request->data['challan_id'];
		$do_list = $this->DistReturnChallanDetail->find('all', array(
			'fields' => array('DISTINCT Product.id', 'Product.name'),
			'conditions' => array(
				'DistReturnChallanDetail.challan_id' => $challan_id
			),
			'joins'=>array(
				array(
					'alias'=>'Product',
					'table'=>'products',
					'type'=>'left',
					'conditions'=>'Product.id=DistReturnChallanDetail.product_id'
				)
			),
			/*'order' => array('Product.order'=>'asc'),*/
			'recursive' => -1
		));


		if (!empty($do_list)) {
			$array = array();
			foreach ($do_list as $val) {
				$data['id'] = $val['Product']['id'];
				$data['title'] = $val['Product']['name'];
				$array[] = $data;
			}
			echo json_encode(array_merge($rs, $array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}


	public function get_inventory_details()
	{
		$this->loadModel('DistReturnChallanDetail');
		$challan_id = $this->request->data['challan_id'];
		$product_id = $this->request->data['product_id'];
		$batch_no = ($this->request->data['batch_no'] != '' ? $this->request->data['batch_no'] : NULL);
		$expire_date = ($this->request->data['expire_date'] != '' ? $this->request->data['expire_date'] : NULL);
		$stock_info = $this->CurrentInventory->find('first', array(
			'fields' => array('CurrentInventory.qty'),
			'conditions' => array(
				'CurrentInventory.product_id' => $product_id,
				'CurrentInventory.batch_number' => $batch_no,
				'CurrentInventory.expire_date' => $expire_date,
				'CurrentInventory.inventory_status_id' => $this->ncp_trsaction_code['inventory_status_id'],
				//'CurrentInventory.transaction_type_id' => $this->ncp_trsaction_code['transaction_type_id'],
				'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
			),
			'recursive' => -1
		));

		$data['stock_quantity'] = $stock_info['CurrentInventory']['qty'];

		$challan_info = $this->DistReturnChallanDetail->find('first', array(
			'fields' => array('SUM(DistReturnChallanDetail.challan_qty) as challan_qty', 'SUM(DistReturnChallanDetail.remaining_qty) as remaining_qty'),
			'conditions' => array(
				'DistReturnChallanDetail.challan_id' => $challan_id,
				'DistReturnChallanDetail.product_id' => $product_id,
			),
			'group' => array('DistReturnChallanDetail.product_id'),
			'recursive' => -1
		));
		/*echo $this->ReturnChallanDetail->getLastQuery();
		pr($challan_info);exit;*/
		$data['qty'] = $challan_info['0']['challan_qty'];
		$data['remaining_qty'] = $challan_info['0']['remaining_qty'];
		echo json_encode($data);
		$this->autoRender = false;
	}
	public function get_batch_list()
	{
		$rs = array(array('id' => '', 'title' => '---- Select Batch -----'));
		$product_id = $this->request->data['product_id'];
		$inventory_status_id = $this->request->data['inventory_status_id'];
		if (isset($this->request->data['with_stock'])) {
			$with_stock = $this->request->data['with_stock'];
			$conditions[] = array('CurrentInventory.qty >' => 0);
		} else $with_stock = false;



		if (isset($this->request->data['transaction_type_id'])) {
			$conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
			// $conditions[] = array('CurrentInventory.transaction_type_id' => $this->request->data['transaction_type_id']);
			$conditions[] = array('CurrentInventory.product_id' => $product_id);
			$conditions[] = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId());
		} else {
			$conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
			$conditions[] = array('CurrentInventory.product_id' => $product_id);
			$conditions[] = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId());
			$conditions[] = array('CurrentInventory.qty >' => 0);
			// $conditions[] = array('Challan.transaction_type_id' => 16, 'Challan.status' => 2);
		}

		//$product_id = 12;
		$batch_list = $this->CurrentInventory->find('all', array(
			'fields' => array('CurrentInventory.batch_number as id', 'CurrentInventory.batch_number as title'),
			'conditions' => $conditions,

			'group' => array('CurrentInventory.batch_number'),
			'recursive' => -1
		));
		/* foreach($batch_list as ){

        } */

		$data_array = Set::extract($batch_list, '{n}.0');
		/* echo "<pre>";
          print_r($batch_list);
          print_r($data_array);
          exit; */
		if (!empty($batch_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}


	function get_db_list(){

        $tso_id = $this->request->data['tso_id'];
    
        $this->loadModel('DistTsoMapping');

        $output = "<option value=''>--- Select Distributor ---</option>";
        
		$dist_conditions = array(
			'DistTsoMapping.dist_tso_id' => $tso_id,
			'DistDistributor.is_active'=> 1
			
		);
        
        $distDistributors = $this->DistTsoMapping->find('all',array(
            'conditions'=>$dist_conditions,
            'fields'=>array('DistDistributor.id','DistDistributor.name'),
        ));
        if ($distDistributors) {
            foreach ($distDistributors as $key => $data) {
                $k = $data['DistDistributor']['id'];
                $v = $data['DistDistributor']['name'];
                $output .= "<option value='$k'>$v</option>";
            }
        }
        echo $output;
        $this->autoRender = false;
    
    }

	










}
