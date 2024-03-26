<?php

App::uses('AppController', 'Controller');

/**
 * Challans Controller
 *
 * @property Challan $Challan
 * @property PaginatorComponent $Paginator
 */
class ProductIssuesController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Filter.Filter');
	public $uses = array('Challan', 'ChallanDetail', 'Store', 'Product', 'Territory', 'CurrentInventory', 'SalesPerson', 'User', 'MeasurementUnit');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->set('page_title', 'Product Issue List');


		//Show only Child territory and Territory who has No child
		$this->loadModel('Office');

		$child_territory_parent_ids = $this->Territory->find('list', array(
			'conditions' => array(
				'parent_id !=' => 0,
			),
			'fields' => array('Territory.parent_id', 'Territory.name'),
		));

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions = array('NOT' => array('Territory.id' => array_keys($child_territory_parent_ids)));
		} else {
			$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId(), 'NOT' => array('Territory.id' => array_keys($child_territory_parent_ids)));
		}



		$territory = $this->Territory->find(
			'all',
			array(
				'conditions' => $conditions,
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			)
		);
		$territories = array();
		foreach ($territory as $key => $value) {
			$territories[$value['Territory']['id']] = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
		}
		$this->set(compact('territories'));


		$this->Challan->recursive = 0;
		if ($this->UserAuth->getOfficeParentId() == 0) {
			$conditions = array(
				//'Challan.transaction_type_id' => 2,
				'Challan.inventory_status_id' => 1,
				'OR' => array(
					array('Challan.transaction_type_id' => 2), //ASO TO SO (Product Issue)
					array('Challan.transaction_type_id' => 5), //ASO TO SO (Product Issue received)
				)
			);
		} else {
			$conditions = array(
				//'Challan.transaction_type_id' => 2,
				'Challan.inventory_status_id' => 1,
				'Challan.sender_store_id' => $this->UserAuth->getStoreId(),
				'OR' => array(
					array('Challan.transaction_type_id' => 2), //ASO TO SO (Product Issue)
					array('Challan.transaction_type_id' => 5), //ASO TO SO (Product Issue received)
				)
			);
		}
		$this->paginate = array(
			'conditions' => $conditions,
			'recursive' => 0,
			'order' => array('Challan.id' => 'desc')
		);

		//pr($this->paginate());

		$this->set('challans', $this->paginate());
	}

	/**
	 * admin_view method
	 *
	 * @param string $id
	 * @return void
	 * @throws NotFoundException
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

		$user_name = $this->User->find('first', array(
			'conditions' => array('User.id' => $challan['Challan']['created_by']),
			'recursive' => -1
		));

		$challandetail = $this->ChallanDetail->find(
			'all',
			array(
				'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
				'fields' => 'ChallanDetail.*,Challan.challan_date,VirtualProduct.product_code,VirtualProduct.name,VirtualProduct.parent_id,VirtualProduct.is_virtual,Product.product_code,Product.name,Product.parent_id,Product.is_virtual,MeasurementUnit.name',
				'order' => array('Product.order' => 'asc'),
				'recursive' => 0
			)
		);


		//echo '<pre>';print_r($challandetail);exit;

		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.*', 'st.name', 'st.address', 'Territory.*', 'Office.office_name', 'Office.address'),
			'joins' => array(
				array(
					'table' => 'stores',
					'alias' => 'st',
					'type' => 'LEFT',
					'conditions' => array(
						'SalesPerson.territory_id=st.territory_id'
					)
				)
			),
			'conditions' => array('st.id' => $challan['ReceiverStore']['id']),
			'recursive' => 0
		));

		//pr($so_info); die();

		$this->set(compact('challan', 'challandetail', 'so_info', 'user_name'));
	}

	public function getProductPrice($product_id, $challan_date)
	{
		$this->LoadModel('ProductPrice');
		$product_prices = $this->ProductPrice->find('first', array(
			'conditions' => array(
				'ProductPrice.product_id' => $product_id,
				'ProductPrice.effective_date <=' => $challan_date,
				'ProductPrice.has_combination' => 0,
				'OR' => array('ProductPrice.project_id is null', 'ProductPrice.project_id' => 0),
			),
			'order' => array('ProductPrice.effective_date DESC'),
			'recursive' => -1

		));
		$this->autoRender = false;
		//pr($product_prices);exit;
		return $product_prices['ProductPrice'];
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->set('page_title', 'New Product Issue');

		// pr('here');exit;

		if ($this->request->is('post')) {
			//$this->dd($this->request->data);
			if (empty($this->request->data['product_id'])) {
				$this->Session->setFlash(__('Product issue not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			} else {
				$stock_available = 1;
				foreach ($this->request->data['product_qty'] as $k => $v) {
					if ($this->request->data['product_qty'][$k] < $this->request->data['quantity'][$k]) {
						$stock_available = 0;
						break;
					}
				}

				if (!$stock_available) {
					$this->Session->setFlash(__('Quantity should be less then equal Stock quantity.'), 'flash/error');
					$this->redirect(array('action' => 'index'));
				}
				date_default_timezone_set('Asia/Dhaka');
				$yesterday = date('d-m-Y', strtotime('-1 day'));
				$today = date('d-m-Y');
				$challan_date = $this->request->data['Challan']['challan_date'];

				if ($challan_date != $yesterday && $challan_date != $today) {
					$this->Session->setFlash(__('Challan Date must be yesterday or today.'), 'flash/error');
					$this->redirect(array('action' => 'add'));
					exit;
				}

				$this->request->data['Challan']['transaction_type_id'] = 2; //ASO TO SO (Product Issue)
				$this->request->data['Challan']['inventory_status_id'] = 1;

				$this->request->data['Challan']['challan_date'] = date('Y-m-d', strtotime($this->request->data['Challan']['challan_date']));
				$this->request->data['Challan']['sender_store_id'] = $this->UserAuth->getStoreId();
				$this->request->data['Challan']['updated_by'] = 0;
				$this->request->data['Challan']['created_at'] = $this->current_datetime();
				$this->request->data['Challan']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['Challan']['updated_at'] = $this->current_datetime();
				$this->request->data['Challan']['status'] = 0;

				/*if (array_key_exists('draft', $this->request->data)) {
				  $this->request->data['Challan']['status'] = 0;
				}else{
				  $this->request->data['Challan']['status'] = 1;
				}*/
				$this->Challan->create();
				if ($this->Challan->save($this->request->data)) {

					$udata['id'] = $this->Challan->id;
					$udata['challan_no'] = 'PI' . (10000 + $this->Challan->id);
					$this->Challan->save($udata);
					if (!empty($this->request->data['product_id'])) {
						$data_array = array();
						$update_data_array = array();
						$so_update_data_array = array();
						$insert_data_array = array();
						foreach ($this->request->data['product_id'] as $key => $val) {
							$data['ChallanDetail']['challan_id'] = $this->Challan->id;

							$productinfo = $this->get_mother_product_info($val);

							if ($productinfo['Product']['parent_id'] > 0) {

								$pdnamehsow = $this->get_product_inventroy_check($val, $productinfo['Product']['parent_id']);

								$data['ChallanDetail']['product_id'] = $productinfo['Product']['parent_id'];
								$data['ChallanDetail']['virtual_product_id'] = $val;
								$data['ChallanDetail']['virtual_product_name_show'] = $pdnamehsow;
							} else {

								$data['ChallanDetail']['product_id'] = $val;
								$data['ChallanDetail']['virtual_product_id'] = 0;
								$data['ChallanDetail']['virtual_product_name_show'] = 0;
							}

							$data['ChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
							$data['ChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
							$data['ChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];

							if ($this->request->data['expire_date'][$key] != ' ' && $this->request->data['expire_date'][$key] != 'null' && $this->request->data['expire_date'][$key] != '') {

								$data['ChallanDetail']['expire_date'] = date('Y-m-d', strtotime($this->request->data['expire_date'][$key]));
							} else {
								$data['ChallanDetail']['expire_date'] = '';
							}
							//echo $data['ChallanDetail']['expire_date'];

							$data['ChallanDetail']['inventory_status_id'] = 1;
							$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
							$data['ChallanDetail']['source'] = $this->request->data['source'][$key];
							$data_array[] = $data;

							// ------------ stock update --------------------
							$inventory_info = $this->CurrentInventory->find('first', array(
								'conditions' => array(
									'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
									'CurrentInventory.inventory_status_id' => 1,
									'CurrentInventory.product_id' => $val,
									'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
									'CurrentInventory.expire_date' => $data['ChallanDetail']['expire_date']
								),
								'recursive' => -1
							));

                           // echo $this->CurrentInventory->getLastQuery();exit;
							//print_r($inventory_info);die();

							$deduct_quantity = $this->unit_convert($val, $this->request->data['measurement_unit'][$key], $this->request->data['quantity'][$key]);

							$update_data['id'] = $inventory_info['CurrentInventory']['id'];
							$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] - $deduct_quantity;
							$update_data_array[] = $update_data;

							/* --------------------------- update so inventory ------------------------------ */

							/* $so_inventory_info = $this->CurrentInventory->find('first',array(
							  'conditions' => array(
							  'CurrentInventory.store_id' => $this->request->data['Challan']['receiver_store_id'],
							  'CurrentInventory.inventory_status_id' => 1,
							  'CurrentInventory.product_id' => $val,
							  'CurrentInventory.batch_number' => $this->request->data['batch_no'.$val],
							  'CurrentInventory.expire_date' => ($this->request->data['expire_date'.$val] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'.$val])) : Null)
							  ),
							  'recursive' => -1
							  ));

							  $so_quantity = $this->unit_convert($val,$this->request->data['measurement_unit'.$val],$this->request->data['quantity'.$val]);

							  if(!empty($so_inventory_info))
							  {
							  $so_update_data['id'] = $so_inventory_info['CurrentInventory']['id'];
							  $so_update_data['qty'] = $so_inventory_info['CurrentInventory']['qty'] + $so_quantity;
							  $so_update_data_array[] = $so_update_data;
							  }else{
							  $insert_data['store_id'] = $this->request->data['Challan']['receiver_store_id'];
							  $insert_data['inventory_status_id'] = 1;
							  $insert_data['product_id'] = $val;
							  $insert_data['batch_number'] = $this->request->data['batch_no'.$val];
							  $insert_data['expire_date'] = ($this->request->data['expire_date'.$val] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'.$val])) : Null);
							  $insert_data['qty'] = $so_quantity;
							  $insert_data_array[] = $insert_data;
							} */

							/* ------------------------------ update so inventory --------------------------- */
						}
						// insert challan data
						$this->ChallanDetail->saveAll($data_array);
                        echo $this->ChallanDetail->getLastQuery();exit;


					}
					$this->Session->setFlash(__('Product issue has been Drafted.'), 'flash/success');
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

		$Store = $this->Store->find('all', array(
			'fields' => array('Store.id', 'Store.name', 'sp.name'),
			'conditions' => array(
				'store_type_id' => 3,
				'Store.office_id' => $this->UserAuth->getOfficeId(),
				'NOT' => array('Store.territory_id' => array_keys($child_territory_parent_id)),
				'Store.name NOT LIKE' => '%Corporate%'
			),
			'joins' => array(
				array(
					'table' => 'sales_people',
					'alias' => 'sp',
					'type' => 'INNER',
					'conditions' => array(
						'sp.office_id=Store.office_id AND sp.territory_id=Store.territory_id'
					)
				)
			),
			'order' => array('Store.name' => 'asc'),
			'recursive' => -1
		));

		// echo $this->Store->getLastquery();exit;
		$receiverStore = array();
		foreach ($Store as $data) {
			$receiverStore[$data['Store']['id']] = $data['Store']['name'] . ' (' . $data['sp']['name'] . ' )';
		}
		//pr($receiverStore);die();

		/*
		$products_from_ci = $this->CurrentInventory->find('all', array('fields' => array('DISTINCT CurrentInventory.product_id'),
			'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(),'CurrentInventory.qty > ' => 0)
		));
	  */

		// echo $this->UserAuth->getStoreId();exit;

		// $products_from_ci = $this->CurrentInventory->find('all', array(
		// 	'fields' => array('CurrentInventory.product_id'),
		// 	'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId()),
		// 	'group' => 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)>0'
		// ));



		// $currentproductlist = $products_from_ci;

		// $product_ci = array();
		// $product_ci2 = array();
		// foreach ($products_from_ci as $each_ci) {
		// 	$product_ci2[] = $each_ci['CurrentInventory']['product_id'];
		// 	$product_ci[$each_ci['CurrentInventory']['product_id']] = $each_ci['CurrentInventory']['product_id'];
		// }

		// //echo '<pre>';print_r($product_ci);exit;

		// $productlist = array();
		// foreach ($currentproductlist as $pdid) {

		// 	$productid = $pdid['CurrentInventory']['product_id'];

		// 	$productinfo = $this->Product->find('first', array(
		// 		'conditions' => array(
		// 			'Product.id' => $productid
		// 		),
		// 		'fields' => array('Product.id', 'Product.name', 'Product.is_virtual', 'Product.parent_id'),
		// 		'recursive' => -1
		// 	));

		// 	//echo '<pre>';print_r($pinfo);exit;

		// 	if ($productinfo['Product']['is_virtual'] > 0 and $productinfo['Product']['parent_id'] > 0) {

		// 		$virtualproductid = $productinfo['Product']['parent_id'];

		// 		$motherproductinfo = $this->get_mother_product_info($virtualproductid);

		// 		$virtualproductcount = $this->get_virtual_product_count($virtualproductid);

		// 		$motherproductcheck = $product_ci[$virtualproductid];

		// 		if (empty($motherproductcheck) and $virtualproductcount < 2) {
		// 			$productlist[$productinfo['Product']['id']] = $motherproductinfo['Product']['name'];
		// 		} else {
		// 			$productlist[$productinfo['Product']['id']] = $productinfo['Product']['name'];
		// 		}
		// 	} else {
		// 		$productlist[$productinfo['Product']['id']] = $productinfo['Product']['name'];
		// 	}
		// }

		// // $products = $productlist;
		// $product_lists = $this->Product->find('list', array(
		// 	'conditions' => array('Product.id' => array_keys($productlist)),
		// 	'order' => array('Product.order asc')
		// ));

		// $products = array();
		// foreach ($product_lists as $key => $name) {
		// 	$products[$key] = $productlist[$key];
		// }


		// $product_ci_in = implode(",", $product_ci2);
		//$products = $this->Product->find('list', array('conditions' => array('Product.id' => $product_ci), 'order' => array('order' => 'asc')));

		// $this->set(compact('receiverStore', 'products'));
		$this->set(compact('receiverStore'));
	}

	public function get_product_inventroy_check($vpid, $parent_product_id)
	{

		$parentproductcount = $this->CurrentInventory->find('count', array(
			'fields' => array('CurrentInventory.product_id'),
			'conditions' => array(
				'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
				'CurrentInventory.product_id' => $parent_product_id
			),
			'group' => 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)>0',
			'recursive' => -1
		));

		$chilhproductCount = $this->CurrentInventory->find('count', array(
			'fields' => array('CurrentInventory.product_id'),
			'conditions' => array(
				'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
				'Product.parent_id' => $parent_product_id
			),
			'joins' => array(
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => 'Product.id = CurrentInventory.product_id'
				)
			),
			'group' => 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)>0',
			'recursive' => -1
		));

		if (empty($parentproductcount)) {
			$parentproductcount = 0;
		}

		if (empty($chilhproductCount)) {
			$chilhproductCount = 0;
		}

		if ($parentproductcount == 0 and $chilhproductCount == 1) {
			$show = 0;
		} else {
			$show = 1;
		}

		return $show;
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
	public function get_virtual_product_count($pid)
	{

		$productCount = $this->CurrentInventory->find('count', array(
			'fields' => array('CurrentInventory.product_id'),
			'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'Product.parent_id' => $pid),
			'joins' => array(
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => 'Product.id = CurrentInventory.product_id'
				)
			),
			'group' => 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)>0',
			'recursive' => -1
		));

		return $productCount;
	}


	public function get_territory_wise_group()
	{

		$request_data = $this->request->data;
		$rs = array(array('id' => '', 'name' => '---- Select Product -----'));
		// pr($request_data);exit;

		/***Show only Virtual (Child Territory) and No Child Territory ***/

		$this->loadModel('Territory');

		$child_territory_parent_id = $this->Territory->find('list', array(
			'conditions' => array(
				'parent_id !=' => 0,
			),
			'fields' => array('Territory.parent_id', 'Territory.name'),
		));

		$Store = $this->Store->find('all', array(
			'fields' => array('Store.id', 'Store.name', 'sp.name'),
			'conditions' => array(
				'store_type_id' => 3,
				'Store.office_id' => $this->UserAuth->getOfficeId(),
				'NOT' => array('Store.territory_id' => array_keys($child_territory_parent_id)),
				'Store.name NOT LIKE' => '%Corporate%'
			),
			'joins' => array(
				array(
					'table' => 'sales_people',
					'alias' => 'sp',
					'type' => 'INNER',
					'conditions' => array(
						'sp.office_id=Store.office_id AND sp.territory_id=Store.territory_id'
					)
				)
			),
			'order' => array('Store.name' => 'asc'),
			'recursive' => -1
		));


		$receiverStore = array();
		foreach ($Store as $data) {
			$receiverStore[$data['Store']['id']] = $data['Store']['name'] . ' (' . $data['sp']['name'] . ' )';
		}


		$products_from_ci = $this->CurrentInventory->find('all', array(
			'fields' => array('CurrentInventory.product_id'),
			'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId()),
			'group' => 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)>0'
		));


		$currentproductlist = $products_from_ci;

		$product_ci = array();
		$product_ci2 = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci2[] = $each_ci['CurrentInventory']['product_id'];
			$product_ci[$each_ci['CurrentInventory']['product_id']] = $each_ci['CurrentInventory']['product_id'];
		}



		$productlist = array();
		foreach ($currentproductlist as $pdid) {

			$productid = $pdid['CurrentInventory']['product_id'];

			$productinfo = $this->Product->find('first', array(
				'conditions' => array(
					'Product.id' => $productid
				),
				'fields' => array('Product.id', 'Product.name', 'Product.is_virtual', 'Product.parent_id'),
				'recursive' => -1
			));



			if ($productinfo['Product']['is_virtual'] > 0 and $productinfo['Product']['parent_id'] > 0) {

				$virtualproductid = $productinfo['Product']['parent_id'];

				$motherproductinfo = $this->get_mother_product_info($virtualproductid);

				$virtualproductcount = $this->get_virtual_product_count($virtualproductid);

				$motherproductcheck = $product_ci[$virtualproductid];

				if (empty($motherproductcheck) and $virtualproductcount < 2) {
					$productlist[$productinfo['Product']['id']] = $motherproductinfo['Product']['name'];
				} else {
					$productlist[$productinfo['Product']['id']] = $productinfo['Product']['name'];
				}
			} else {
				$productlist[$productinfo['Product']['id']] = $productinfo['Product']['name'];
			}
		}

		/* Start Product Group start */

		$store_id = $request_data['store_id'];

		$territory_id_arr = $this->Store->find('first', array(
			'conditions' => array(
				'store.id' => $store_id
			),
			'fields' => array('Store.territory_id'),
			'recursive' => -1
		));


		$territory_id =  $territory_id_arr['Store']['territory_id'];

		$this->loadModel('TerritoryProductGroup');

		$territory_id_product_group = $this->TerritoryProductGroup->find('all', array(
			'fields' => array('TerritoryProductGroup.product_group_id'),
			'conditions' => array(
				'TerritoryProductGroup.territory_id' => $territory_id,

			),
			'recursive' => 0
		));



		$product_array = array();

		foreach ($territory_id_product_group as $key => $product) {

			$product_array[$product['TerritoryProductGroup']['product_group_id']] = $product['TerritoryProductGroup']['product_group_id'];
		}

		/* Start Product Group End */



		$product_lists = $this->Product->find('list', array(
			'conditions' => array('Product.id' => array_keys($productlist), 'Product.group_id' => array_keys($product_array)),
			'order' => array('Product.order asc')
		));

		$products = array();
		// foreach ($product_lists as $key => $name) {
		// 	$products[$key] = $productlist[$key];
		// }



		foreach ($product_lists as $key => $name) {
			// $products[$key] = $productlist[$key];
			$products[] = array(
				'id' => $key,
				'name' => $productlist[$key]
			);
		}

		if (!empty($products)) {
			echo json_encode(array_merge($rs, $products));
		} else {
			echo json_encode($rs);
		}

		$this->autoRender = false;
	}



	/**
	 * admin_delete method
	 *
	 * @param string $id
	 * @return void
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
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

	public function admin_edit($id = null)
	{
		$this->set('page_title', 'Edit Product Issue');
		$stock_available = 1;
		$this->Challan->unbindModel(
			array('belongsTo' => array('TransactionType', 'SenderStore', 'ReceiverStore', 'Requisition'))
		);
		$challan_info = $this->Challan->find('first', array(
			'conditions' => array('Challan.id' => $id),
			'recursive' => 1
		));
		foreach ($challan_info['ChallanDetail'] as $key => $data) {

			$measurement_unit = $this->MeasurementUnit->find('first', array(
				'conditions' => array('MeasurementUnit.id' => $data['measurement_unit_id']),
				'recursive' => -1
			));
			$challan_info['ChallanDetail'][$key]['MeasurementUnit'] = $measurement_unit['MeasurementUnit'];

			$product = $this->Product->find('first', array(
				'conditions' => array('Product.id' => $data['product_id']),
				'recursive' => -1
			));

			$challan_info['ChallanDetail'][$key]['Product'] = $product['Product'];

			if ($data['virtual_product_id'] > 0) {

				$vproduct = $this->Product->find('first', array(
					'conditions' => array('Product.id' => $data['virtual_product_id']),
					'recursive' => -1
				));

				$challan_info['ChallanDetail'][$key]['VirtualProduct'] = $vproduct['Product'];
			}
		}


		// pr($challan_info);exit;
		if ($challan_info['Challan']['status'] > 0) {
			$this->Session->setFlash(__('Product issue already has been Updated.'), 'flash/warning');
			$this->redirect(array('action' => 'index'));
		}
		if ($this->request->is('post')) {
			//pr($this->request->data);exit;
			$stock_avail = 1;
			foreach ($this->request->data['product_qty'] as $k => $v) {
				if ($this->request->data['product_qty'][$k] < $this->request->data['quantity'][$k]) {
					$stock_avail = 0;
					break;
				}
			}

			if (!$stock_avail) {
				$this->Session->setFlash(__('Quantity should be less then equal Stock quantity.'), 'flash/error');
				$this->redirect(array('action' => 'edit', $id));
			}


			$store_id = $challan_info['Challan']['sender_store_id'];
			$no_of_product = count($challan_info['ChallanDetail']);

			/*if (!array_key_exists('draft', $this->request->data)) {
			for ($challan_detail_count=0; $challan_detail_count < $no_of_product; $challan_detail_count++) {
			$product_id = $challan_info['ChallanDetail'][$challan_detail_count]['product_id'];
			$sales_qty = $challan_info['ChallanDetail'][$challan_detail_count]['challan_qty'];
			$measurement_unit_id = $challan_info['ChallanDetail'][$challan_detail_count]['measurement_unit_id'];
			$base_quantity = $this->unit_convert($product_id,$measurement_unit_id,$sales_qty);
			$update_type = 'add';
			$this->update_current_inventory($base_quantity,$product_id,$store_id,$update_type);
			}
			}*/

			$challan_id = $id;
			date_default_timezone_set('Asia/Dhaka');
			$yesterday = date('Y-m-d', strtotime('-1 day'));
			$today = date('Y-m-d');
			$challan_date = $this->request->data['Challan']['challan_date'];
			if ($challan_date != $yesterday && $challan_date != $today) {
				$this->Session->setFlash(__('Challan Date must be yesterday or today.'), 'flash/error');
				$this->redirect(array('action' => 'edit', $challan_id));
				exit;
			}

			// $this->ChallanDetail->deleteAll(array('ChallanDetail.challan_id'=>$challan_id));

			if (empty($this->request->data['product_id'])) {
				$this->Session->setFlash(__('Product issue not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			} else {

				$this->request->data['Challan']['transaction_type_id'] = 2; //ASO TO SO (Product Issue)
				$this->request->data['Challan']['inventory_status_id'] = 1;

				$this->request->data['Challan']['challan_date'] = date('Y-m-d', strtotime($this->request->data['Challan']['challan_date']));
				$this->request->data['Challan']['sender_store_id'] = $this->UserAuth->getStoreId();
				$this->request->data['Challan']['updated_at'] = $this->current_datetime();
				$this->request->data['Challan']['updated_by'] = $this->UserAuth->getUserId();

				$challan_update_set_sql = "
                receiver_store_id=" . $this->request->data['Challan']['receiver_store_id'] . ",
                sender_store_id=" . $this->request->data['Challan']['sender_store_id'] . ",
                challan_date = '" . $this->request->data['Challan']['challan_date'] . "',
                remarks = '" . $this->request->data['Challan']['remarks'] . "',
                carried_by = '" . $this->request->data['Challan']['carried_by'] . "',
                truck_no = '" . $this->request->data['Challan']['truck_no'] . "',
                driver_name = '" . $this->request->data['Challan']['driver_name'] . "',
                inventory_status_id = '" . $this->request->data['Challan']['inventory_status_id'] . "',
                transaction_type_id = '" . $this->request->data['Challan']['transaction_type_id'] . "',
                updated_at = '" . $this->request->data['Challan']['updated_at'] . "',
                updated_by = '" . $this->request->data['Challan']['updated_by'] . "',
              ";
				/*$challan_update_set_sql=array(
					'receiver_store_id'=>$this->request->data['Challan']['receiver_store_id'],
					'sender_store_id'=>$this->request->data['Challan']['sender_store_id'],
					'challan_date' =>"'".$this->request->data['Challan']['challan_date']."'",
					'remarks' => "'".$this->request->data['Challan']['remarks']."'",
					'carried_by' => "'".$this->request->data['Challan']['carried_by']."'",
					'truck_no' => "'".$this->request->data['Challan']['truck_no']."'",
					'driver_name' => "'".$this->request->data['Challan']['driver_name']."'",
					'inventory_status_id' => "'".$this->request->data['Challan']['inventory_status_id']."'",
					'transaction_type_id' => "'".$this->request->data['Challan']['transaction_type_id']."'",
					'updated_at' => "'".$this->request->data['Challan']['updated_at']."'",
					'updated_by' => "'".$this->request->data['Challan']['updated_by']."'",
				  );*/
				$challan_update_conditions = "id=$id";
				// $challan_update_conditions=array("id"=>$id);
				if (array_key_exists('draft', $this->request->data)) {
					$this->request->data['Challan']['status'] = 0;
					// $challan_update_set_sql['status']=0;
					$challan_update_set_sql .= "status=0";
				} else {
					$this->request->data['Challan']['status'] = 1;
					$challan_update_set_sql .= "status=1";
					//$challan_update_set_sql['status']=1;
					$challan_update_conditions .= "AND status=0";
					//$challan_update_conditions['status']=0;
				}
				$prev_challan_status = $this->Challan->query("SELECT * FROM challans WHERE $challan_update_conditions");
				$datasource = $this->Challan->getDataSource();
				try {
					$datasource->begin();
					$challan_update = 0;
					if ($prev_challan_status) {
						if (!$challan_update = $this->Challan->query("UPDATE challans set $challan_update_set_sql WHERE $challan_update_conditions")) {
							throw new Exception();
						}
					}
					// $this->request->data['Challan']['id'] = $id;
					// if ($this->Challan->save($this->request->data))
					if ($challan_update) {
						if (!$this->ChallanDetail->deleteAll(array('ChallanDetail.challan_id' => $challan_id))) {
							throw new Exception();
						}
						// $udata['id'] = $this->Challan->id;
						$udata['id'] = $id;
						// $udata['challan_no'] = 'PI' . (10000 + $this->Challan->id);
						$udata['challan_no'] = 'PI' . (10000 + $id);
						if (!$this->Challan->save($udata)) {
							throw new Exception();
						}
						if (!empty($this->request->data['product_id'])) {
							$data_array = array();
							$update_data_array = array();
							$so_update_data_array = array();
							$insert_data_array = array();

							$parent_product_liast_array = array();
							foreach ($this->request->data['product_id'] as $key => $val) {
								$data_array = array();
								$update_data_array = array();
								$data['ChallanDetail']['challan_id'] = $id;

								$productinfo = $this->get_mother_product_info($val);

								if ($productinfo['Product']['parent_id'] > 0) {

									$checkparentpd = $parent_product_liast_array[$productinfo['Product']['parent_id']];

									if (count($checkparentpd) > 0) {
										$pdnamehsow = 1;
									} else {
										$pdnamehsow = $this->get_product_inventroy_check($val, $productinfo['Product']['parent_id']);
									}

									$data['ChallanDetail']['product_id'] = $productinfo['Product']['parent_id'];
									$data['ChallanDetail']['virtual_product_id'] = $val;
									$data['ChallanDetail']['virtual_product_name_show'] = $pdnamehsow;
								} else {

									$data['ChallanDetail']['product_id'] = $val;
									$data['ChallanDetail']['virtual_product_id'] = 0;
									$data['ChallanDetail']['virtual_product_name_show'] = 0;

									$parent_product_liast_array[$val] = $val;
								}

								//$data['ChallanDetail']['product_id'] = $val;
								$data['ChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
								$data['ChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
								$data['ChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
								//$data['ChallanDetail']['expire_date'] = ($this->request->data['expire_date'][$key] != '' && $this->request->data['expire_date'][$key] != 'null') ? date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : ' ';
								//echo $this->request->data['expire_date'][$key];
								if ($this->request->data['expire_date'][$key] != ' ' && $this->request->data['expire_date'][$key] != 'null' && $this->request->data['expire_date'][$key] != '') {
									$data['ChallanDetail']['expire_date'] = date('Y-m-d', strtotime($this->request->data['expire_date'][$key]));
								} else {
									$data['ChallanDetail']['expire_date'] = '';
								}
								//echo $data['ChallanDetail']['expire_date'] = ' ';
								$data['ChallanDetail']['inventory_status_id'] = 1;
								$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
								$data['ChallanDetail']['source'] = $this->request->data['source'][$key];
								$data_array[] = $data;
								if (array_key_exists('draft', $this->request->data)) {
									// insert challan data
									if (!$this->ChallanDetail->saveAll($data_array)) {
										throw new Exception();
									}
								} else {
									$stock_available = 1;
									// ------------ stock update --------------------
									$inventory_info = $this->CurrentInventory->find('first', array(
										'conditions' => array(
											'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
											'CurrentInventory.inventory_status_id' => 1,
											'CurrentInventory.product_id' => $val,
											'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
											'CurrentInventory.expire_date' => $data['ChallanDetail']['expire_date']
										),
										'recursive' => -1
									));

									$deduct_quantity = $this->unit_convert($val, $this->request->data['measurement_unit'][$key], $this->request->data['quantity'][$key]);

									$update_data['id'] = $inventory_info['CurrentInventory']['id'];
									$update_data['qty'] = $inventory_info['CurrentInventory']['qty'] - $deduct_quantity;
									if ($update_data['qty'] < 0) {
										$stock_available = 0;
									}
									$update_data['transaction_type_id'] = 2; // ASO to SO
									$update_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['Challan']['challan_date']));
									$update_data_array[] = $update_data;

									if ($stock_available) {
										// Update inventory data
										if (!$this->CurrentInventory->saveAll($update_data_array)) {
											throw new Exception();
										}
										if (!$this->ChallanDetail->saveAll($data_array)) {
											throw new Exception();
										}
									}
								}
								/* --------------------------- update so inventory ------------------------------ */

								/* $so_inventory_info = $this->CurrentInventory->find('first',array(
								'conditions' => array(
								'CurrentInventory.store_id' => $this->request->data['Challan']['receiver_store_id'],
								'CurrentInventory.inventory_status_id' => 1,
								'CurrentInventory.product_id' => $val,
								'CurrentInventory.batch_number' => $this->request->data['batch_no'.$val],
								'CurrentInventory.expire_date' => ($this->request->data['expire_date'.$val] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'.$val])) : Null)
								),
								'recursive' => -1
								));

								$so_quantity = $this->unit_convert($val,$this->request->data['measurement_unit'.$val],$this->request->data['quantity'.$val]);

								if(!empty($so_inventory_info))
								{
								$so_update_data['id'] = $so_inventory_info['CurrentInventory']['id'];
								$so_update_data['qty'] = $so_inventory_info['CurrentInventory']['qty'] + $so_quantity;
								$so_update_data_array[] = $so_update_data;
								}else{
								$insert_data['store_id'] = $this->request->data['Challan']['receiver_store_id'];
								$insert_data['inventory_status_id'] = 1;
								$insert_data['product_id'] = $val;
								$insert_data['batch_number'] = $this->request->data['batch_no'.$val];
								$insert_data['expire_date'] = ($this->request->data['expire_date'.$val] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'.$val])) : Null);
								$insert_data['qty'] = $so_quantity;
								$insert_data_array[] = $insert_data;
								} */
								/* ------------------------------ update so inventory --------------------------- */
							}

							// insert challan data

							/*if (array_key_exists('draft', $this->request->data))
							{
							// insert challan data
							$this->ChallanDetail->saveAll($data_array);
							}
							else
							{
							// insert challan data
							$this->ChallanDetail->saveAll($data_array);

							if($stock_available)
							{
							// Update inventory data
							$this->CurrentInventory->saveAll($update_data_array);
							}
							else
							{
								$challan_update=array();
								$challan_update['id'] = $this->Challan->id;
								$challan_update['status']= 0;
								$this->Challan->save($challan_update);
								pr($update_data_array);exit;
								$this->Session->setFlash(__('Issued Product Quantity not Available.'), 'flash/error');
								$this->redirect(array('action' => 'edit',$this->Challan->id));
							}
							}*/
						}
						$datasource->commit();
						$this->Session->setFlash(__('Product issue has been Updated.'), 'flash/success');
						$this->redirect(array('action' => 'index'));
					} else {
						$this->Session->setFlash(__('Product issue Already Submitted.'), 'flash/warning');
						$this->redirect(array('action' => 'index'));
					}
				} catch (Exception $e) {
					$datasource->rollback();
					$this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
					$this->redirect(array('action' => 'edit/' . $id));
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

		$Store = $this->Store->find('all', array(
			'fields' => array('Store.id', 'Store.name', 'sp.name'),
			'conditions' => array(
				'store_type_id' => 3,
				'NOT' => array('Store.territory_id' => array_keys($child_territory_parent_id)),
				'Store.office_id' => $this->UserAuth->getOfficeId(),
				'Store.name NOT LIKE' => '%Corporate%'
			),
			'joins' => array(
				array(
					'table' => 'sales_people',
					'alias' => 'sp',
					'type' => 'INNER',
					'conditions' => array(
						'sp.office_id=Store.office_id AND sp.territory_id=Store.territory_id'
					)
				)
			),
			'order' => array('Store.name' => 'asc'),
			'recursive' => -1
		));
		$receiverStore = array();
		foreach ($Store as $data) {
			$receiverStore[$data['Store']['id']] = $data['Store']['name'] . ' (' . $data['sp']['name'] . ' )';
		}


		/*
		$products_from_ci = $this->CurrentInventory->find('all', array('fields' => array('DISTINCT CurrentInventory.product_id'),
			'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'CurrentInventory.qty > ' => 0)
		));

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci[] = $each_ci['CurrentInventory']['product_id'];
		}

		$product_ci_in = implode(",", $product_ci);
		$products = $this->Product->find('list', array('conditions' => array('Product.id' => $product_ci), 'order' => array('order' => 'asc')));
		//$products = $this->Product->find('list', array('order' => array('order' => 'asc')));
		*/

		$products_from_ci = $this->CurrentInventory->find('all', array(
			'fields' => array('CurrentInventory.product_id'),
			'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId()),
			'group' => 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)>0'
		));

		$currentproductlist = $products_from_ci;

		$product_ci = array();
		$product_ci2 = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci2[] = $each_ci['CurrentInventory']['product_id'];
			$product_ci[$each_ci['CurrentInventory']['product_id']] = $each_ci['CurrentInventory']['product_id'];
		}

		$productlist = array();
		foreach ($currentproductlist as $pdid) {

			$productid = $pdid['CurrentInventory']['product_id'];

			$productinfo = $this->Product->find('first', array(
				'conditions' => array(
					'Product.id' => $productid
				),
				'fields' => array('Product.id', 'Product.name', 'Product.is_virtual', 'Product.parent_id'),
				'recursive' => -1
			));

			//echo '<pre>';print_r($pinfo);exit;

			if ($productinfo['Product']['is_virtual'] > 0 and $productinfo['Product']['parent_id'] > 0) {

				$virtualproductid = $productinfo['Product']['parent_id'];

				$motherproductinfo = $this->get_mother_product_info($virtualproductid);

				$virtualproductcount = $this->get_virtual_product_count($virtualproductid);

				$motherproductcheck = $product_ci[$virtualproductid];

				if (empty($motherproductcheck) and $virtualproductcount < 2) {
					$productlist[$productinfo['Product']['id']] = $motherproductinfo['Product']['name'];
				} else {
					$productlist[$productinfo['Product']['id']] = $productinfo['Product']['name'];
				}
			} else {
				$productlist[$productinfo['Product']['id']] = $productinfo['Product']['name'];
			}
		}

		$product_lists = $this->Product->find('list', array(
			'conditions' => array('Product.id' => array_keys($productlist)),
			'order' => array('Product.order asc')
		));

		$products = array();
		foreach ($product_lists as $key => $name) {
			$products[$key] = $productlist[$key];
		}

		//echo '<pre>';print_r($productlist);exit;

		$product_ci_in = implode(",", $product_ci2);

		$product_source = $this->Product->find('list', array('fields' => 'source'));


		foreach ($challan_info['ChallanDetail'] as $key => $value) {

			//echo '<pre>info';print_r($value['product_id']);

			if ($value['virtual_product_id'] > 0) {
				$value['product_id'] = $value['virtual_product_id'];
			}

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


			$challan_info['ChallanDetail'][$key]['stock_qty'] = $this->unit_convertfrombase($value['product_id'], $value['Product']['challan_measurement_unit_id'], $current_inventory_info['CurrentInventory']['qty']);
		}
		// redirect to index page if previously submitted this challan to server

		$this->set(compact('receiverStore', 'products', 'challan_info', 'product_source'));
	}

	public function update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct')
	{
		$this->loadModel('CurrentInventory');

		$find_type = 'all';
		if ($update_type == 'add')
			$find_type = 'first';
		$inventory_info = $this->CurrentInventory->find($find_type, array(
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_id,
				'CurrentInventory.inventory_status_id' => 1,
				'CurrentInventory.product_id' => $product_id
			),
			'order' => array('CurrentInventory.expire_date' => 'asc'),
			'recursive' => -1
		));

		if ($update_type == 'deduct') {
			foreach ($inventory_info as $val) {
				if ($quantity <= $val['CurrentInventory']['qty']) {
					$this->CurrentInventory->id = $val['CurrentInventory']['id'];
					$this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty - ' . $quantity), array('CurrentInventory.id' => $val['CurrentInventory']['id']));
					break;
				} else {
					$quantity = $quantity - $val['CurrentInventory']['qty'];
					$this->CurrentInventory->id = $val['CurrentInventory']['id'];
					$this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty - ' . $val['CurrentInventory']['qty']), array('CurrentInventory.id' => $val['CurrentInventory']['id']));
				}
			}
		} else {
			/*$this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty + '.$inventory_info['CurrentInventory']['qty']),array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id']));*/

			$this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty + ' . $quantity), array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id']));
		}
		return true;
	}


	//for challan referance number check
	public function admin_challan_validation()
	{

		$data_array = array();

		if ($this->request->is('post')) {
			$receiver_store_id = $this->request->data['receiver_store_id'];


			$con = array('Challan.receiver_store_id' => $receiver_store_id);


			$challan_list = $this->Challan->find('all', array(
				'conditions' => $con,
				'fields' => array('id', 'challan_no', 'challan_date'),
				'order' => array('id' => 'desc'),
				'limit' => 1,
				'recursive' => -1
			));

			//$challan_list = count($challan_list);


			foreach ($challan_list as $list) {
				$data_array['id'] = $list['Challan']['id'];
				$data_array['challan_no'] = $list['Challan']['challan_date'];
				$data_array['challan_date'] = date('d-M, Y', strtotime($list['Challan']['challan_date']));
			}

			//pr($data_array);

			echo json_encode($data_array);
		}

		$this->autoRender = false;
	}


	public function get_remarks_product_id_munit_id($product_id, $unit_id, $min_qty)
    {
        $this->loadModel('Product');
        $products_info = $this->Product->find('first', array('conditions' => array('Product.id' => $product_id), 'recursive' => -1));
        $measurement_unit_id = $products_info['Product']['sales_measurement_unit_id'];
        $base_measurement_unit_id = $products_info['Product']['base_measurement_unit_id'];

        $base_qty = $this->convert_unit_to_unit($product_id, $unit_id, $base_measurement_unit_id, $min_qty);
        $cartoon_qty = $this->unit_convertfrombase($product_id, 16, $base_qty);

        $cartoon = explode('.', $cartoon_qty);
        $cartoon_qty = $cartoon[0];
        if ($cartoon_qty <= 0) {
            $this->loadModel('MeasurementUnit');
            $meauserment_unit = $this->MeasurementUnit->find('first', array(
                'conditions' => array(
                    'MeasurementUnit.id' => $measurement_unit_id
                ),
                'recursive' => -1
            ));
            $measurement_unit_name = $meauserment_unit['MeasurementUnit']['name'];
            if (strlen($measurement_unit_name) > 4) {
                $measurement_unit_name = substr($measurement_unit_name, 0, 4) . '.';
            }
            $dispenser = $this->unit_convertfrombase($product_id, $measurement_unit_id, $base_qty);
        } else {
            if ($cartoon[1] != '00' && $cartoon[1]) {
                $this->loadModel('MeasurementUnit');
                $meauserment_unit = $this->MeasurementUnit->find('first', array(
                    'conditions' => array(
                        'MeasurementUnit.id' => $measurement_unit_id
                    ),
                    'recursive' => -1
                ));
                $measurement_unit_name = $meauserment_unit['MeasurementUnit']['name'];
                if (strlen($measurement_unit_name) > 4) {
                    $measurement_unit_name = substr($measurement_unit_name, 0, 4) . '.';
                }
                $base_qty = $this->unit_convert($product_id, 16, '.' . $cartoon[1]);
                $dispenser = $this->unit_convertfrombase($product_id, $measurement_unit_id, $base_qty);
            }
        }

        $result_data = array();
        $result_data['remarks'] = '';
        if ($cartoon_qty)
            $result_data['remarks'] .= $cartoon_qty . " S/c";
        if (isset($dispenser)) {
            $result_data['remarks'] .= ($result_data['remarks'] ? ', ' : '') . $dispenser . " $measurement_unit_name";
        }
        $this->autoRender = false;
        return $result_data['remarks'];
    }




}
