<?php
App::uses('AppController', 'Controller');
/**
 * Challans Controller
 *
 * @property Challan $Challan
 * @property PaginatorComponent $Paginator
 */
class DoChallansController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Filter.Filter');
	public $uses = array('Challan', 'ChallanDetail', 'Store', 'Product', 'Territory', 'CurrentInventory', 'Requisition', 'RequisitionDetail');
	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->set('page_title', 'DO Challan List');
		$this->Challan->recursive = 0;
		if ($this->UserAuth->getOfficeParentId() == 0) {
			$conditions = array(
				'OR' => array(
					'Challan.transaction_type_id' => 29,
					'Challan.transaction_type_id' => 28,
				)
			);
		} else {
			$conditions = array(
				'AND' => array(
					array(
						'OR' => array(
							array('Challan.transaction_type_id' => 29),
							array('Challan.transaction_type_id' => 28),
						),
					),
					array(
						'OR' => array(
							array('Challan.sender_store_id' => $this->UserAuth->getStoreId()),
							array('Challan.receiver_store_id' => $this->UserAuth->getStoreId()),
						),
					)
				)


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

		if ($this->request->is('post')) {
			$data_array = array();
			$insert_data_array = array();
			$update_data_array = array();
			foreach ($this->request->data['product_id'] as $key => $val) {

				$receive_quantity = $this->unit_convert($val, $this->request->data['measurement_unit_id'][$key], $this->request->data['received_quantity'][$key]);


				// ------------ stock update --------------------			
				$inventory_info = $this->CurrentInventory->find('first', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
						'CurrentInventory.inventory_status_id' => 1,
						'CurrentInventory.product_id' => $val,
						'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
						'CurrentInventory.expire_date' => $this->request->data['expire_date'][$key]
					),
					'recursive' => -1
				));

				if (!empty($inventory_info)) {
					$update_data['id'] = $inventory_info['CurrentInventory']['id'];
					$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] + $receive_quantity;
					$update_data['transaction_type_id'] = 29; // ASO to ASO (DO Challan Received)
					$update_data['transaction_date'] = $this->current_date();
					$update_data_array[] = $update_data;
				} else {
					$insert_data['store_id'] = $this->UserAuth->getStoreId();
					$insert_data['inventory_status_id'] = 1;
					$insert_data['product_id'] = $val;
					$insert_data['batch_number'] = $this->request->data['batch_no'][$key];
					$insert_data['expire_date'] = ($this->request->data['expire_date'][$key] != '' ? $this->request->data['expire_date'][$key] : Null);
					$insert_data['qty'] = $receive_quantity;
					$insert_data['transaction_type_id'] = 29; // ASO to ASO (DO Challan Received)
					$insert_data['transaction_date'] = $this->current_date();
					$insert_data_array[] = $insert_data;
				}
				//----------------- End Stock update ---------------------


				$data['ChallanDetail']['id'] = $this->request->data['id'][$key];
				$data['ChallanDetail']['received_qty'] = $this->request->data['received_quantity'][$key];
				$data_array[] = $data;
			}
			$datasource = $this->Challan->getDataSource();
			try {
				$datasource->begin();
				// insert inventory data
				
				if (!empty($insert_data_array) && !$this->CurrentInventory->saveAll($insert_data_array)) {
					throw new Exception();
				}

				// Update inventory data
				if (!empty($update_data_array) &&!$this->CurrentInventory->saveAll($update_data_array)) {
					throw new Exception();
				}

				// update received quantity
				if (!$this->ChallanDetail->saveAll($data_array)) {
					throw new Exception();
				}

				// update challan status 
				$chalan['id'] = $id;
				$chalan['status'] = 2;
				/*$chalan['received_date'] = $this->current_date();*/
				$chalan['transaction_type_id'] = 29; // ASO to ASO (DO Challan Received)
				$chalan['received_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['Challan']['received_date']));
				$chalan['updated_by'] = $this->UserAuth->getUserId();
				if (!$this->Challan->save($chalan)) {
					throw new Exception();
				}
				$datasource->commit();
				$this->Session->setFlash(__('DO Challan has been received.'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} catch (Exception $e) {
				$datasource->rollback();
				echo $e;exit;
				$this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
				$this->redirect(array('action' => 'view/' . $id));
			}
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
				'fields' => 'ChallanDetail.*,Product.product_code,Product.name,VirtualProduct.product_code,VirtualProduct.name,MeasurementUnit.name',
				'recursive' => 0
			)
		);
		$this->set(compact('challan', 'challandetail', 'office_parent_id'));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->set('page_title', 'DO Challan List');

		if ($this->request->is('post')) {
            //$this->request->data['batch_no'];exit;
            //$this->dd($this->request->data['product_id']);
			if (empty($this->request->data['product_id'])) {
				$this->Session->setFlash(__('DO Challan not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->request->data['Challan']['transaction_type_id'] = 28;  //ASO TO ASO (DO Challans)
				$this->request->data['Challan']['challan_date'] = $this->current_date();
				$this->request->data['Challan']['sender_store_id'] = $this->UserAuth->getStoreId();
				$this->request->data['Challan']['created_at'] = $this->current_datetime();
				$this->request->data['Challan']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['Challan']['status'] = 0;

				$requisition_id = $this->request->data['Challan']['requisition_id'];

				$this->Challan->create();
				if ($this->Challan->save($this->request->data)) {

					$udata['id'] = $this->Challan->id;
					$udata['challan_no'] = 'DC' . (10000 + $this->Challan->id);
					$this->Challan->save($udata);

					if (!empty($this->request->data['product_id'])) {
						$data_array = array();
						$parent_product_array = array();
						foreach ($this->request->data['product_id'] as $key => $val) {
							$data['ChallanDetail']['challan_id'] = $this->Challan->id;
							//$data['ChallanDetail']['product_id'] = $val;

							$productinfo = $this->get_mother_product_info($val);

							if ($productinfo['Product']['parent_id'] > 0) {

								$checkparentpd = $parent_product_array[$productinfo['Product']['parent_id']];
								if (count($checkparentpd) > 0) {
									$pdnamehsow = 1;
								} else {
									$pdnamehsow = $this->get_product_inventroy_check($requisition_id, $productinfo['Product']['parent_id'], $val);
								}


								$data['ChallanDetail']['product_id'] = $productinfo['Product']['parent_id'];
								$data['ChallanDetail']['virtual_product_id'] = $val;
								$data['ChallanDetail']['virtual_product_name_show'] = $pdnamehsow;
							} else {

								$data['ChallanDetail']['product_id'] = $val;
								$data['ChallanDetail']['virtual_product_id'] = 0;
								$data['ChallanDetail']['virtual_product_name_show'] = 0;

								$parent_product_array[$val] = $val;
							}

							$data['ChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
							$data['ChallanDetail']['inventory_status_id'] = 1;
							$data['ChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
							$data['ChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
							$data['ChallanDetail']['expire_date'] = ($this->request->data['expire_date'][$key] != '' ? Date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
							$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
							$data_array[] = $data;

							// ------------ stock update --------------------			
							/*$inventory_info = $this->CurrentInventory->find('first',array(
								'conditions' => array(
													'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
													'CurrentInventory.product_id' => $val,
													'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
													'CurrentInventory.expire_date' => ($this->request->data['expire_date'][$key] !='' ? Date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null)
												),
								'recursive' => -1				
							));						
							
							$update_data['id'] = $inventory_info['CurrentInventory']['id'];
							$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] - $this->request->data['quantity'][$key];
							$update_data_array[] = $update_data;
							
							// ------------ do update --------------------			
							$do_info = $this->RequisitionDetail->find('first',array(
								'conditions' => array(
													'RequisitionDetail.requisition_id' => $this->request->data['Challan']['requisition_id'],
													'RequisitionDetail.product_id' => $val,
													'RequisitionDetail.batch_no' => $this->request->data['batch_no'][$key],
													'RequisitionDetail.expire_date' => ($this->request->data['expire_date'][$key] !='' ? Date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null)
												),
								'recursive' => -1				
							));						
							
							$update_do_data['id'] = $do_info['RequisitionDetail']['id'];
							$update_do_data['remaining_qty'] = $do_info['RequisitionDetail']['remaining_qty'] - $this->request->data['quantity'][$key];
							$update_do_data['update_do_data'] = $this->current_date();
							$update_do_data_array[] = $update_do_data;*/
						}
						// insert challan data
						$this->ChallanDetail->saveAll($data_array);

						/*// Update inventory data
						$this->CurrentInventory->saveAll($update_data_array);
						
						// Update Do quantity data
						$this->RequisitionDetail->saveAll($update_do_data_array);	*/
					}

					// update DO status
					$do_udata['id'] = $this->request->data['Challan']['requisition_id'];
					$do_udata['status'] = ($this->request->data['Requisition']['status'] > 0 ? 2 : 1);
					$this->Requisition->save($do_udata);

					$this->Session->setFlash(__('DO challan has been completed.'), 'flash/success');
					$this->redirect(array('action' => 'edit', $this->Challan->id));
				}
			}
		}

		$receivers = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 2, 'id !=' => $this->UserAuth->getStoreId()),
			'order' => array('name' => 'asc')
		));

		$this->set(compact('receivers'));
	}


	public function admin_edit($id = null)
	{
		if (!$this->Challan->exists($id)) {
			throw new NotFoundException(__('Invalid challan'));
		}
		if ($this->request->is('post')) {
			 //$this->dd($this->request->data);die;
			if (empty($this->request->data['product_id'])) {
				$this->Session->setFlash(__('DO Challan not Updated.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->request->data['Challan']['transaction_type_id'] = 28;   //ASO TO ASO (DO Challans)
				$this->request->data['Challan']['challan_date'] = $this->current_date();
				$this->request->data['Challan']['sender_store_id'] = $this->UserAuth->getStoreId();
				$this->request->data['Challan']['created_at'] = $this->current_datetime();
				$this->request->data['Challan']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['Challan']['id'] = $id;
				if (array_key_exists('draft', $this->request->data)) {
					$this->request->data['Challan']['status'] = 0;
				} else {
					$this->request->data['Challan']['status'] = 1;
				}


				$requisition_id = $this->request->data['Challan']['requisition_id'];
				$datasource = $this->Challan->getDataSource();
				try {
					$datasource->begin();
					if ($this->Challan->save($this->request->data)) {
						if (!$this->Challan->ChallanDetail->deleteAll(array('ChallanDetail.challan_id' => $id), false)) {
							throw new Exception();
						}

						if (!empty($this->request->data['product_id'])) {
							$data_array = array();
							$parent_product_array = array();
							foreach ($this->request->data['product_id'] as $key => $val) {
								$data['ChallanDetail']['challan_id'] = $id;
								$data['ChallanDetail']['product_id'] = $val;

								$productinfo = $this->get_mother_product_info($val);

								if ($productinfo['Product']['parent_id'] > 0) {

									$checkparentpd = $parent_product_array[$productinfo['Product']['parent_id']];
									if (count($checkparentpd) > 0) {
										$pdnamehsow = 1;
									} else {
										$pdnamehsow = $this->get_product_inventroy_check($requisition_id, $productinfo['Product']['parent_id'], $val);
									}

									$pdnamehsow = $this->get_product_inventroy_check($requisition_id, $productinfo['Product']['parent_id'], $val);

									$data['ChallanDetail']['product_id'] = $productinfo['Product']['parent_id'];
									$data['ChallanDetail']['virtual_product_id'] = $val;
									$data['ChallanDetail']['virtual_product_name_show'] = $pdnamehsow;
								} else {

									$data['ChallanDetail']['product_id'] = $val;
									$data['ChallanDetail']['virtual_product_id'] = 0;
									$data['ChallanDetail']['virtual_product_name_show'] = 0;

									$parent_product_array[$val] = $val;
								}

								$data['ChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
								$data['ChallanDetail']['inventory_status_id'] = 1;
								$data['ChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
								$data['ChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
								$data['ChallanDetail']['expire_date'] = ($this->request->data['expire_date'][$key] != '' ? Date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
								$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
								$data_array[] = $data;

								// ------------ stock update --------------------		


								$inventory_info = $this->CurrentInventory->find('first', array(
									'conditions' => array(
										'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
										'CurrentInventory.product_id' => $val,
										'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
										'CurrentInventory.expire_date' => ($this->request->data['expire_date'][$key] != '' ? Date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null)
									),
									'recursive' => -1
								));

								$quantity_deduct = $this->unit_convert($val, $this->request->data['measurement_unit'][$key], $this->request->data['quantity'][$key]);
								$update_data['id'] = $inventory_info['CurrentInventory']['id'];
                                //$this->dd($update_data['qty'] = $inventory_info['CurrentInventory']['qty']);
								$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] - $quantity_deduct;
								$update_data['transaction_type_id'] = 28; // ASO to ASO (DO Challan)
								$update_data['transaction_date'] = $this->current_date();
								$update_data_array[] = $update_data;

								// ------------ do update --------------------	
								//echo $this->request->data['Challan']['requisition_id'];

								if ($productinfo['Product']['parent_id'] > 0) {
									$condi = array(
										'RequisitionDetail.requisition_id' => $this->request->data['Challan']['requisition_id'],
										'RequisitionDetail.virtual_product_id' => $val,
										/*'RequisitionDetail.batch_no' => $this->request->data['batch_no'][$key],
									'RequisitionDetail.expire_date' => ($this->request->data['expire_date'][$key] !='' ? Date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null)*/
									);
								} else {
									$condi = array(
										'RequisitionDetail.requisition_id' => $this->request->data['Challan']['requisition_id'],
										'RequisitionDetail.product_id' => $val,
										/*'RequisitionDetail.batch_no' => $this->request->data['batch_no'][$key],
									'RequisitionDetail.expire_date' => ($this->request->data['expire_date'][$key] !='' ? Date('Y-m-d',strtotime($this->request->data['expire_date'][$key])) : Null)*/
									);
								}

								$do_info = $this->RequisitionDetail->find('first', array(
									'conditions' => $condi,
									'recursive' => -1
								));
								//pr($do_info);die;
								$update_do_data['id'] = $do_info['RequisitionDetail']['id'];
								$update_do_data['remaining_qty'] = $do_info['RequisitionDetail']['remaining_qty'] - $this->request->data['quantity'][$key];
								$update_do_data['update_do_data'] = $this->current_date();
								// $update_do_data_array[] = $update_do_data;
								// 
								if (array_key_exists('save', $this->request->data)) {
									if (!$this->RequisitionDetail->save($update_do_data)) {
										throw new Exception();
									}
								}
							}
							// insert challan data
							if (!$this->ChallanDetail->saveAll($data_array)) {
								throw new Exception();
							}

							// Update inventory data

							if (array_key_exists('draft', $this->request->data)) {
								$this->Session->setFlash(__('DO challan has been Drafted.'), 'flash/success');
							} else {
								if (!$this->CurrentInventory->saveAll($update_data_array)) {
									throw new Exception();
								}

                                //echo $this->CurrentInventory->getLastQuery();exit;
								// Update Do quantity data
								// $this->RequisitionDetail->saveAll($update_do_data_array);
								$this->Session->setFlash(__('DO challan has been Saved.'), 'flash/success');
								// update DO status
								$do_udata['id'] = $this->request->data['Challan']['requisition_id'];
								if ($this->request->data['Requisition']['status'] > 0) {
									$do_udata['status'] = 2;
								} else {
									$chk_remaining_qty = $this->RequisitionDetail->find('all', array(
										'conditions' => array(
											'RequisitionDetail.requisition_id' => $this->request->data['Challan']['requisition_id'],
											'RequisitionDetail.remaining_qty >' => 0
										),
										'recursive' => -1
									));
									if ($chk_remaining_qty) {
										$do_udata['status'] = 1;
									} else {
										$do_udata['status'] = 2;
									}
								}
								if (!$this->Requisition->save($do_udata)) {
									throw new Exception();
								}
							}
						}
						$datasource->commit();
						$this->redirect(array('action' => 'index'));
					} else {
						throw new Exception();
					}
				} catch (Exception $e) {
					$datasource->rollback();
					$this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
					$this->redirect(array('action' => 'edit/' . $id));
				}
			}
		} else {
			$challan_info = $this->Challan->find('all', array(
				/*'fields'=>array('Challan.*','Requisition.*','ChallanDetail.*'),*/
				'conditions' => array('Challan.id' => $id),
				'recursive' => 2
			));
			//pr($challan_info);die;
			$receiver_store_id = $challan_info[0]['Challan']['receiver_store_id'];
			$do = $this->Requisition->find('all', array(
				'fields' => array('Requisition.id as id', 'Requisition.do_no as title', 'Requisition.status as status'),
				'conditions' => array(
					'Requisition.receiver_store_id' => $receiver_store_id,
					'Requisition.sender_store_id' => $this->UserAuth->getStoreId(),
					/*'Requisition.status'=>1	*/
				),
				'recursive' => -1
			));
			$requisitions = array();
			$req_status = array();
			foreach ($do as $data) {
				$requisitions[$data[0]['id']] = $data[0]['title'];
				$req_status[$data[0]['id']] = $data[0]['status'];
			}
			$this->loadModel('RequisitionDetail');
			//$requisition_id = 
			$do_product = $this->RequisitionDetail->find('all', array(
				'fields' => array('Product.id', 'Product.name', 'VirtualProduct.id', 'VirtualProduct.name', 'RequisitionDetail.virtual_product_id'),
				'conditions' => array(
					'RequisitionDetail.requisition_id' => $challan_info[0]['Challan']['requisition_id'],
				),
				'order' => array('Product.order' => 'asc'),
				'recursive' => 0
			));
			$products = array();
			foreach ($do_product as $data) {
				if ($data['RequisitionDetail']['virtual_product_id'] > 0) {
					$products[$data['VirtualProduct']['id']] = $data['VirtualProduct']['name'];
				} else {
					$products[$data['Product']['id']] = $data['Product']['name'];
				}
			}
		}

		$receivers = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 2, 'id !=' => $this->UserAuth->getStoreId()),
			'order' => array('name' => 'asc')
		));

		//pr($challan_info[0]['ChallanDetail']);exit;


		$this->set(compact('receivers', 'challan_info', 'requisitions', 'products', 'req_status'));
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


	/*----------------------- Chainbox Data ---------------------------*/

	public function get_do_list()
	{
		$rs = array(array('id' => '', 'title' => '---- Select DO -----'));
		$receiver_store_id = $this->request->data['receiver_store_id'];
		$do_list = $this->Requisition->find('all', array(
			'fields' => array('Requisition.id as id', 'Requisition.do_no as title'),
			'conditions' => array(
				'Requisition.receiver_store_id' => $receiver_store_id,
				'Requisition.sender_store_id' => $this->UserAuth->getStoreId(),
				'Requisition.status' => 1
			),
			'recursive' => -1
		));
		$data_array = Set::extract($do_list, '{n}.0');
		if (!empty($do_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function get_do_product_list(){
		$this->loadModel('RequisitionDetail');
		$rs = array(array('id' => '', 'title' => '---- Select Product -----'));
		$requisition_id = $this->request->data['requisition_id'];
		$do_list = $this->RequisitionDetail->find('all', array(
			'fields' => array('Product.id', 'Product.name', 'Product.product_type_id', 'VirtualProduct.id', 'VirtualProduct.name', 'RequisitionDetail.virtual_product_name_show'),
			'conditions' => array(
				'RequisitionDetail.requisition_id' => $requisition_id
			),
			'order' => array('Product.order' => 'asc'),
			'recursive' => 0
		));
  
		if (!empty($do_list)) {
			//-------------added by golam rabbi------------\\
			foreach ($do_list as $data) {
                //if ($data['Product']['product_type_id'] == 3)
				if ($data['RequisitionDetail']['virtual_product_id'] > 0) {
					$product_array[] = array(
						'id' => $data['VirtualProduct']['id'],
						'title' => $data['VirtualProduct']['name']
					);
				} else {
					$product_array[] = array(
						'id' => $data['Product']['id'],
						'title' => $data['Product']['name']
					);
				}
			}
   
			if (!empty($product_array)) {
                //$this->dd(array_merge($rs, $product_array));
				echo json_encode(array_merge($rs, $product_array));
			} else {
				echo json_encode($rs);
			}
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function get_do_details()
	{
		$this->loadModel('RequisitionDetail');
		$requisition_id = $this->request->data['requisition_id'];
		$product_id = $this->request->data['product_id'];
		$do_info = $this->RequisitionDetail->find('first', array(
			'conditions' => array(
				'RequisitionDetail.requisition_id' => $requisition_id,
				'RequisitionDetail.product_id' => $product_id
			),
			'recursive' => -1
		));
		echo json_encode($do_info);
		$this->autoRender = false;
	}


	public function get_inventory_details(){
		$requisition_id = $this->request->data['requisition_id'];
		$product_id = $this->request->data['product_id'];
		$batch_no = $this->request->data['batch_no'];
		$expire_date = ($this->request->data['expire_date'] != '' ? $this->request->data['expire_date'] : '0000-00-00');
        if (empty($batch_no) && empty($this->request->data['expire_date'])){
            $batch_no ='';
            $expire_date ='';
        }
		$stock_info = $this->CurrentInventory->find('first', array(
			'fields' => array('CurrentInventory.qty', 'Product.challan_measurement_unit_id'),
			'conditions' => array(
				'CurrentInventory.product_id' => $product_id,
				'CurrentInventory.batch_number' => $batch_no,
				'CurrentInventory.expire_date' => $expire_date,
				'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
			),
			'recursive' => 0
		));

		$data['stock_quantity'] = $this->unit_convertfrombase($product_id, $stock_info['Product']['challan_measurement_unit_id'], $stock_info['CurrentInventory']['qty']);


		//---------------added by golam rabbi---------\\

		$productinfo = $this->get_mother_product_info($product_id);

		//echo '<pre>';print_r($productinfo);exit;

		if ($productinfo['Product']['parent_id'] > 0) {
			$con2 = array(
				'RequisitionDetail.requisition_id' => $requisition_id,
				'RequisitionDetail.virtual_product_id' => $product_id,
				'RequisitionDetail.inventory_status_id' => 1
			);
		} else {
			$con2 = array(
				'RequisitionDetail.requisition_id' => $requisition_id,
				'RequisitionDetail.product_id' => $product_id,
				'RequisitionDetail.inventory_status_id' => 1
			);
		}


		$do_info = $this->RequisitionDetail->find('first', array(
			'fields' => array('RequisitionDetail.remaining_qty'),
			'conditions' => $con2,
			'recursive' => 0
		));

		//----------------end--------------\\

		$data['do_quantity'] = $do_info['RequisitionDetail']['remaining_qty'];

		echo json_encode($data);

		$this->autoRender = false;
	}

	public function get_all_product_with_qty(){
		$this->loadModel('RequisitionDetail');
		$rs = array(
            array('id' => '', 'title' => '---- Select Product -----')
        );
		$requisition_id = $this->request->data['requisition_id'];
		$do_product_list = $this->RequisitionDetail->find('all', array(
			//'fields' => array('RequisitionDetail.*','Product.product_type_id','Product.name','MeasurementUnit.name'),
			'fields' => array('RequisitionDetail.*','Product.name','Product.id','MeasurementUnit.name','MeasurementUnit.id','Product.maintain_batch','Product.is_maintain_expire_date'),
			'conditions' => array(
				'RequisitionDetail.requisition_id' => $requisition_id,
				'RequisitionDetail.remaining_qty >' => 0
			),
			'order' => array('Product.order' => 'asc'),
			'recursive' => 0
		));

        //$this->dd($do_product_list);
       //echo json_encode($do_product_list);
        //$this->autoRender = false;
		$this->set(compact('do_product_list'));
	}


	public function get_mother_product_info($pid)
	{

		$productinfo = $this->Product->find('first', array(
			'conditions' => array(
				'Product.id' => $pid
			),
			'fields' => array('Product.id', 'Product.name', 'Product.product_code', 'Product.is_virtual', 'Product.parent_id'),
			'recursive' => -1
		));

		return $productinfo;
	}

	public function get_product_inventroy_check($requisition_id, $parent_product_id, $virtual_product_id)
	{

		$parentproductcount = $this->RequisitionDetail->find('count', array(
			'fields' => array('RequisitionDetail.id'),
			'conditions' => array(
				'RequisitionDetail.requisition_id' => $requisition_id,
				'RequisitionDetail.product_id' => $parent_product_id,
				'RequisitionDetail.remaining_qty >' => 0
			),
			'order' => array('Product.order' => 'asc'),
			'recursive' => -1
		));

		if (empty($parentproductcount)) {
			$parentproductcount = 0;
		}

		$chilhproductCount = $this->RequisitionDetail->find('count', array(
			'fields' => array('RequisitionDetail.id'),
			'conditions' => array(
				'RequisitionDetail.requisition_id' => $requisition_id,
				'RequisitionDetail.virtual_product_id' => $virtual_product_id,
				'RequisitionDetail.remaining_qty >' => 0
			),
			'order' => array('Product.order' => 'asc'),
			'recursive' => -1
		));

		if ($parentproductcount == 0 and $chilhproductCount == 1) {
			$show = 0;
		} else {
			$show = 1;
		}

		return $show;
	}
}
