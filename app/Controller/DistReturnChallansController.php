<?php
App::uses('AppController', 'Controller');
/**
 * Challans Controller
 *
 * @property DistReturnChallan $DistReturnChallan
 * @property PaginatorComponent $Paginator
 */
class DistReturnChallansController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Filter.Filter');
	public $uses = array(
		'DistReturnChallan', 'DistReturnChallanDetail', 'DistNcpCollection', 'DistNcpCollectionDetail',
		'DistStore', 'Store', 'Product', 'Territory', 'DistCurrentInventory', 'MeasurementUnit',
		'CurrentInventory', 'DistProductPrice', 'DistCurrentInventory'
	);
	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->set('page_title', 'Return Challan List');
		/*For DistStore Filtering : Start */


		$storeCondition = array('Store.store_type_id' => 2);
		$area_store_list = $this->Store->find(
			'list',
			array(
				'conditions' => $storeCondition,
				'joins' => array(
					array(
						'table' => 'offices',
						'alias' => 'Office',
						'type' => 'Inner',
						'conditions' => 'Office.id=Store.office_id'
					)
				),
				'fields' => array('Store.id', 'Store.name'),
				//'order' => array('Store.name' => 'asc'),
				'recursive' => -1
			)
		);
		//pr($area_store_list);exit;


		$this->loadModel('Office');
		$this->loadModel('DistStore');
		$this->loadModel('DistTso');
		$this->loadModel('DistTsoMapping');
		$this->loadModel('DistRouteMapping');
		$this->loadModel('DistAreaExecutive');

		$user_id = $this->UserAuth->getUserId();
		$user_group_id = $this->Session->read('UserAuth.UserGroup.id');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$this->set('office_parent_id', $office_parent_id);

		if ($office_parent_id == 0) {
			$conditions = array('Office.office_type_id' => 2);
		} else {
			$conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
		}

		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
		$this->set('offices', $offices);

		$this->DistReturnChallan->recursive = 0;
		if ($office_parent_id == 0) {
			$conditions = array();
		} else {
			if ($user_group_id == 1029 || $user_group_id == 1028) {
				if ($user_group_id == 1028) {
					$dist_ae_info = $this->DistAreaExecutive->find('first', array(
						'conditions' => array('DistAreaExecutive.user_id' => $user_id, 'DistAreaExecutive.is_active' => 1),
						'recursive' => -1,
					));
					$dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
					$dist_tso_info = $this->DistTso->find('list', array(
						'conditions' => array('dist_area_executive_id' => $dist_ae_id),
						'fields' => array('DistTso.id', 'DistTso.dist_area_executive_id'),
					));

					$dist_tso_id = array_keys($dist_tso_info);
				} else {
					$dist_tso_info = $this->DistTso->find('first', array(
						'conditions' => array('DistTso.user_id' => $user_id, 'DistTso.is_active' => 1),
						'recursive' => -1,
					));
					$dist_tso_id = $dist_tso_info['DistTso']['id'];
				}

				$tso_dist_list = $this->DistTsoMapping->find('list', array(
					'conditions' => array(
						'dist_tso_id' => $dist_tso_id,
					),
					'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
				));
				$dist_store_con = array('DistStore.dist_distributor_id' => array_keys($tso_dist_list));
				$dist_store_info = $this->DistStore->find('list', array(
					'conditions' => $dist_store_con,
					'recursive' => -1
				));
				$conditions = array('DistReturnChallan.sender_store_id' => array_keys($dist_store_info));
			} elseif ($user_group_id == 1034) {
				$sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
				$this->loadModel('DistUserMapping');
				$distributor = $this->DistUserMapping->find('first', array(
					'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
				));
				$distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
				$dist_store_con = array('DistStore.dist_distributor_id' => $distributor_id);
				$dist_store_info = $this->DistStore->find('first', array(
					'conditions' => $dist_store_con,
					'recursive' => -1
				));
				$dist_store_id = $dist_store_info['DistStore']['id'];
				$conditions = array('DistReturnChallan.sender_store_id' => $dist_store_id);
			} else {
				$dist_store_con = array('DistStore.office_id' => $this->UserAuth->getOfficeId());
				$dist_store_info = $this->DistStore->find('list', array(
					'conditions' => $dist_store_con,
					'recursive' => -1
				));
				$conditions = array('DistReturnChallan.sender_store_id' => array_keys($dist_store_info));
			}
		}
		$this->paginate = array(
			'conditions' => $conditions,
			'recursive' => 0,
			'order' => array('DistReturnChallan.id' => 'desc')
		);

		//pr($this->paginate());exit;

		$this->loadModel('InventoryStatus');
		$inventoryStatus = $this->InventoryStatus->find('list', array(
			'conditions' => array('id !=' => 2)
		));
		$this->set('inventoryStatus', $inventoryStatus);
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
		ini_set('max_execution_time', 300); //300 seconds = 5 minutes

		$this->set('page_title', 'Return Challan Details');
		if (!$this->DistReturnChallan->exists($id)) {
			throw new NotFoundException(__('Invalid challan'));
		}

		$options = array(
			'conditions' => array(
				'DistReturnChallan.' . $this->DistReturnChallan->primaryKey => $id
			),
			'recursive' => 0
		);
		$challan = $this->DistReturnChallan->find('first', $options);
		$challandetail = $this->DistReturnChallanDetail->find('all', array(
				'conditions' => array(
					'DistReturnChallanDetail.challan_id' => $challan['DistReturnChallan']['id']
				),
				'joins'=>array(
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'left',
						'conditions' => 'Product.id = DistReturnChallanDetail.product_id'
					), 
					array(
						'alias' => 'VirtualProduct',
						'table' => 'products',
						'type' => 'left',
						'conditions' => 'VirtualProduct.id = DistReturnChallanDetail.virtual_product_id'
					),
					array(
						'alias' => 'MeasurementUnit',
						'table' => 'measurement_units',
						'type' => 'left',
						'conditions' => 'MeasurementUnit.id = Product.sales_measurement_unit_id'
					),
				),
				'fields' => 'DistReturnChallanDetail.*,VirtualProduct.product_code,VirtualProduct.name,VirtualProduct.parent_id,VirtualProduct.is_virtual,Product.product_code,Product.name,MeasurementUnit.name',
				'order' => array('Product.order' => 'asc'),
				'recursive' => 0
			)
		);
		$dist_store_id = $challan['DistReturnChallan']['sender_store_id'];
		$dist_db_id_find = $this->DistStore->find('first', array(
			'conditions' => array(
				'DistStore.id' => $dist_store_id
			)
		));
		$dist_distributor_id = $dist_db_id_find['DistStore']['dist_distributor_id'];
		$total_price = 0;
		foreach ($challandetail as $key => $value) {
			$product_price = 0;
			$product_price = $value['DistReturnChallanDetail']['challan_qty'] * $value['DistReturnChallanDetail']['unit_price'];

			$total_price = $total_price + $product_price;
		}
		
		/*Receive return Challan by Area office*/
		if ($this->request->is('post')) {
			if (!$this->request->data['DistReturnChallan']['received_date']) {
				$this->Session->setFlash(__('Return Challan Received Date is required.'), 'flash/error');
				$this->redirect(array('action' => 'view/' . $id));
			}

			if ($challan['DistReturnChallan']['status'] > 1) {
				$this->Session->setFlash(__('Return challan has already been received.'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
			//echo date('Y-m-d', strtotime($this->request->data['DistReturnChallan']['received_date']));
			//pr($this->request->data);die();
			$data_array = array();
			$data = array();
			$insert_data_array = array();
			$ncp_insert_data_array = array();
			$update_data_array = array();
			$ncp_update_data_array = array();
			$update_second_data_array = array();

			$update_data = array();
			$ncp_update_data = array();

			//pr($this->request->data);
			//exit;

			foreach ($this->request->data['product_id'] as $key => $val) {

				$receive_quantity = $this->request->data['quantity'][$key];
				$quantity = $this->unit_convert($val, $this->request->data['measurement_unit_id'][$key], $receive_quantity);

				// ------------ stock update --------------------

				$is_ncp = $this->request->data['is_ncp'][$key];

				if (!$is_ncp) {
					// ------------ sellable and bonus stock update --------------------
					$inventory_info = $this->CurrentInventory->find('first', array(
						'conditions' => array(
							'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
							'CurrentInventory.inventory_status_id' => $this->request->data['inventory_status_id'][$key],
							'CurrentInventory.product_id' => $val,
							'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
							'CurrentInventory.expire_date' => $this->request->data['expire_date'][$key]
						),
						'recursive' => -1
					));

					//pr($inventory_info);exit;

					if (!empty($inventory_info)) {
						$update_data['id'] = $inventory_info['CurrentInventory']['id'];
						$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] + $quantity;
						$update_data['transaction_type_id'] = 38; //SO TO ASO (Return Received)
						$update_data['updated_at'] = $this->current_datetime();
						$update_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['DistReturnChallan']['received_date']));
						$update_data_array[] = $update_data;
						// Update inventory data
						$this->CurrentInventory->saveAll($update_data);
						unset($update_data);
					} else {

						/* Again update without batch and expire date */
						$inventory_info_row = $this->CurrentInventory->find('first', array(
							'conditions' => array(
								'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
								'CurrentInventory.inventory_status_id' => $this->request->data['inventory_status_id'][$key],
								'CurrentInventory.product_id' => $val
							),
							'recursive' => -1
						));

						if (!empty($inventory_info_row)) {

							$update_data['id'] = $inventory_info_row['CurrentInventory']['id'];
							$update_data['qty'] = $inventory_info_row['CurrentInventory']['qty'] + $quantity;
							$update_data['updated_at'] = $this->current_datetime();
							$update_data['transaction_type_id'] = 38; //SO TO ASO (Return Received)
							$update_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['DistReturnChallan']['received_date']));
							$update_second_data_array[] = $update_data;

							// Update inventory data
							$this->CurrentInventory->saveAll($update_data);
							unset($update_data);
						} else {
							$insert_data['store_id'] = $this->UserAuth->getStoreId();
							$insert_data['inventory_status_id'] = $this->request->data['inventory_status_id'][$key];
							$insert_data['product_id'] = $val;
							$insert_data['batch_number'] = $this->request->data['batch_no'][$key];
							$insert_data['expire_date'] = $this->request->data['expire_date'][$key];
							$insert_data['qty'] = $quantity;
							$insert_data['updated_at'] = $this->current_datetime();
							$insert_data['transaction_type_id'] = 38; //SO TO ASO (Return Received)
							$insert_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['DistReturnChallan']['received_date']));
							$insert_data_array[] = $insert_data;
						}
					}
					// ------------ End sellable and bonus stock update --------------------
				} else {
					// ------------ stock update --------------------
					$inventory_info = $this->CurrentInventory->find('first', array(
						'conditions' => array(
							'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
							'CurrentInventory.inventory_status_id' => 2,
							'CurrentInventory.product_id' => $val,
							'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
							'CurrentInventory.expire_date' => $this->request->data['expire_date'][$key]
						),
						'recursive' => -1
					));

					//echo $this->CurrentInventory->getLastQuery().'<br>';
					if (!empty($inventory_info)) {
						$ncp_update_data['id'] = $inventory_info['CurrentInventory']['id'];
						$ncp_update_data['qty'] = $inventory_info['CurrentInventory']['qty'] + $quantity;
						$ncp_update_data['updated_at'] = $this->current_datetime();
						$ncp_update_data['transaction_type_id'] = 38; // SO TO ASO (Receive NCP Return)
						$ncp_update_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['DistReturnChallan']['received_date']));
						// $update_data_array[] = $update_data;
						$this->CurrentInventory->save($ncp_update_data);
						unset($ncp_update_data);
					} else {
						$ncp_insert_data['store_id'] = $this->UserAuth->getStoreId();
						$ncp_insert_data['inventory_status_id'] = 2; //NCP inventory
						$ncp_insert_data['product_id'] = $val;
						$ncp_insert_data['batch_number'] = $this->request->data['batch_no'][$key];
						$ncp_insert_data['expire_date'] = $this->request->data['expire_date'][$key];
						$ncp_insert_data['qty'] = $quantity;
						$ncp_insert_data['updated_at'] = $this->current_datetime();
						$ncp_insert_data['transaction_type_id'] = 38; // SO TO ASO (Receive NCP Return)
						$ncp_insert_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['DistReturnChallan']['received_date']));
						// $insert_data_array[] = $insert_data;
						//pr($insert_data);
						$this->CurrentInventory->create();
						$this->CurrentInventory->save($ncp_insert_data);
						unset($ncp_insert_data);
					}
				}

				/* update so inventory start */

				/*$inventory_info_rows = $this->CurrentInventory->find('all', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $this->request->data['SenderStore_id'][$key],
						'CurrentInventory.inventory_status_id' => 1, //All time product deduct from sound product
						'CurrentInventory.product_id' => $val
					),
					'recursive' => -1
				));


				if (!empty($inventory_info_rows)) {
					
					
					   $total_base_unit=$quantity;
						for ($batch_count = 0; $batch_count < count($inventory_info_rows); $batch_count++) {

							if ($inventory_info_rows[$batch_count]['CurrentInventory']['qty'] > $total_base_unit) {
								$updated_qty = [];
								$updated_current_inventory['id'] = $inventory_info_rows[$batch_count]['CurrentInventory']['id'];
								$updated_current_inventory['qty'] = $inventory_info_rows[$batch_count]['CurrentInventory']['qty'] - $total_base_unit;
								$updated_current_inventory['updated_at'] = $this->current_datetime();

								$updated_current_inventory['transaction_type_id']= 19; //SO TO ASO (Return Received)
								$updated_current_inventory['transaction_date'] = date('Y-m-d', strtotime($this->request->data['DistReturnChallan']['received_date']));
								$this->CurrentInventory->save($updated_current_inventory);
								break;
							} else {
								$updated_qty[$batch_count] = $inventory_info_rows[$batch_count]['CurrentInventory']['qty'] - $inventory_info_rows[$batch_count]['CurrentInventory']['qty'];

								$updated_current_inventory['id'] = $inventory_info_rows[$batch_count]['CurrentInventory']['id'];
								$updated_current_inventory['qty'] = $updated_qty[$batch_count];
								$updated_current_inventory['updated_at'] = $this->current_datetime();
								$updated_current_inventory['transaction_type_id']= 19; //SO TO ASO (Return Received)
								$updated_current_inventory['transaction_date'] = date('Y-m-d', strtotime($this->request->data['DistReturnChallan']['received_date']));
								$this->CurrentInventory->save($updated_current_inventory);
							   // remaining amount 
								$total_base_unit = $total_base_unit - $inventory_info_rows[$batch_count]['CurrentInventory']['qty'];
							}
						}
				}*/

				/* update so inventory end */
				//----------------- End Stock update ---------------------


				$data['DistReturnChallanDetail']['id'] = $this->request->data['id'][$key];
				$data['DistReturnChallanDetail']['received_qty'] = $receive_quantity;
				$data_array[] = $data;
			}


			// insert inventory data
			$this->CurrentInventory->saveAll($insert_data_array);

			// update received quantity

			$this->DistReturnChallanDetail->saveAll($data_array);

			// update challan status 
			$returnChalan['id'] = $id;
			$returnChalan['status'] = 2;
			//$returnChalan['received_date'] = date('Y-m-d',strtotime($this->request->data['DistReturnChallan']['received_date']));
			$returnChalan['received_date'] = date('Y-m-d', strtotime($this->request->data['DistReturnChallan']['received_date']));
			$returnChalan['updated_at'] = $this->current_datetime();
			$returnChalan['updated_by'] = $this->UserAuth->getUserId();
			$returnChalan['transaction_type_id'] = 37; // Dist TO ASO (Return Received) 
			$this->DistReturnChallan->save($returnChalan);

			/*Update NCP Status to -4 for NCP received by area office*/
			if($this->DistNcpCollection->updateAll(
				array(
					'DistNcpCollection.ncp_challan_id'		=> $id,
					'DistNcpCollection.status'				=> 4,
					'DistNcpCollection.updated_by'	=> $this->UserAuth->getUserId(),
					'DistNcpCollection.updated_at'	=> "'".$this->current_datetime()."'"
				),
				array(
					'DistNcpCollection.status' => 3,
					array(
						'DistNcpCollection.ncp_challan_id' => $id
					)
				)
			)){
				/*NCP Details update*/
				$this->DistNcpCollectionDetail->updateAll(
					array(
						'DistNcpCollectionDetail.status'		=> 4,
						'DistNcpCollectionDetail.updated_by'	=> $this->UserAuth->getUserId(),
						'DistNcpCollectionDetail.updated_at'	=> "'".$this->current_datetime()."'"
					),
					array(
						'DistNcpCollectionDetail.status'			=> 3,
						'DistNcpCollectionDetail.ncp_collection_id' => $this->DistNcpCollection->find('list',
							array(
								'fields' => array('DistNcpCollection.id'),
								'conditions' => array('DistNcpCollection.ncp_challan_id' => $id),
								'recursive' => -1
							)
						)
					)
				);
			}
			//update balance
			$balance = 0;
			$this->loadModel('DistDistributorBalance');
			$this->loadModel('DistDistributorBalanceHistory');
			$dist_balance_info = $this->DistDistributorBalance->find('first', array(
				'conditions' => array(
					'DistDistributorBalance.dist_distributor_id' => $dist_distributor_id
				),
				'limit' => 1,
				'recursive' => -1
			));
			$dist_balance = $dist_balance_info['DistDistributorBalance']['balance'];
			$balance = $dist_balance + $total_price;

			$dist_balance_data = array();
			$dist_balance = array();
			$dist_balance['id'] = $dist_balance_info['DistDistributorBalance']['id'];
			$dist_balance['office_id'] = $dist_balance_info['DistDistributorBalance']['office_id'];
			$dist_balance['dist_distributor_id'] = $dist_balance_info['DistDistributorBalance']['dist_distributor_id'];
			$dist_balance['balance'] = $balance;

			if ($this->DistDistributorBalance->save($dist_balance)) {

				$dist_balance_data['dist_distributor_id'] = $dist_distributor_id;
				$dist_balance_data['dist_distributor_balance_id'] = $dist_balance_info['DistDistributorBalance']['id'];
				$dist_balance_data['office_id'] = $dist_balance_info['DistDistributorBalance']['office_id'];
				$dist_balance_data['balance'] = $balance;
				$dist_balance_data['balance_type'] = 1;
				$dist_balance_data['balance_transaction_type_id'] = 3;
				$dist_balance_data['transaction_amount'] = $total_price;
				$dist_balance_data['transaction_date'] = date('Y-m-d');
				$dist_balance_data['created_at'] = $this->current_datetime();
				$dist_balance_data['created_by'] = $this->UserAuth->getUserId();
				$dist_balance_data['updated_at'] = $this->current_datetime();
				$dist_balance_data['updated_by'] = $this->UserAuth->getUserId();
				$this->DistDistributorBalanceHistory->create();
				$this->DistDistributorBalanceHistory->save($dist_balance_data);
			}
			//end update balance
			$this->Session->setFlash(__('Return Challan has been received.'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$this->set(compact('challan', 'challandetail', 'office_parent_id'));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->set('page_title', 'New Return Challan');

		$this->loadModel('Office');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions = array('Office.office_type_id' => 2);
		} else {
			$conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
		}
		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
		$this->set(compact('offices'));


		/*$effective_next_date = $this->DistProductPrice->find('first', array(
				'conditions' => array('DistProductPrice.product_id' => 27, 'DistProductPrice.effective_date <=' => '2019-12-10', 'DistProductPrice.institute_id' => 0, 'DistProductPrice.has_combination' => 0),
				//'fields' => array('DistProductPrice.id','DistProductPrice.effective_date','DistProductCombination.*'),
				'order' => array('DistProductPrice.effective_date' => 'desc'),
				'recursive' => 1
				)
			);
		
		pr($effective_next_date);
		exit;*/


		/*$this->loadmodel('DistDistributor');
		$distributors = $this->DistDistributor->find('list', array(
			'fields' => array('DistDistributor.id', 'DistDistributor.name'),
			'conditions' => array('DistDistributor.office_id' => $office_id,'DistDistributor.is_active'=> 1),
			'order' => array('DistDistributor.name' => 'asc'),
			'recursive' => 0
		));
		$this->set(compact('distributors'));*/

		if ($this->request->is('post')) {




			if (empty($this->request->data['s']) && empty($this->request->data['b']) && empty($this->request->data['n'])) {
				$this->Session->setFlash(__('Return Challan not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			} else {
				$dist_distributor_id = $this->request->data['DistReturnChallan']['distributor_id'];
				$dist_store_info = $this->DistStore->find('first', array(
					'conditions' => array('DistStore.dist_distributor_id' => $dist_distributor_id),
					'recursive' => -1
				));
				$dist_store_id = $dist_store_info['DistStore']['id'];

				$storeCondition = array('Store.office_id' => $this->request->data['DistReturnChallan']['office_id'], 'Store.store_type_id' => 2);
				$area_store_info = $this->Store->find(
					'first',
					array(
						'conditions' => $storeCondition,
						'joins' => array(
							array(
								'table' => 'offices',
								'alias' => 'Office',
								'type' => 'Inner',
								'conditions' => 'Office.id=Store.office_id'
							)
						),
						'fields' => array('Store.id', 'Store.name'),
						//'order' => array('Store.name' => 'asc'),
						'recursive' => -1
					)
				);
				$receiver_store_id = $area_store_info['Store']['id'];

				$challan_data = array();

				$challan_data['DistReturnChallan']['transaction_type_id'] = 13; //Dist TO ASO (return)
				$challan_data['DistReturnChallan']['inventory_status_id'] = 1;

				$challan_data['DistReturnChallan']['office_id'] = $this->request->data['DistReturnChallan']['office_id'];

				$challan_data['DistReturnChallan']['challan_date'] = $this->current_date();
				$challan_data['DistReturnChallan']['sender_store_id'] = $dist_store_id;
				$challan_data['DistReturnChallan']['receiver_store_id'] = $receiver_store_id;

				//$challan_data['DistReturnChallan']['sender_store_id'] =23; //mohakhali store
				$challan_data['DistReturnChallan']['status'] = 1;
				$challan_data['DistReturnChallan']['created_at'] = $this->current_datetime();
				$challan_data['DistReturnChallan']['created_by'] = $this->UserAuth->getUserId();
				$challan_data['DistReturnChallan']['updated_at'] = $this->current_datetime();
				$challan_data['DistReturnChallan']['updated_by'] = 0;

				//pr($challan_data);

				$this->DistReturnChallan->create();

				if ($this->DistReturnChallan->save($challan_data)) {
					$udata = array();
					$udata['id'] = $this->DistReturnChallan->id;
					$udata['challan_no'] = 'DRCHA' . (10000 + $this->DistReturnChallan->id);
					$this->DistReturnChallan->save($udata);


					if (!empty($this->request->data['s'])) {
						$data_array = array();
						$update_data_array = array();
						$so_update_data_array = array();
						$insert_data_array = array();
						$data = array();
						$parent_product_array = array();
						foreach ($this->request->data['s']['product_id'] as $key => $val) {
							//pr($this->request->data['s']['product_id']);

							$productid = $this->request->data['s']['product_id'][$key];
							$productinfo = $this->get_mother_product_info($productid);
							if($productinfo['Product']['parent_id'] > 0){

								$checkparentpd = $parent_product_array[$productinfo['Product']['parent_id']];

								if(count($checkparentpd) > 0){
									$pdnamehsow = 1;
								}else{
									$pdnamehsow = $this->get_dist_return_product_inventroy_check($productinfo['Product']['parent_id'], $productid, $dist_distributor_id, 0);
								}

								$data['DistReturnChallanDetail']['product_id'] = $productinfo['Product']['parent_id'];
								$data['DistReturnChallanDetail']['virtual_product_id'] = $val;
								$data['DistReturnChallanDetail']['virtual_product_name_show'] = $pdnamehsow;

							}else{

								$data['DistReturnChallanDetail']['product_id'] = $productid;
								$data['DistReturnChallanDetail']['virtual_product_id'] = 0;
								$data['DistReturnChallanDetail']['virtual_product_name_show'] = 0;

								$parent_product_array[$productid] = $productid;
								
							}

							// /$data['DistReturnChallanDetail']['product_id'] = 

							$data['DistReturnChallanDetail']['challan_id'] = $this->DistReturnChallan->id;
							
							$data['DistReturnChallanDetail']['measurement_unit_id'] = $this->request->data['s']['measurement_unit'][$key];
							$data['DistReturnChallanDetail']['challan_qty'] = $this->request->data['s']['quantity'][$key];
							$data['DistReturnChallanDetail']['unit_price'] = $this->request->data['s']['unit_price'][$key];

							$data['DistReturnChallanDetail']['batch_no'] = $this->request->data['s']['batch_no'][$key];
							$data['DistReturnChallanDetail']['expire_date'] = ($this->request->data['s']['expire_date'][$key] != '' ? date('Y-m-d', strtotime($this->request->data['s']['expire_date'][$key])) : NULL);
							$data['DistReturnChallanDetail']['inventory_status_id'] = 1;
							$data['DistReturnChallanDetail']['remarks'] = $this->request->data['s']['remarks'][$key];

							$data_array[] = $data;

							// ------------ stock update --------------------
							$inventory_info = $this->DistCurrentInventory->find('first', array(
								'conditions' => array(
									//'CurrentInventory.qty >=' => 0,
									'DistCurrentInventory.store_id' => $dist_store_id,
									'DistCurrentInventory.inventory_status_id' => 1,
									'DistCurrentInventory.product_id' => $this->request->data['s']['product_id'][$key]
								),
								'order' => array('DistCurrentInventory.qty' => 'desc'),
								'recursive' => -1
							));

							//pr($inventory_info);
							//exit;

							$deduct_quantity = $this->unit_convert($val, $this->request->data['s']['measurement_unit'][$key], $this->request->data['s']['quantity'][$key]);

							$update_data['id'] = $inventory_info['DistCurrentInventory']['id'];
							$update_data['qty'] = $inventory_info['DistCurrentInventory']['qty'] - $deduct_quantity;
							$update_data['transaction_type_id'] = 13; // Dist to ASO (Return)	
							$insert_data['transaction_date'] = $this->current_date();
							$update_data_array[] = $update_data;
						}

						//pr($data_array);

						// insert challan data
						$this->DistReturnChallanDetail->saveAll($data_array);
						// Update inventory data
						$this->DistCurrentInventory->saveAll($update_data_array);
					}

					if (!empty($this->request->data['b'])) {
						$data_array = array();
						$update_data_array = array();
						$so_update_data_array = array();
						$insert_data_array = array();
						$data = array();
						$parent_product_array = array();
						foreach ($this->request->data['b']['product_id'] as $key => $val) {
							//pr($this->request->data['s']['product_id']);

							$productid = $this->request->data['b']['product_id'][$key];
							$productinfo = $this->get_mother_product_info($productid);
							if($productinfo['Product']['parent_id'] > 0){

								$checkparentpd = $parent_product_array[$productinfo['Product']['parent_id']];

								if(count($checkparentpd) > 0){
									$pdnamehsow = 1;
								}else{
									$pdnamehsow = $this->get_dist_return_product_inventroy_check($productinfo['Product']['parent_id'], $productid, $dist_distributor_id, 1);
								}

								$data['DistReturnChallanDetail']['product_id'] = $productinfo['Product']['parent_id'];
								$data['DistReturnChallanDetail']['virtual_product_id'] = $val;
								$data['DistReturnChallanDetail']['virtual_product_name_show'] = $pdnamehsow;

							}else{

								$data['DistReturnChallanDetail']['product_id'] = $productid;
								$data['DistReturnChallanDetail']['virtual_product_id'] = 0;
								$data['DistReturnChallanDetail']['virtual_product_name_show'] = 0;

								$parent_product_array[$productid] = $productid;
								
							}
							//$data['DistReturnChallanDetail']['product_id'] = $this->request->data['b']['product_id'][$key];

							$data['DistReturnChallanDetail']['challan_id'] = $this->DistReturnChallan->id;
							$data['DistReturnChallanDetail']['measurement_unit_id'] = $this->request->data['b']['measurement_unit'][$key];
							$data['DistReturnChallanDetail']['challan_qty'] = $this->request->data['b']['quantity'][$key];
							$data['DistReturnChallanDetail']['unit_price'] = 0;

							$data['DistReturnChallanDetail']['batch_no'] = $this->request->data['b']['batch_no'][$key];
							$data['DistReturnChallanDetail']['expire_date'] = ($this->request->data['b']['expire_date'][$key] != '' ? date('Y-m-d', strtotime($this->request->data['b']['expire_date'][$key])) : NULL);
							$data['DistReturnChallanDetail']['inventory_status_id'] = 1;
							$data['DistReturnChallanDetail']['remarks'] = $this->request->data['b']['remarks'][$key];

							$data_array[] = $data;

							// ------------ stock update --------------------
							$inventory_info = $this->DistCurrentInventory->find('first', array(
								'conditions' => array(
									//'CurrentInventory.qty >=' => 0,
									'DistCurrentInventory.store_id' => $dist_store_id,
									'DistCurrentInventory.inventory_status_id' => 1,
									'DistCurrentInventory.product_id' => $this->request->data['b']['product_id'][$key]
								),
								'order' => array('DistCurrentInventory.qty' => 'desc'),
								'recursive' => -1
							));

							//pr($inventory_info);
							//exit;

							$deduct_quantity = $this->unit_convert($val, $this->request->data['b']['measurement_unit'][$key], $this->request->data['b']['quantity'][$key]);

							$update_data['id'] = $inventory_info['DistCurrentInventory']['id'];
							$update_data['qty'] = $inventory_info['DistCurrentInventory']['qty'] - $deduct_quantity;
							$update_data['transaction_type_id'] = 13; // Dist to ASO (Return)	
							$insert_data['transaction_date'] = $this->current_date();
							$update_data_array[] = $update_data;
						}

						// insert challan data
						$this->DistReturnChallanDetail->saveAll($data_array);
						// Update inventory data
						$this->DistCurrentInventory->saveAll($update_data_array);
					}


					if (!empty($this->request->data['n'])) {
						$data_array = array();
						$update_data_array = array();
						$so_update_data_array = array();
						$insert_data_array = array();
						$data = array();
						$parent_product_array = array();
						foreach ($this->request->data['n']['product_id'] as $key => $val) {
							//pr($this->request->data['n']['product_id']);

							$productid = $this->request->data['n']['product_id'][$key];

							$productinfo = $this->get_mother_product_info($productid);

							if($productinfo['Product']['parent_id'] > 0){

								$checkparentpd = $parent_product_array[$productinfo['Product']['parent_id']];

								if(count($checkparentpd) > 0){
									$pdnamehsow = 1;
								}else{
									$pdnamehsow = $this->get_dist_return_product_inventroy_check($productinfo['Product']['parent_id'], $productid, $dist_distributor_id, 1);
								}

								
								$data['DistReturnChallanDetail']['product_id'] = $productinfo['Product']['parent_id'];
								$data['DistReturnChallanDetail']['virtual_product_id'] = $val;
								$data['DistReturnChallanDetail']['virtual_product_name_show'] = $pdnamehsow;

							}else{

								$data['DistReturnChallanDetail']['product_id'] = $productid;
								$data['DistReturnChallanDetail']['virtual_product_id'] = 0;
								$data['DistReturnChallanDetail']['virtual_product_name_show'] = 0;

								$parent_product_array[$productid] = $productid;
								
							}
							//$data['DistReturnChallanDetail']['product_id'] = $this->request->data['n']['product_id'][$key];
							$data['DistReturnChallanDetail']['challan_id'] = $this->DistReturnChallan->id;
							
							$data['DistReturnChallanDetail']['measurement_unit_id'] = $this->request->data['n']['measurement_unit'][$key];
							$data['DistReturnChallanDetail']['challan_qty'] = $this->request->data['n']['quantity'][$key];
							$data['DistReturnChallanDetail']['unit_price'] = $this->request->data['n']['unit_price'][$key];

							$data['DistReturnChallanDetail']['batch_no'] = $this->request->data['n']['batch_no'][$key];
							$data['DistReturnChallanDetail']['expire_date'] = ($this->request->data['n']['expire_date'][$key] != '' ? date('Y-m-d', strtotime($this->request->data['n']['expire_date'][$key])) : NULL);
							$data['DistReturnChallanDetail']['inventory_status_id'] = 1;
							$data['DistReturnChallanDetail']['remarks'] = $this->request->data['n']['remarks'][$key];
							$data['DistReturnChallanDetail']['is_ncp'] = 1;

							$data_array[] = $data;

							// ------------ stock update --------------------
							$inventory_info = $this->DistCurrentInventory->find('first', array(
								'conditions' => array(
									//'CurrentInventory.qty >=' => 0,
									'DistCurrentInventory.store_id' => $dist_store_id,
									'DistCurrentInventory.inventory_status_id' => 1,
									'DistCurrentInventory.product_id' => $this->request->data['n']['product_id'][$key]
								),
								'order' => array('DistCurrentInventory.qty' => 'desc'),
								'recursive' => -1
							));

							//pr($inventory_info);
							//exit;

							$deduct_quantity = $this->unit_convert($val, $this->request->data['n']['measurement_unit'][$key], $this->request->data['n']['quantity'][$key]);

							$update_data['id'] = $inventory_info['DistCurrentInventory']['id'];
							$update_data['qty'] = $inventory_info['DistCurrentInventory']['qty'] - $deduct_quantity;
							$update_data['transaction_type_id'] = 13; // Dist to ASO (Return)	
							$insert_data['transaction_date'] = $this->current_date();
							$update_data_array[] = $update_data;
						}

						//pr($data_array);

						// insert challan data
						$this->DistReturnChallanDetail->saveAll($data_array);
						// Update inventory data
						$this->DistCurrentInventory->saveAll($update_data_array);
					}

					//exit;



					$this->Session->setFlash(__('Product Returned has been created.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
			}
		}

		/*
		$receiverStore = $this->DistStore->find('list', array(
			'conditions' => array('store_type_id' => 1,'office_id' => $this->UserAuth->getOfficeParentId()),
			'order' => array('name'=>'asc')
		));	
		*/

		$receiverStore = $this->DistStore->find('list', array(
			'conditions' => array('store_type_id' => 1),
			'order' => array('name' => 'asc')
		));

		$this->loadModel('InventoryStatus');
		$inventoryStatus = $this->InventoryStatus->find('list', array(
			'conditions' => array('id !=' => 2)
		));


		/*$products_from_ci = $this->DistCurrentInventory->find('all', array('fields' => array('DistCurrentInventory.product_id'),
			'conditions' => array('DistCurrentInventory.store_id' => $this->UserAuth->getStoreId()),
			'group' => 'DistCurrentInventory.product_id HAVING sum(DistCurrentInventory.qty)>0'
		));

		
		$product_ci=array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci[]=$each_ci['DistCurrentInventory']['product_id'];
		}
		
		$product_ci_in=implode(",",$product_ci);
		$products = $this->Product->find('list', array('conditions' => array('Product.id' => $product_ci),'order' => array('order' => 'asc')));*/

		//$products = $this->Product->find('list',array('order' => array('name'=>'asc')));	

		$this->set(compact('receiverStore', 'inventoryStatus'));
	}



	public function get_inventory_product_list()
	{
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$dist_distributor_id = $this->request->data['distributor_id'];

		$products_from_ci = $this->DistCurrentInventory->find('all', array(
			'fields' => array('DistCurrentInventory.product_id'),
			'conditions' => array('DistStore.dist_distributor_id' => $dist_distributor_id),
			'group' => 'DistCurrentInventory.product_id HAVING sum(DistCurrentInventory.qty)>0'
		));

		//pr($products_from_ci);exit;

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			//array_push($product_ci, $each_ci['DistCurrentInventory']['product_id']);
			$product_ci[]=$each_ci['DistCurrentInventory']['product_id'];
		}

		$product_ci_in = implode(",", $product_ci);

		$products = $this->Product->find('all', array(
			'conditions' => array('Product.id' => $product_ci), 'order' => array('Product.order' => 'asc'),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'ParentProduct',
					'type' => 'left',
					'conditions' => 'ParentProduct.id=Product.parent_id'
				)
			),
			'fields' => array('Product.id as id', 'Product.name as name', 'ParentProduct.id as p_id', 'ParentProduct.name as p_name'),
			'recursive' => -1
		));

		//echo '<pre>';print_r($products);exit;
		$group_product = array();
		foreach ($products as $data) {
			if ($data[0]['p_id']) {
				$group_product[$data[0]['p_id']][] = $data[0]['id'];
			} else {
				$group_product[$data[0]['id']][] = $data[0]['id'];
			}
		}

		//echo '<pre>';print_r($group_product);exit;

		$product_array = array();
		foreach ($products as $data) {
			if ($data['0']['p_id'] && count($group_product[$data[0]['p_id']]) == 1) {
				$name = $data[0]['p_name'];
			} else {
				$name = $data[0]['name'];
			}
			$product_array[] = array(
				'id' => $data[0]['id'],
				'name' => $name
			);
		}

		if (!empty($products)) {
			echo json_encode(array_merge($rs, $product_array));
		} else {
			echo json_encode($rs);
		}


		$this->autoRender = false;
	}

	public function get_bonus_inventory_product_list()
	{
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$dist_distributor_id = $this->request->data['distributor_id'];

		$products_from_ci = $this->DistCurrentInventory->find('all', array(
			'fields' => array('DistCurrentInventory.product_id'),
			'conditions' => array('DistStore.dist_distributor_id' => $dist_distributor_id),
			'group' => 'DistCurrentInventory.product_id HAVING sum(DistCurrentInventory.qty)>0'
		));

		//pr($products_from_ci);exit;

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			//array_push($product_ci, $each_ci['DistCurrentInventory']['product_id']);
			$product_ci[]=$each_ci['DistCurrentInventory']['product_id'];
		}

		$product_ci_in = implode(",", $product_ci);

		$products = $this->Product->find('all', array(
			'conditions' => array(
				'Product.id' => $product_ci, 
				'Product.is_distributor_product' => 1
			), 
			'order' => array('Product.order' => 'asc'),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'ParentProduct',
					'type' => 'left',
					'conditions' => 'ParentProduct.id=Product.parent_id'
				)
			),
			'fields' => array('Product.id as id', 'Product.name as name', 'ParentProduct.id as p_id', 'ParentProduct.name as p_name'),
			'recursive' => -1
		));

		$group_product = array();
		foreach ($products as $data) {
			if ($data[0]['p_id']) {
				$group_product[$data[0]['p_id']][] = $data[0]['id'];
			} else {
				$group_product[$data[0]['id']][] = $data[0]['id'];
			}
		}

		$product_array = array();
		foreach ($products as $data) {
			if ($data['0']['p_id'] && count($group_product[$data[0]['p_id']]) == 1) {
				$name = $data[0]['p_name'];
			} else {
				$name = $data[0]['name'];
			}
			$product_array[] = array(
				'id' => $data[0]['id'],
				'name' => $name
			);
		}

		if (!empty($products)) {
			echo json_encode(array_merge($rs, $product_array));
		} else {
			echo json_encode($rs);
		}


		/*
		$results = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'conditions' => array('Product.id' => $product_ci, 'Product.is_distributor_product' => 1),
			'order' => array('Product.order' => 'asc'),
			'recursive' => -1
		));

		$data_array = Set::extract($results, '{n}.Product');
		if (!empty($results)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		*/

		$this->autoRender = false;

	}

	public function get_mother_product_info($pid){

		$productinfo = $this->Product->find('first', array(
			'conditions' => array(
				'Product.id' => $pid
			), 
			'fields'=>array('Product.id', 'Product.name', 'Product.product_code', 'Product.is_virtual', 'Product.parent_id'),
			'recursive'=>-1
		));

		return $productinfo;

	}

	public function get_dist_return_product_inventroy_check($parent_product_id, $vpid, $dist_distributor_id, $dbproductid){

		if($dbproductid == 1){
			$con = array(
				'DistStore.dist_distributor_id' => $dist_distributor_id, 
				'DistCurrentInventory.product_id' => $parent_product_id,
				'Product.is_distributor_product' => 1,
			);
		}else{
			$con = array(
				'DistStore.dist_distributor_id' => $dist_distributor_id, 
				'DistCurrentInventory.product_id' => $parent_product_id,
			);
		}

		$parentproductcount = $this->DistCurrentInventory->find('count', array(
			'fields' => array('DistCurrentInventory.product_id'),
			'conditions' => $con,
			'group' => 'DistCurrentInventory.product_id HAVING sum(DistCurrentInventory.qty)>0'
		));

		if($dbproductid == 1){
			$con2 = array(
				'DistStore.dist_distributor_id' => $dist_distributor_id, 
				'Product.parent_id' => $parent_product_id,
				'Product.is_distributor_product' => 1,
			);
		}else{
			$con2 = array(
				'DistStore.dist_distributor_id' => $dist_distributor_id, 
				'Product.parent_id' => $parent_product_id,
			);
		}

		$chilhproductCount = $this->DistCurrentInventory->find('count', array(
			'fields' => array('DistCurrentInventory.product_id'),
			'conditions' => $con2,
			'group' => 'DistCurrentInventory.product_id HAVING sum(DistCurrentInventory.qty)>0'
		));

		if(empty($parentproductcount)){
			$parentproductcount = 0;
		}

		if(empty($chilhproductCount)){
			$chilhproductCount = 0;
		}

		if($parentproductcount == 0 AND $chilhproductCount == 1){
			$show = 0;
		}else{
			$show = 1;
		}

		return $show;

	}

	public function get_batch_list()
	{
		//$rs = array(array('id' => '', 'title' => '---- Select Batch -----'));
		$product_id = $this->request->data['product_id'];
		$dist_distributor_id = $this->request->data['distributor_id'];
		$office_id = $this->request->data['office_id'];

		/*$dist_store_info = $this->DistStore->find('first', array(
			//'fields' => array('DistStore.batch_number as id', 'CurrentInventory.batch_number as title'),
			'conditions' => array('DistStore.dist_distributor_id' => $dist_distributor_id),
			//'group' => array('CurrentInventory.batch_number'),
			'recursive' => -1
		));
		$dist_store_id = $dist_store_info['DistStore']['id'];*/

		$area_store_info = $this->Store->find('first', array(
			'conditions' => array('Store.office_id' => $office_id, 'Store.store_type_id' => 2),
			'recursive' => -1
		));
		$area_store_id = $area_store_info['Store']['id'];

		if (isset($this->request->data['inventory_status_id'])) {
			$inventory_status_id = 2;
		} else {
			$inventory_status_id = 1;
		}

		if (isset($this->request->data['with_stock'])) {
			$with_stock = $this->request->data['with_stock'];
			$conditions[] = array('CurrentInventory.qty >' => 0);
		} else $with_stock = false;

		if (isset($this->request->data['transaction_type_id'])) {
			$conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
			$conditions[] = array('CurrentInventory.transaction_type_id' => $this->request->data['transaction_type_id']);
			$conditions[] = array('CurrentInventory.product_id' => $product_id);
			$conditions[] = array('CurrentInventory.store_id' => $area_store_id);
		} else {
			$conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
			$conditions[] = array('CurrentInventory.product_id' => $product_id);
			$conditions[] = array('CurrentInventory.store_id' => $area_store_id);
		}

		//$product_id = 12;
		$batch_list = $this->CurrentInventory->find('all', array(
			'fields' => array('CurrentInventory.batch_number as id', 'CurrentInventory.batch_number as title'),
			'conditions' => $conditions,
			'group' => array('CurrentInventory.batch_number'),
			'recursive' => -1
		));


		//pr($batch_list);die();
		$rs = '<option value="">---- Select ----</option>';
		foreach ($batch_list as $value) {
			$rs .= '<option value="' . $value[0]['id'] . '">' . $value[0]['title'] . '</option>';
		}

		echo $rs;
		$this->autoRender = false;
	}


	public function get_expire_date_list()
	{

		$product_id = $this->request->data['product_id'];
		$dist_distributor_id = $this->request->data['distributor_id'];
		$office_id = $this->request->data['office_id'];
		$batch_no = urldecode($this->request->data['batch_no']);

		$area_store_info = $this->Store->find('first', array(
			'conditions' => array('Store.office_id' => $office_id, 'Store.store_type_id' => 2),
			'recursive' => -1
		));
		$area_store_id = $area_store_info['Store']['id'];

		$rs = array(array('id' => '', 'title' => '---- Select Expired Date -----'));
		//$product_id = 11;

		// echo $batch_no;exit;
		//$batch_no = 'T242';
		$inventory_status_id = 1;
		if (isset($this->request->data['with_stock'])) {
			$with_stock = $this->request->data['with_stock'];
			$conditions[] = array('CurrentInventory.qty >' => 0);
		} else $with_stock = false;

		if (isset($this->request->data['transaction_type_id'])) {
			$conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
			$conditions[] = array('CurrentInventory.transaction_type_id' => $this->request->data['transaction_type_id']);
			$conditions[] = array('CurrentInventory.product_id' => $product_id);
			$conditions[] = array('CurrentInventory.batch_number' => $batch_no);
			$conditions[] = array('CurrentInventory.store_id' => $area_store_id);
		} else {
			$conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
			$conditions[] = array('CurrentInventory.product_id' => $product_id);
			$conditions[] = array('CurrentInventory.batch_number' => $batch_no);
			$conditions[] = array('CurrentInventory.store_id' => $area_store_id);
		}
		$exp_date_list = $this->CurrentInventory->find('all', array(
			'fields' => array('CurrentInventory.expire_date as id', 'CurrentInventory.expire_date as title'),
			'conditions' => $conditions,
			'recursive' => -1
		));
		$i = 0;
		foreach ($exp_date_list as $data) {
			$data_array[] = array('id' => $data[$i]['id'], 'title' => date("M-y", strtotime($data[$i]['title'])));
		}
		//pr($exp_date_list);die();
		$rs = '<option value="">---- Select ----</option>';
		foreach ($exp_date_list as $value) {
			$rs .= '<option value="' . $value[0]['id'] . '">' . $value[0]['title'] . '</option>';
		}

		echo $rs;
		$this->autoRender = false;
	}

	public function get_inventory_details_in_return_challan()
	{
		$product_id = $this->request->data['product_id'];
		$dist_distributor_id = $this->request->data['distributor_id'];
		$office_id = $this->request->data['office_id'];
		$is_bonus = isset($this->request->data['is_bonus']) ? $this->request->data['is_bonus'] : 0;

		$dist_store_info = $this->DistStore->find('first', array(
			'conditions' => array('DistStore.dist_distributor_id' => $dist_distributor_id),
			'recursive' => -1
		));
		$dist_store_id = $dist_store_info['DistStore']['id'];

		$conditions_options['DistCurrentInventory.product_id'] = $product_id;
		$conditions_options['DistCurrentInventory.store_id'] = $dist_store_id;

		if ($is_bonus) {
			$info = $this->DistCurrentInventory->find('first', array(
				'fields' => array('DistCurrentInventory.qty', 'Product.return_measurement_unit_id'),
				'conditions' => array($conditions_options),
				'recursive' => 0
			));
		} else {
			$info = $this->DistCurrentInventory->find('first', array(
				'fields' => array('DistCurrentInventory.qty', 'Product.return_measurement_unit_id'),
				'conditions' => array($conditions_options),
				'recursive' => 0
			));
		}

		//pr($info);die;
		//echo $info['CurrentInventory']['qty'];

		if (!empty($info)) {
			echo $this->unit_convertfrombase($product_id, $info['Product']['return_measurement_unit_id'], $is_bonus ? $info['DistCurrentInventory']['qty'] : $info['DistCurrentInventory']['qty']);
		} else {
			echo '';
		}

		$this->autoRender = false;
	}

	public function get_individual_price_bk_2022_03_02()
	{

		$product_id = $this->request->data['product_id'];
		$min_qty = $this->request->data['min_qty'];

		$price_info = $this->DistProductPrice->find(
			'first',
			array(
				'conditions' => array(
					'DistProductPrice.product_id' => $product_id,
					//'DistProductPrice.effective_date <=' => '2019-12-10',
					'DistProductPrice.effective_date <=' => date('Y-m-d'),
					'DistProductPrice.institute_id' => 0,
					'DistProductPrice.has_combination' => 0
				),
				//'fields' => array('DistProductPrice.id','DistProductPrice.effective_date','DistProductCombination.*'),
				'order' => array('DistProductPrice.effective_date' => 'desc'),
				'recursive' => 1
			)
		);

		$no_com_val['individual_slab'] = array();
		$less_qty_val = array();
		$result_data = array();
		foreach ($price_info['DistProductCombination'] as $result) {
			$no_com_val['individual_slab'][] = array(
				'min_qty' => $result['min_qty'],
				'price' => $result['price'],
			);

			if ($min_qty >= $result['min_qty']) {
				$less_qty_val[$result['min_qty']] = $result['price'];
			}
		}

		ksort($less_qty_val);
		$unit_rate = array_pop($less_qty_val);
		if (empty($unit_rate)) {
			$unit_rate = $price_info['DistProductPrice']['general_price'];
		}
		if ($unit_rate) {
			$result_data['unit_rate'] = sprintf("%1\$.6f", $unit_rate);
			$result_data['total_value'] = $unit_rate * sprintf("%1\$.6f", $min_qty);
		} else {
			$result_data['unit_rate'] = 0;
			$result_data['total_value'] = 0;
		}

		echo json_encode($result_data);

		$this->autoRender = false;
	}


	public function get_individual_price()
	{

		$product_id = $this->request->data['product_id'];
		$min_qty = $this->request->data['min_qty'];



		$this->LoadModel('ProductCombinationsV2');
		$this->LoadModel('CombinationsV2');
		$this->LoadModel('CombinationDetailsV2');
		$order_date = date('Y-m-d');


		/*------------- min price slab finding ----------------------*/
		$slab_conditions = array();
		$slab_conditions['ProductCombinationsV2.effective_date <='] = date('Y-m-d', strtotime($order_date));
		$slab_conditions['ProductCombinationsV2.product_id'] = $product_id;
		$slab_conditions['ProductCombinationsV2.min_qty <='] = $min_qty;
		$price_slab = $this->ProductCombinationsV2->find('first', array(
			'conditions' => $slab_conditions,
			'joins' => array(
				array(
					'table' => 'product_price_section_v2',
					'alias' => 'PriceSection',
					'conditions' => 'PriceSection.id=ProductCombinationsV2.section_id and PriceSection.is_db=1'
				),
				array(
					'table' => 'product_price_db_slabs',
					'alias' => 'DBSlab',
					'conditions' => 'DBSlab.product_combination_id=ProductCombinationsV2.id'
				)
			),
			'fields' => array(
				'ProductCombinationsV2.id',
				'ProductCombinationsV2.effective_date',
				'ProductCombinationsV2.min_qty',
				'ProductCombinationsV2.price',
				'DBSlab.price',
				'DBSlab.discount_amount',
			),
			'order' => array(
				'ProductCombinationsV2.effective_date desc',
				'ProductCombinationsV2.min_qty desc'
			),
			'recursive' => -1
		));

		if ($price_slab) {
			$result_data['unit_rate'] = $price_slab['DBSlab']['price'];
			$result_data['total_value'] =  sprintf('%.2f', $price_slab['DBSlab']['price'] * $min_qty);
		} else {

			$slab_conditions = array();
			$slab_conditions['ProductCombinationsV2.effective_date <='] = date('Y-m-d', strtotime($order_date));
			$slab_conditions['ProductCombinationsV2.product_id'] = $product_id;
			/* $slab_conditions['ProductCombinationsV2.min_qty <='] = $min_qty; */
			$price_slab = $this->ProductCombinationsV2->find('first', array(
				'conditions' => $slab_conditions,
				'joins' => array(
					array(
						'table' => 'product_price_section_v2',
						'alias' => 'PriceSection',
						'conditions' => 'PriceSection.id=ProductCombinationsV2.section_id and PriceSection.is_db=1'
					),
					array(
						'table' => 'product_price_db_slabs',
						'alias' => 'DBSlab',
						'conditions' => 'DBSlab.product_combination_id=ProductCombinationsV2.id'
					)
				),
				'fields' => array(
					'ProductCombinationsV2.id',
					'ProductCombinationsV2.effective_date',
					'ProductCombinationsV2.min_qty',
					'ProductCombinationsV2.price',
					'DBSlab.price',
					'DBSlab.discount_amount',
				),
				'order' => array(
					'ProductCombinationsV2.effective_date desc',
					'ProductCombinationsV2.min_qty desc'
				),
				'recursive' => -1
			));

			if ($price_slab) {
				$result_data['unit_rate'] = $price_slab['DBSlab']['price'];
				$result_data['total_value'] =  sprintf('%.2f', $price_slab['DBSlab']['price'] * $min_qty);
			} else {
				$result_data['unit_rate'] = 0;
				$result_data['total_value'] = 0;
			}
		}

		echo json_encode($result_data);

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
	public function admin_delete($id = null)
	{
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->DistReturnChallan->id = $id;
		if (!$this->DistReturnChallan->exists()) {
			throw new NotFoundException(__('Invalid challan'));
		}
		if ($this->DistReturnChallan->delete()) {
			$this->flash(__('DistReturnChallan deleted'), array('action' => 'index'));
		}
		$this->flash(__('DistReturnChallan was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}

	public function admin_edit($id = null)
	{
		$this->set('page_title', 'Edit Return Challan');

		$draft_info = $this->DistReturnChallan->find('all', array(
			'conditions' => array('DistReturnChallan.id' => $id),

			'recursive' => 1
		));
		// pr($draft_info);exit;
		$this->loadModel('ProductPrice');
		$product_price_list = $this->ProductPrice->find('list', array(
			'fields' => array('product_id', 'general_price')
		));
		//pr($product_price_list);die();
		foreach ($draft_info[0]['DistReturnChallanDetail'] as $key => $value) {
			$current_inventory_info = $this->DistCurrentInventory->find('first', array(
				'conditions' => array(
					'DistCurrentInventory.store_id' => $this->UserAuth->getStoreId(),
					'DistCurrentInventory.product_id' => $value['product_id'],
					'DistCurrentInventory.batch_number' => $value['batch_no'],
					'DistCurrentInventory.expire_date' => $value['expire_date'],
					'DistCurrentInventory.inventory_status_id' => $value['inventory_status_id']
				),
				'recursive' => -1
			));
			$product_info = $this->Product->find('first', array(
				'conditions' => array('Product.id' => $value['product_id']),
				'recursive' => -1
			));
			$measurement_unit_info = $this->MeasurementUnit->find('first', array(
				'conditions' => array('MeasurementUnit.id' => $value['measurement_unit_id']),
				'recursive' => -1
			));
			$draft_info[0]['DistReturnChallanDetail'][$key]['Product'] = $product_info['Product'];
			$draft_info[0]['DistReturnChallanDetail'][$key]['MeasurementUnit'] = $measurement_unit_info['MeasurementUnit'];
			$draft_info[0]['DistReturnChallanDetail'][$key]['stock_qty'] = $this->unit_convertfrombase($value['product_id'], $product_info['Product']['return_measurement_unit_id'], $current_inventory_info['DistCurrentInventory']['qty']);
		}

		if ($this->request->is('post')) {

			$return_challan_id = $id;
			$this->DistReturnChallanDetail->deleteAll(array('DistReturnChallanDetail.challan_id' => $return_challan_id));

			if (empty($this->request->data['product_id'])) {
				$this->Session->setFlash(__('Return Challan not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			} else {

				$this->request->data['DistReturnChallan']['transaction_type_id'] = 7; //ASO TO CWH (Returun)  
				$this->request->data['DistReturnChallan']['inventory_status_id'] = $this->request->data['DistReturnChallan']['inventory_status_id'];
				$this->request->data['DistReturnChallan']['challan_date'] = $this->current_date();
				$this->request->data['DistReturnChallan']['sender_store_id'] = $this->UserAuth->getStoreId();
				$this->request->data['DistReturnChallan']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['DistReturnChallan']['updated_at'] = $this->current_datetime();
				$this->request->data['DistReturnChallan']['updated_by'] = $this->UserAuth->getUserId();
				$this->request->data['DistReturnChallan']['id'] = $return_challan_id;
				if (array_key_exists('draft', $this->request->data)) {
					$this->request->data['DistReturnChallan']['status'] = 0;
				} else {
					$this->request->data['DistReturnChallan']['status'] = 1;
				}
				if ($this->DistReturnChallan->save($this->request->data)) {

					$udata['id'] = $return_challan_id;
					$udata['challan_no'] = 'RCH' . (10000 + $return_challan_id);
					$this->DistReturnChallan->save($udata);
					if (!empty($this->request->data['product_id'])) {
						$data_array = array();
						$update_data_array = array();
						$so_update_data_array = array();
						$insert_data_array = array();
						foreach ($this->request->data['product_id'] as $key => $val) {
							$data['DistReturnChallanDetail']['challan_id'] = $return_challan_id;
							$data['DistReturnChallanDetail']['product_id'] = $val;
							$data['DistReturnChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
							$data['DistReturnChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
							$data['DistReturnChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];

							$data['DistReturnChallanDetail']['inventory_status_id'] = $this->request->data['DistReturnChallan']['inventory_status_id'];
							$data['DistReturnChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
							if ($this->request->data['expire_date'][$key] != ' ' && $this->request->data['expire_date'][$key] != 'null' && $this->request->data['expire_date'][$key] != '') {
								$data['DistReturnChallanDetail']['expire_date'] = date('Y-m-d', strtotime($this->request->data['expire_date'][$key]));
							} else {
								$data['DistReturnChallanDetail']['expire_date'] = '';
							}
							$data_array[] = $data;

							//pr($this->request->data['expire_date']);
							//pr($data['DistReturnChallanDetail']['expire_date']);die();

							// ------------ stock update --------------------
							$inventory_info = $this->DistCurrentInventory->find('first', array(
								'conditions' => array(
									'DistCurrentInventory.store_id' => $this->UserAuth->getStoreId(),
									'DistCurrentInventory.inventory_status_id' => $this->request->data['DistReturnChallan']['inventory_status_id'],
									'DistCurrentInventory.product_id' => $val,
									'DistCurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
									'DistCurrentInventory.expire_date' => ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null)
								),
								'recursive' => -1
							));

							$deduct_quantity = $this->unit_convert($val, $this->request->data['measurement_unit'][$key], $this->request->data['quantity'][$key]);

							$update_data['id'] = $inventory_info['DistCurrentInventory']['id'];
							$update_data['qty'] = $inventory_info['DistCurrentInventory']['qty'] - $deduct_quantity;
							$update_data['transaction_type_id'] = 7; // ASO to CWH (Return)	
							$update_data['transaction_date'] = $this->current_date();
							$update_data_array[] = $update_data;
						}
						if (array_key_exists('draft', $this->request->data)) {
							// insert challan data
							$this->DistReturnChallanDetail->saveAll($data_array);
						} else {
							// insert challan data
							$this->DistReturnChallanDetail->saveAll($data_array);
							// Update inventory data
							$this->DistCurrentInventory->saveAll($update_data_array);
						}
					}

					$this->Session->setFlash(__('Return Challan has been updated.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
			}
		}

		/*
		$receiverStore = $this->DistStore->find('list', array(
			'conditions' => array('store_type_id' => 1,'office_id' => $this->UserAuth->getOfficeParentId()),
			'order' => array('name'=>'asc')
		));	
	*/

		$receiverStore = $this->DistStore->find('list', array(
			'conditions' => array('store_type_id' => 1),
			'order' => array('name' => 'asc')
		));

		$this->loadModel('InventoryStatus');
		$inventoryStatus = $this->InventoryStatus->find('list', array(
			'conditions' => array('id !=' => 2)
		));


		$this->loadModel('InventoryStatus');
		$inventoryStatus = $this->InventoryStatus->find('list', array(
			'conditions' => array('id !=' => 2)
		));
		$products_from_ci = $this->DistCurrentInventory->find('all', array(
			'fields' => array('DISTINCT DistCurrentInventory.product_id'),
			'conditions' => array('DistCurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'DistCurrentInventory.qty > ' => 0)
		));

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci[] = $each_ci['DistCurrentInventory']['product_id'];
		}

		$product_ci_in = implode(",", $product_ci);
		$products = $this->Product->find('list', array('conditions' => array('Product.id' => $product_ci), 'order' => array('order' => 'asc')));

		//$products = $this->Product->find('list',array('order' => array('order'=>'asc')));		
		$this->set(compact('receiverStore', 'products', 'inventoryStatus', 'draft_info', 'product_price_list'));
	}
	public function getSOName($territory_id = 0)
	{
		if ($territory_id) {
			$this->loadModel('Territory');
			$territory_info = $this->Territory->find(
				'first',
				array(
					'conditions' => array('Territory.id' => $territory_id),
					'fields' => array('SalesPerson.name'),
					'recursive' => 0
				)
			);
			//pr($territory_info);
			//exit;
			if ($territory_info['SalesPerson']['name']) {
				return $territory_info['SalesPerson']['name'];
			} else {
				return 'NA';
			}
		} else {
			return 'NA';
		}
	}
}
