<?php
App::uses('AppController', 'Controller');
/**
 * Challans Controller
 *
 * @property Challan $Challan
 * @property PaginatorComponent $Paginator
 */
class NcpProductIssuesController extends AppController
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
	public function admin_index()
	{
		$this->set('page_title', 'NCP Product Issue List');
		$this->Challan->recursive = 0;
		if ($this->UserAuth->getOfficeParentId() == 0) {
			$conditions = array(
				'OR' => array(
					array('Challan.transaction_type_id' => 17), //17=ASO to SO (NCP Return)
					array('Challan.transaction_type_id' => 18)  // 18=ASO to SO (Receive NCP Return)
				),
				'Challan.inventory_status_id' => 2,
			);
		} else {
			$conditions = array(
				'OR' => array(
					array('Challan.transaction_type_id' => 17), //17=ASO to SO (NCP Return)
					array('Challan.transaction_type_id' => 18)  // 18=ASO to SO (Receive NCP Return)
				),
				'Challan.inventory_status_id' => 2,
				'Challan.sender_store_id' => $this->UserAuth->getStoreId()
			);
		}
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
		
		//$this->dd($challandetail);
		
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

		$this->loadModel('ReturnChallan');
		$this->loadModel('ReturnChallanDetail');

		if ($this->request->is('post')) {

			if (empty($this->request->data['product_id'])) {
				$this->Session->setFlash(__('NCP product issue not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			} else {

				$this->Challan->validator()->remove('challan_referance_no', 'isUnique');

				$this->request->data['Challan']['transaction_type_id'] = 17;  //17=ASO to SO (NCP Return)
				$this->request->data['Challan']['inventory_status_id'] = 2;  //NCP Inventory
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
					$udata['challan_no'] = 'NCPPI' . (10000 + $this->Challan->id);
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
							$data['ChallanDetail']['inventory_status_id'] = 2;
							$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
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
							$update_data_array[] = $update_data;	

							// ------------ ReturnChallanDetail  update --------------------			
							$challan_info = $this->ReturnChallanDetail->find('first',array(
								'conditions' => array(
													'ReturnChallanDetail.challan_id' => $this->request->data['Challan']['challan_id'],
													'ReturnChallanDetail.product_id' => $val
												),
								'recursive' => -1				
							));		
							
							$update_challan_data['id'] = $challan_info['ReturnChallanDetail']['id'];
							$update_challan_data['remaining_qty'] = $this->request->data['quantity'][$key]-$challan_info['ReturnChallanDetail']['remaining_qty'];
							$update_challan_data_array[] = $update_challan_data;*/
						}
						// insert challan data
						$this->ChallanDetail->saveAll($data_array);

						// Update inventory data
						//$this->CurrentInventory->saveAll($update_data_array);

						// Update Chalan quantity data
						//$this->ReturnChallanDetail->saveAll($update_challan_data_array);

					}


					// Challan close
					/*
					$do_udata['id'] = $this->request->data['Challan']['challan_id'];								
					$do_udata['is_close'] = ($this->request->data['Challan']['is_close'] == 1 ? $this->request->data['Challan']['is_close'] : NULL );								
					$this->ReturnChallan->save($do_udata);*/

					$this->Session->setFlash(__('NCP Product issue has been drafted.'), 'flash/success');
					$this->redirect(array('action' => 'edit', $this->Challan->id));
				}
			}
		}

		/***Show only Virtual (Child Territory) and No Child Territory ***/

		$this->loadModel('Territory');

		$child_territory_parent_id = $this->Territory->find('list', array(
			'conditions' => array(
				'parent_id !=' => 0,
			),
			'fields' => array('Territory.parent_id', 'Territory.name'),
		));

		$receiverStore = $this->Store->find('list', array(
			'conditions' => array(
				'store_type_id' => 3,
				'office_id' => $this->UserAuth->getOfficeId(),
				'NOT' => array('territory_id' => array_keys($child_territory_parent_id)),
				//'territory_id Not IN' => array_keys($child_territory_parent_id)
			),
			'order' => array('name' => 'asc')
		));

		//$products = $this->Product->find('list',array('order' => array('name'=>'asc')));		
		$this->set(compact('receiverStore'));
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

		$this->loadModel('ReturnChallan');
		$this->loadModel('ReturnChallanDetail');

		$ncp_challan_id = $id;
		$ncp_challan_draft_info = $this->Challan->find('all', array(
			'conditions' => array('Challan.id' => $ncp_challan_id),
			'recursive' => 2
		));

		$challan_list_array = $this->ReturnChallan->find('all', array(
			'fields' => array('ReturnChallan.id as id', 'ReturnChallan.challan_no as title'),
			'conditions' => array(
				'ReturnChallan.is_close' => NULL,
				'ReturnChallan.transaction_type_id' => 13,
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

		foreach ($ncp_challan_draft_info[0]['ChallanDetail'] as $key => $value) {
			$current_inventory_info = $this->CurrentInventory->find('first', array(
				'conditions' => array(
					'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
					'CurrentInventory.product_id' => $value['product_id'],
					'CurrentInventory.batch_number' => $value['batch_no'],
					'CurrentInventory.expire_date' => $value['expire_date'],
					'CurrentInventory.inventory_status_id' => 1
				),
				'recursive' => -1
			));
			//$ncp_challan_draft_info[0]['ChallanDetail'][$key]['stock_qty'] = $this->unit_convertfrombase($value['product_id'],$value['Product']['challan_measurement_unit_id'],$current_inventory_info['CurrentInventory']['qty']);
			$ncp_challan_draft_info[0]['ChallanDetail'][$key]['stock_qty'] = $current_inventory_info['CurrentInventory']['qty'];
		}
		//pr($product_list);die();

		if ($this->request->is('post')) {

			$this->ChallanDetail->deleteAll(array('ChallanDetail.challan_id' => $ncp_challan_id));

			if (empty($this->request->data['product_id'])) {
				$this->Session->setFlash(__('NCP product issue not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			} else {

				$this->Challan->validator()->remove('challan_referance_no', 'isUnique');

				$this->request->data['Challan']['transaction_type_id'] = 17;  //17=ASO to SO (NCP Return)
				$this->request->data['Challan']['inventory_status_id'] = 2;  //NCP Inventory
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
					$udata['challan_no'] = 'NCPPI' . (10000 + $ncp_challan_id);
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
							$data['ChallanDetail']['inventory_status_id'] = 2;
							$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
							$data_array[] = $data;

							// ------------ stock update --------------------	

							$inventory_info = $this->CurrentInventory->find('first', array(
								'conditions' => array(
									'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
									'CurrentInventory.inventory_status_id' => 1,
									'CurrentInventory.product_id' => $val,
									'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
									'CurrentInventory.expire_date' => ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null)
								),
								'recursive' => -1
							));

							$deduct_quantity = $this->request->data['quantity'][$key];

							$update_data['id'] = $inventory_info['CurrentInventory']['id'];
							$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] - $deduct_quantity;
							$update_data['transaction_type_id'] = 17;
							$update_data['transaction_date'] = $this->current_date();

							$update_data_array[] = $update_data;

							// ------------ ReturnChallanDetail  update --------------------			
							$challan_info = $this->ReturnChallanDetail->find('all', array(
								'conditions' => array(
									'ReturnChallanDetail.challan_id' => $this->request->data['Challan']['challan_id'],
									'ReturnChallanDetail.product_id' => $val
								),
								'recursive' => -1
							));
							$challan_qty_for_returnchallan_update = $this->request->data['quantity'][$key];
							foreach ($challan_info as $re_ch_info) {
								$update_challan_data['id'] = $re_ch_info['ReturnChallanDetail']['id'];
								if ($re_ch_info['ReturnChallanDetail']['remaining_qty'] < $challan_qty_for_returnchallan_update) {
									$update_challan_data['remaining_qty'] = 0;
									$challan_qty_for_returnchallan_update = $challan_qty_for_returnchallan_update - $re_ch_info['ReturnChallanDetail']['remaining_qty'];
								} else {
									$update_challan_data['remaining_qty'] = $re_ch_info['ReturnChallanDetail']['remaining_qty'] - $challan_qty_for_returnchallan_update;
									$challan_qty_for_returnchallan_update = $re_ch_info['ReturnChallanDetail']['remaining_qty'] - $challan_qty_for_returnchallan_update;
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
							$this->ReturnChallanDetail->saveAll($update_challan_data_array);
						}
					}


					// Challan close
					if (array_key_exists('save', $this->request->data)) {
						$do_udata['id'] = $this->request->data['Challan']['challan_id'];
						$do_udata['is_close'] = ($this->request->data['Challan']['is_close'] == 1 ? $this->request->data['Challan']['is_close'] : NULL);
						$this->ReturnChallan->save($do_udata);
					}

					$this->Session->setFlash(__('Product issue has been created.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
			}
		}

		$receiverStore = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 3, 'office_id' => $this->UserAuth->getOfficeId()),
			'order' => array('name' => 'asc')
		));

		//$products = $this->Product->find('list',array('order' => array('name'=>'asc')));		
		$this->set(compact('receiverStore', 'ncp_challan_draft_info', 'challan_list', 'product_list'));
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
				'ReturnChallan.transaction_type_id' => 13,
				'ReturnChallan.inventory_status_id' => 2,
				'ReturnChallan.receiver_store_id' => $this->UserAuth->getStoreId(),
				'ReturnChallan.sender_store_id' => $receiver_store_id
			),
			'recursive' => -1
		));
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
		$this->loadModel('ReturnChallanDetail');
		$rs = array(array('id' => '', 'title' => '---- Select Product -----'));
		$challan_id = $this->request->data['challan_id'];
		$do_list = $this->ReturnChallanDetail->find('all', array(
			'fields' => array('DISTINCT Product.id', 'Product.name'),
			'conditions' => array(
				'ReturnChallanDetail.challan_id' => $challan_id
			),
			/*'order' => array('Product.order'=>'asc'),*/
			'recursive' => 0
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
		$this->loadModel('ReturnChallanDetail');
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
				'CurrentInventory.inventory_status_id' => 1,
				//'CurrentInventory.transaction_type_id' => 1,
				'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
			),
			'recursive' => -1
		));

		$data['stock_quantity'] = $stock_info['CurrentInventory']['qty'];

		$challan_info = $this->ReturnChallanDetail->find('first', array(
			'fields' => array('SUM(ReturnChallanDetail.challan_qty) as challan_qty', 'SUM(ReturnChallanDetail.remaining_qty) as remaining_qty'),
			'conditions' => array(
				'ReturnChallanDetail.challan_id' => $challan_id,
				'ReturnChallanDetail.product_id' => $product_id,
			),
			'group' => array('ReturnChallanDetail.product_id'),
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
}
