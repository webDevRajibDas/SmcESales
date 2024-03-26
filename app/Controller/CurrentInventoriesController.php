<?php

App::uses('AppController', 'Controller');

/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class CurrentInventoriesController extends AppController{
	public $components = array('Paginator', 'Filter.Filter');
	public $uses = array('CurrentInventory', 'Store', 'ProductType');

	public function admin_index(){
		$this->set('page_title', 'Current Inventories');
        //pr($this->UserAuth->getStoreId());exit;
		$this->CurrentInventory->recursive = 1;
		$this->loadModel('Store');
		$this->loadModel('InventoryStatuses');
		$this->loadModel('ProductCategory');
		$this->loadModel('ProductType');
		$this->loadModel('SalesPerson');

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		//echo $this->UserAuth->getStoreId(); exit;
        //pr($this->UserAuth->getOfficeParentId());exit;
        
		if ($office_parent_id == 0) {
			$conditions = array('CurrentInventory.inventory_status_id !=' => 2);
			$storeCondition = array();
		} else {
			$conditions = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'CurrentInventory.inventory_status_id !=' => 2);
			$storeCondition = array('Store.office_id' => $this->UserAuth->getOfficeId());
		}
		//$conditions = array('Product.product_category_id' => 20); 
  
		$this->paginate = array(
			//'conditions' => array('CurrentInventory.store_id'=>$this->UserAuth->getStoreId()),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'product_categories',
					'alias' => 'ProductCategory',
					'type' => 'INNER',
					'conditions' => array(
						'Product.product_category_id = ProductCategory.id'
					)
				)
			),
			'fields' => array('CurrentInventory.product_id', 'CurrentInventory.store_id', 'SUM(CurrentInventory.qty) AS total', 'Product.name', 'Product.base_measurement_unit_id', 'Product.sales_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'ProductCategory.name', 'CurrentInventory.inventory_status_id'),
			'group' => array('CurrentInventory.product_id', 'Product.name', 'Product.order', 'Product.base_measurement_unit_id', 'Product.sales_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'CurrentInventory.store_id', 'ProductCategory.name', 'CurrentInventory.inventory_status_id'),
			'order' => array('Product.order' => 'ASC')
		);

		$currentInventories = $this->paginate();
        //$this->dd($currentInventories);exit;

		$this->loadModel('MeasurementUnit');
		$measurement_unit_list = $this->MeasurementUnit->find('list', array('fields' => 'name'));
		$productTypes = $this->ProductType->find('list', array('fields' => 'name'));
		$this->set(compact('measurement_unit_list', 'currentInventories'));
		$market_id = isset($this->request->data['UserDoctorVisitPlanList']['market_id']) != '' ? $this->request->data['UserDoctorVisitPlanList']['market_id'] : 0;

  
		if (isset($this->request->data['CurrentInventory']['product_categories_id']) != '') {
			//$products = $this->CurrentInventory->Product->find('list', array('order' => array('Product.order' => 'ASC')));
			$products = $this->CurrentInventory->Product->find('list', array(
				'conditions' => array('Product.product_category_id' => $this->request->data['CurrentInventory']['product_categories_id']),
				'order' => array('Product.order' => 'ASC'),
				'recursive' => -1
			));
		} else {
			$products = array();
		}
		if (isset($this->request->data['CurrentInventory']['category_summary'])) {
			$summaryCategoryList = $this->ProductCategory->find(
				'all',
				array(
					//'order' => array('Product.order'=>'asc'),
					'recursive' => -1
				)
			);

			$category_summary = $this->request->data['CurrentInventory']['category_summary'];
		} else {
			$category_summary = false;
			$summaryCategoryList = '';
		}
		$this->set(compact('category_summary', 'summaryCategoryList', 'productTypes'));
  
		$productCategories = $this->ProductCategory->find('list');
		//$stores = $this->Store->find('list', array('conditions' => $storeCondition));
		//pr($stores);

		$store_list = $this->Store->find('all',
			array(
				'conditions' => $storeCondition,
				'fields' => array('Store.id', 'Store.name', 'Territory.id'),
				//'order' => array('Store.name' => 'asc'),
				'recursive' => 0
			)
		);

		$stores = array();
		foreach ($store_list as $key => $value) {
			$stores[$value['Store']['id']] = $value['Store']['name'] . ' (' . $this->getSOName($value['Territory']['id']) . ')';
		}

		$inventoryStatuses = $this->InventoryStatuses->find('list', array('conditions' => array('InventoryStatuses.id !=' => 2)));

		$srore_keys = array_keys($stores);
		$srore_keys = 2061;
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.*', 'st.name', 'st.id', 'st.address', 'Territory.*', 'Office.office_name'),
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
			'conditions' => array('st.id' => $srore_keys),
			'order' => array('st.name' => 'asc'),
			'recursive' => 0
		));

		$Store = $this->Store->find('all', array(
			'fields' => array('Store.id', 'Store.name', 'sp.name'),
			'conditions' => array('store_type_id' => 3, 'Store.office_id' => $this->UserAuth->getOfficeId()),
			'joins' => array(
				array(
					'table' => 'sales_people',
					'alias' => 'sp',
					'type' => 'LEFT',
					'conditions' => array(
						'sp.office_id=Store.office_id AND sp.territory_id=Store.territory_id'
					)
				)
			),
			'order' => array('Store.name' => 'asc'),
			'recursive' => -1
		));

		//pr($Store);exit;
		$soStores = array();
		foreach ($Store as $data) {
			$soStores[] = $data['Store']['name'];
		}

		//print_r($soStores);
		if (isset($this->request['data']['CurrentInventory']['store_id'])) {
			$StoreId = $this->request['data']['CurrentInventory']['store_id'];
		} else {
			$StoreId = $this->UserAuth->getStoreId();
		}
		$this->set(compact('stores', 'inventoryStatuses', 'products', 'productCategories', 'StoreId'));
		// pr($currentInventories);exit;
	}

	public function admin_index_so($store_id = 33)
	{
		/* $this->set('page_title','Current Inventories');
		  pr($this->UserAuth->getStoreId());
		  exit; */
		$this->CurrentInventory->recursive = 1;
		$this->loadModel('Store');
		$this->loadModel('InventoryStatuses');
		$this->loadModel('ProductCategory');
		$office_parent_id = $this->UserAuth->getOfficeParentId();

		if ($office_parent_id == 0) {
			$conditions = array('CurrentInventory.inventory_status_id' => 1);
			$storeCondition = array();
		} else {
			$conditions = array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.inventory_status_id' => 1); //$this->UserAuth->getStoreId()
			$storeCondition = array('Store.office_id' => $this->UserAuth->getOfficeId());
		}

		//pr($this->UserAuth->getOfficeParentId());
		//exit;
		$this->paginate = array(
			//'conditions' => array('CurrentInventory.store_id'=>$this->UserAuth->getStoreId()),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'product_categories',
					'alias' => 'ProductCategory',
					'type' => 'INNER',
					'conditions' => array(
						'Product.product_category_id = ProductCategory.id'
					)
				)
			),
			'fields' => array('CurrentInventory.product_id', 'CurrentInventory.store_id', 'SUM(CurrentInventory.qty) AS total', 'Product.name', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'ProductCategory.name'),
			'group' => array('CurrentInventory.product_id', 'Product.name', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'CurrentInventory.store_id', 'ProductCategory.name'),
			//'order' => array('CurrentInventory.id' => 'DESC')
		);

		/* pr($this->paginate());
		  exit; */
		//$store_conditions = array('conditions'=>array('Store.office_id' => $this->UserAuth->getOfficeId()),'order' => array('Store.name' => 'ASC'));
		$this->set('currentInventories', $this->paginate());
		$products = $this->CurrentInventory->Product->find('list');
		$productCategories = $this->ProductCategory->find('list');
		$stores = $this->Store->find('list', array('conditions' => $storeCondition));
		$inventoryStatuses = $this->InventoryStatuses->find('list');
		$this->set(compact('stores', 'inventoryStatuses', 'products', 'productCategories'));
		//	$r=$this->CurrentInventory->find ('all',array('conditions'=>array('CurrentInventory.store_id'=>33),'fields'=>array('CurrentInventory.product_id','SUM(CurrentInventory.qty) AS total','Product.name'),'group'=>array('CurrentInventory.product_id','Product.name'))) ;
	}

	/*
	 * view details method
	 */

	public function admin_viewDetails($product_id = null, $store_id = null, $inventory_status_id = null)
	{
		$this->set('page_title', 'Current Inventories');


		$this->CurrentInventory->recursive = 1;
		$office_parent_id = $this->UserAuth->getOfficeParentId();

		//$productInfo=$this->Product->find('all',array('conditions'=>array('Product.id'=>$this->request->data['CurrentInventory']['product_id']),'recursive'=>-1,'fields'=>array('Product.id','Product.name','Product.sales_measurement_unit_id')));


		if ($office_parent_id == 0) {
			$this->paginate = array(
				'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.product_id' => $product_id, 'CurrentInventory.inventory_status_id' => $inventory_status_id),
				'fields' => array('CurrentInventory.id', 'CurrentInventory.product_id', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date', 'CurrentInventory.qty', 'InventoryStatuses.name', 'Store.name', 'Product.name', 'Product.product_code', 'Product.sales_measurement_unit_id'),
				'order' => array('CurrentInventory.id' => 'DESC')
			);
			$store_conditions = array('order' => array('Store.name' => 'ASC'));
		} else {
			$this->paginate = array( //$this->UserAuth->getStoreId()
				'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.product_id' => $product_id, 'CurrentInventory.inventory_status_id' => $inventory_status_id),
				'fields' => array('CurrentInventory.id', 'CurrentInventory.product_id', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date', 'CurrentInventory.qty', 'InventoryStatuses.name', 'Store.name', 'Product.name', 'Product.product_code', 'Product.sales_measurement_unit_id'),
				'order' => array('CurrentInventory.id' => 'DESC')
			);
			$store_conditions = array('conditions' => array('Store.office_id' => $this->UserAuth->getOfficeId()), 'order' => array('Store.name' => 'ASC'));
		}
		$currentInventories1 = $this->paginate();
		foreach ($currentInventories1 as $currentInventory) {
			$currentInventory['CurrentInventory']['sale_unit_qty'] = $this->unit_convertfrombase($currentInventory['CurrentInventory']['product_id'], $currentInventory['Product']['sales_measurement_unit_id'], $currentInventory['CurrentInventory']['qty']);
			$currentInventories[] = $currentInventory;
		}

		//$this->set('currentInventories', $this->paginate());
		$stores = $this->Store->find('list', $store_conditions);
		$this->set(compact('stores', 'office_parent_id', 'currentInventories'));
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
		if (!$this->CurrentInventory->exists($id)) {
			throw new NotFoundException(__('Invalid current inventory'));
		}
		$options = array('conditions' => array('CurrentInventory.' . $this->CurrentInventory->primaryKey => $id));
		$this->set('currentInventory', $this->CurrentInventory->find('first', $options));
	}

	public function admin_add()
	{
		$this->loadModel('Store');
		$this->loadModel('InventoryStatuses');
		$office_parent_id = $this->UserAuth->getOfficeParentId();

		if ($office_parent_id == 0) {
			$conditions = array('CurrentInventory.inventory_status_id' => 1);
			$storeCondition = array();
		} else {
			$conditions = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'CurrentInventory.inventory_status_id' => 1);
			$storeCondition = array('Store.office_id' => $this->UserAuth->getOfficeId());
		}

		if ($this->request->is('post')) {

			if (empty($this->request->data['product_id'])) {
				$this->Session->setFlash(__('CurrentInventory not created.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			} else {
				$data = array();
				$log_qty = array();
				$data['CurrentInventory']['store_id'] = $this->request->data['CurrentInventory']['store_id'];
				$data['CurrentInventory']['updated_at'] = $this->current_datetime();
				$data['CurrentInventory']['inventory_status_id'] = $this->request->data['CurrentInventory']['inventory_status_id'];
				$data['CurrentInventory']['transaction_type_id'] = 14; //  Opening Balance  
				$data['CurrentInventory']['transaction_date'] = $this->current_date();
				if (!empty($this->request->data['product_id'])) {
					$data_array = array();
					$store_id = $this->request->data['CurrentInventory']['store_id'];
					$inventory_status_id = $this->request->data['CurrentInventory']['inventory_status_id'];
					foreach ($this->request->data['product_id'] as $key => $val) {

						$product_id = $val;
						$batch_number = $this->request->data['batch_no'][$key];
						$expire_date = $this->request->data['expire_date'][$key];
						$log_qty[$key] = $this->request->data['quantity'][$key];
						$conditions = array();

						$conditions[] = array('CurrentInventory.product_id' => $product_id);
						$conditions[] = array('CurrentInventory.batch_number' => $batch_number);

						$conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
						$conditions[] = array('CurrentInventory.store_id' => $store_id);
						if ($expire_date != '') {


							$date = explode('-', $expire_date);
							$date[0] = date('m', strtotime($date[0]));
							$a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
							$expire_date = date("Y-m-t", strtotime($a_date));
							$conditions[] = array('CurrentInventory.expire_date' => $expire_date);
						}
						$inventory_info = $this->CurrentInventory->find('first', array('conditions' => $conditions));
						if (!empty($inventory_info)) {
							$data['CurrentInventory']['id'] = $inventory_info['CurrentInventory']['id'];
							$this->request->data['quantity'][$key] = $this->request->data['quantity'][$key] + $inventory_info['CurrentInventory']['qty'];
						}
						//pr($inventory_info);
						$data['CurrentInventory']['product_id'] = $val;
						$data['CurrentInventory']['qty'] = $this->request->data['quantity'][$key];
						$data['CurrentInventory']['batch_number'] = $this->request->data['batch_no'][$key];
						//$data['CurrentInventory']['expire_date'] = (trim($this->request->data['expire_date'][$key]) != '') ? (date('Y-m-d',strtotime($this->request->data['expire_date'][$key]))) : ""; 
						$date = ($this->request->data['expire_date'][$key] != '' && $this->request->data['expire_date'][$key] != 'null' && $this->request->data['expire_date'][$key] != ' ' ? explode('-', $this->request->data['expire_date'][$key]) : '');
						if (!empty($date[1])) {
							$date[0] = date('m', strtotime($date[0]));
							$a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
							$data['CurrentInventory']['expire_date'] = date("Y-m-t", strtotime($a_date));
						} else {
							$data['CurrentInventory']['expire_date'] = '';
						}

						$data['transaction_date'] = $this->current_date();

						$data_array[] = $data;
					}
					//pr($data_array);die();
					if ($this->CurrentInventory->saveAll($data_array)) {

						$this->loadModel('currentInventoryBalanceLog');
						$data = array();
						$data['currentInventoryBalanceLog']['store_id'] = $this->request->data['CurrentInventory']['store_id'];
						$data['currentInventoryBalanceLog']['created_by'] = $this->UserAuth->getUserId();
						$data['currentInventoryBalanceLog']['created_at'] = $this->current_datetime();
						$data['currentInventoryBalanceLog']['inventory_status'] = $this->request->data['CurrentInventory']['inventory_status_id'];
						$data_array = array();
						foreach ($this->request->data['product_id'] as $key => $val) {
							$data['currentInventoryBalanceLog']['product_id'] = $val;
							$data['currentInventoryBalanceLog']['quantity'] = $log_qty[$key];
							$data['currentInventoryBalanceLog']['batch_no'] = $this->request->data['batch_no'][$key];
							$data['currentInventoryBalanceLog']['exp_date'] = (trim($this->request->data['expire_date'][$key]) != '') ? (date('Y-m-d', strtotime($this->request->data['expire_date'][$key]))) : "";
							$data_array[] = $data;
						}
						//pr($data_array);die();
						$this->currentInventoryBalanceLog->saveAll($data_array);
					}


					$this->Session->setFlash(__('Current Inventory has been created.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
			}
		}

		$stores = $this->Store->find('list', array('conditions' => $storeCondition));
		$productTypes = $this->ProductType->find('list', array('order' => 'id'));
		//$products = $this->CurrentInventory->Product->find('list');
		//$inventoryStatuses = $this->InventoryStatuses->find('list',array('conditions'=>array('InventoryStatuses.id !='=>2)));
		$inventoryStatuses = $this->InventoryStatuses->find('list');
		$this->set(compact('stores', 'productTypes', 'inventoryStatuses'));
	}

	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null)
	{
		$this->CurrentInventory->id = $id;
		if (!$this->CurrentInventory->exists($id)) {
			throw new NotFoundException(__('Invalid current inventory'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->CurrentInventory->save($this->request->data)) {
				$this->flash(__('The current inventory has been saved.'), array('action' => 'index'));
			} else {
			}
		} else {
			$options = array('conditions' => array('CurrentInventory.' . $this->CurrentInventory->primaryKey => $id));
			$this->request->data = $this->CurrentInventory->find('first', $options);
		}
		$inventoryStores = $this->CurrentInventory->InventoryStore->find('list');
		$inventoryStatuses = $this->CurrentInventory->InventoryStatus->find('list');
		$products = $this->CurrentInventory->Product->find('list');
		$batches = $this->CurrentInventory->Batch->find('list');
		$this->set(compact('inventoryStores', 'inventoryStatuses', 'products', 'batches'));
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
		$this->CurrentInventory->id = $id;
		if (!$this->CurrentInventory->exists()) {
			throw new NotFoundException(__('Invalid current inventory'));
		}
		if ($this->CurrentInventory->delete()) {
			$this->flash(__('Current inventory deleted'), array('action' => 'index'));
		}
		$this->flash(__('Current inventory was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}

	/* ----------------------- Chainbox Data --------------------------- */

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
			$conditions[] = array('CurrentInventory.transaction_type_id' => $this->request->data['transaction_type_id']);
			$conditions[] = array('CurrentInventory.product_id' => $product_id);
			$conditions[] = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId());
		} else {
			$conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
			$conditions[] = array('CurrentInventory.product_id' => $product_id);
			$conditions[] = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId());
		}

		//$product_id = 12;
		$batch_list = $this->CurrentInventory->find('all', array(
			'fields' => array('CurrentInventory.batch_number as id', 'CurrentInventory.batch_number as title'),
			'conditions' => $conditions,
			'group' => array('CurrentInventory.batch_number'),
			'recursive' => -1
		));
  
		$data_array = Set::extract($batch_list, '{n}.0');
		if (!empty($batch_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	// batch list by status
	public function get_batch_list_status()
	{
		$rs = array(array('id' => '', 'title' => '---- Select Batch -----'));
		$product_id = $this->request->data['product_id'];
		$inventory_status_id = $this->request->data['inventory_status_id'];
		$batch_list = $this->CurrentInventory->find('all', array(
			'fields' => array('DISTINCT CurrentInventory.batch_number as id', 'CurrentInventory.batch_number as title'),
			'conditions' => array(
				'CurrentInventory.inventory_status_id' => $inventory_status_id,
				'CurrentInventory.product_id' => $product_id,
				'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
			),
			//'group' => array('CurrentInventory.batch_number'),
			'recursive' => -1
		));
		$data_array = Set::extract($batch_list, '{n}.0');
		if (!empty($batch_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function get_expire_date_list(){
		$rs = array(array('id' => '', 'title' => '---- Select Expired Date -----'));
		$product_id = $this->request->data['product_id'];
		//----------------product expire last date-----------\\
		$this->loadModel('ProductMonth');
		$product_expire_month_info = $this->ProductMonth->find('first', array(
			'conditions' => array(
				'ProductMonth.product_id' => $product_id
			),
			'fields' => array('ProductMonth.day_month'),
			'recursive' => -1
		));
		if (empty($product_expire_month_info)) {
			$productExpireLimit = 0;
		} else {
			$productExpireLimit = $product_expire_month_info['ProductMonth']['day_month'];
		}

		$p_expire_date = date('Y-m-t', strtotime("+" . $productExpireLimit . " months"));

		//--------------end-------------\\


		//$product_id = 11;
		$batch_no = urldecode($this->request->data['batch_no']);
		// echo $batch_no;exit;
		//$batch_no = 'T242';
		$inventory_status_id = $this->request->data['inventory_status_id'];
		if (isset($this->request->data['with_stock'])) {
			$with_stock = $this->request->data['with_stock'];
			$conditions[] = array('CurrentInventory.qty >' => 0);
		} else $with_stock = false;

		if (isset($this->request->data['transaction_type_id'])) {
			$conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
			$conditions[] = array('CurrentInventory.transaction_type_id' => $this->request->data['transaction_type_id']);
			$conditions[] = array('CurrentInventory.product_id' => $product_id);
			$conditions[] = array('CurrentInventory.batch_number' => $batch_no);
			$conditions[] = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId());
		} else {
			$conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
			$conditions[] = array('CurrentInventory.product_id' => $product_id);
			$conditions[] = array('CurrentInventory.batch_number' => $batch_no);
			$conditions[] = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId());
		}

		//$conditions[] = array( "(CurrentInventory.expire_date is null OR CurrentInventory.expire_date > '$p_expire_date' )");

		$exp_date_list = $this->CurrentInventory->find('all', array(
			'fields' => array('CurrentInventory.expire_date as id', 'CurrentInventory.expire_date as title'),
			'conditions' => $conditions,
			'recursive' => -1
		));
		$i = 0;
		$data_array = array();
		foreach ($exp_date_list as $data) {
			$data_array[] = array('id' => $data[$i]['id'], 'title' => date("M-y", strtotime($data[$i]['title'])));
		}
		//$data_array = Set::extract($exp_date_list, '{n}.0');
		if (!empty($data_array)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function get_inventory_details()
	{
		$product_id = $this->request->data['product_id'];

		$conditions_options['CurrentInventory.store_id'] = $this->UserAuth->getStoreId();
		$conditions_options['CurrentInventory.product_id'] = $product_id;
		if ($this->request->data['batch_no']) {
			$conditions_options['CurrentInventory.batch_number'] = ($this->request->data['batch_no'] ? $this->request->data['batch_no'] : NULL);
		}
		if ($this->request->data['expire_date']) {
			$conditions_options['CurrentInventory.expire_date'] = (!empty($this->request->data['expire_date']) ? $this->request->data['expire_date'] : NULL);
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
			echo $this->unit_convertfrombase($product_id, $batch_info['Product']['challan_measurement_unit_id'], $batch_info['CurrentInventory']['qty']);
		} else {
			echo '';
		}
		$this->autoRender = false;
	}

	public function get_inventory_details_in_Return_challan()
	{
		$product_id = $this->request->data['product_id'];
		//$product_id = 12;
		$batch_no = $this->request->data['batch_no'];
		//$batch_no = 12000;
		//$expire_date = ($this->request->data['expire_date'] !='' ? $this->request->data['expire_date'] : '0000-00-00');
		if (!empty($this->request->data['expire_date'])) {
			$conditions_options['CurrentInventory.expire_date'] = $this->request->data['expire_date'];
		}
		if (!empty($this->request->data['transaction_type_id'])) {
			$conditions_options['CurrentInventory.transaction_type_id'] = $this->request->data['transaction_type_id'];
		}
		$conditions_options['CurrentInventory.product_id'] = $product_id;
		$conditions_options['CurrentInventory.batch_number'] = $batch_no;
		$conditions_options['CurrentInventory.inventory_status_id'] = $this->request->data['inventory_status_id'];
		$conditions_options['CurrentInventory.store_id'] = $this->UserAuth->getStoreId();
		$batch_info = $this->CurrentInventory->find('first', array(
			'fields' => array('CurrentInventory.qty', 'Product.return_measurement_unit_id'),
			'conditions' => array($conditions_options),
			'recursive' => 0
		));
		//echo $batch_info['CurrentInventory']['qty'];
		if (!empty($batch_info)) {
			echo $this->unit_convertfrombase($product_id, $batch_info['Product']['return_measurement_unit_id'], $batch_info['CurrentInventory']['qty']);
		} else {
			echo '';
		}
		$this->autoRender = false;
	}

	public function get_inventory_details_in_NCP_Return_challan()
	{
		$product_id = $this->request->data['product_id'];
		//$product_id = 12;
		$batch_no = $this->request->data['batch_no'];
		//$batch_no = 12000;
		//$expire_date = ($this->request->data['expire_date'] !='' ? $this->request->data['expire_date'] : '0000-00-00');
		if (!empty($this->request->data['expire_date'])) {
			$conditions_options['CurrentInventory.expire_date'] = $this->request->data['expire_date'];
		}
		if (!empty($this->request->data['transaction_type_id'])) {
			$conditions_options['CurrentInventory.transaction_type_id'] = $this->request->data['transaction_type_id'];
		}
		$conditions_options['CurrentInventory.product_id'] = $product_id;
		$conditions_options['CurrentInventory.batch_number'] = $batch_no;
		$conditions_options['CurrentInventory.inventory_status_id'] = $this->request->data['inventory_status_id'];
		$conditions_options['CurrentInventory.store_id'] = $this->UserAuth->getStoreId();
		$batch_info = $this->CurrentInventory->find('first', array(
			'fields' => array('CurrentInventory.qty', 'Product.return_measurement_unit_id'),
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

	public function get_inventory_status_list()
	{
		$rs = array(array('id' => '', 'title' => '---- Select Inventory Status -----'));
		$product_id = $this->request->data['product_id'];
		//$product_id = 12;
		$status_list = $this->CurrentInventory->find('all', array(
			'fields' => array('DISTINCT  InventoryStatuses.id as id', 'InventoryStatuses.name as title'),
			'conditions' => array(
				'CurrentInventory.product_id' => $product_id,
				'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
			),
			//'group' => array('CurrentInventory.inventory_status_id'),
			'recursive' => 0
		));


		$data_array = Set::extract($status_list, '{n}.0');

		/* echo "<pre>";
		  print_r($data_array);
		  exit; */
		if (!empty($status_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	public function get_inventory_status_by_inv_id()
	{
		$cur_inv_id = $this->request->data['inv_id'];
		$type_id = $this->request->data['types'];
		$cur_inv = array(2, $cur_inv_id);

		$this->LoadModel('CurrentInventory');
		$this->CurrentInventory->Behaviors->load('Containable');
		$cur_inv_product = $this->CurrentInventory->find('all', array(
			'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'CurrentInventory.inventory_status_id' => $cur_inv_id, 'Product.product_type_id' => $type_id),
			'fields' => array('CurrentInventory.product_id', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date', 'CurrentInventory.qty', 'CurrentInventory.inventory_status_id'),
			'contain' => array('InventoryStatuses.name', 'Store.name', 'Product.name', 'Product.product_code', 'Product.ProductType.name'),
			'order' => array('Product.order' => 'ASC'),

		));
		$data_array = array();
		//$fromProducts=array(''=>'--- Select Product ---');
		//$inventory_status= array(''=>'--- Select Status ---');

		if ($cur_inv_product) {

			foreach ($cur_inv_product as $invProduct) {
				$fromProducts[$invProduct['CurrentInventory']['product_id']] = $invProduct['Product']['name'];
			}
			$data_array[1] = $fromProducts;
			$this->LoadModel('InventoryStatus');
			$inventory_status = $this->InventoryStatus->find('list', array('conditions' => array('NOT' => array('InventoryStatus.id' => $cur_inv))));
			$data_array[0] = $inventory_status;
		} else {
			$fromProducts = array();
			$inventory_status = array();
			$data_array[1] = $fromProducts;
			$data_array[0] = $inventory_status;
		}

		echo json_encode($data_array);
		$this->autoRender = false;
	}
	public function get_product_Info_by_inv_id()
	{
		$cur_inv_id = $this->request->data['inv_id'];
		$this->CurrentInventory->Behaviors->load('Containable');


		$product_info = $this->CurrentInventory->find('all', array(
			'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'CurrentInventory.id' => $cur_inv_id),
			'fields' => array('CurrentInventory.id', 'CurrentInventory.qty'),
			'contain' => array('Product.product_type_id', 'Product.ProductType.name', 'Product.base_measurement_unit_id', 'Product.product_category_id'),
			'order' => array('CurrentInventory.id' => 'DESC'),
		));


		//$data_array = Set::extract($status_list, '{n}.0');
		$data_array[0] = Set::combine($product_info, '{n}.Product.product_type_id', '{n}.Product.ProductType.name');
		$data_array['base_measurement_unit_id'] = $product_info[0]['Product']['base_measurement_unit_id'];
		$data_array['product_category_id'] = $product_info[0]['Product']['product_category_id'];
		$data_array['qty'] = $product_info[0]['CurrentInventory']['qty'];
		$data_array[0] = Set::combine($product_info, '{n}.Product.product_type_id', '{n}.Product.ProductType.name');


		echo json_encode($data_array);
		$this->autoRender = false;
	}

	public function get_product_Info_by_inv_id_back()
	{
		$cur_inv_id = $this->request->data['inv_id'];
		$this->CurrentInventory->Behaviors->load('Containable');


		$product_info = $this->CurrentInventory->find('all', array(
			'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'CurrentInventory.id' => $cur_inv_id),
			'fields' => array('CurrentInventory.id', 'CurrentInventory.qty'),
			'contain' => array('Product.product_type_id', 'Product.ProductType.name'),
			'order' => array('CurrentInventory.id' => 'DESC'),
		));


		//$data_array = Set::extract($status_list, '{n}.0');
		$data_array = Set::combine($product_info, '{n}.Product.product_type_id', '{n}.Product.ProductType.name');

		echo json_encode($data_array);
		$this->autoRender = false;
	}

	//only temporary for so

	public function get_batch_list_so()
	{
		$rs = array(array('id' => '', 'title' => '---- Select Batch -----'));
		$product_id = $this->request->data['product_id'];
		$inventory_status_id = $this->request->data['inventory_status_id'];
		//$product_id = 12;
		$batch_list = $this->CurrentInventory->find('all', array(
			'fields' => array('CurrentInventory.batch_number as id', 'CurrentInventory.batch_number as title'),
			'conditions' => array(
				'CurrentInventory.inventory_status_id' => $inventory_status_id,
				'CurrentInventory.product_id' => $product_id,
				'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
			),
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

	public function get_expire_date_list_so()
	{
		$rs = array(array('id' => '', 'title' => '---- Select Batch -----'));
		$product_id = $this->request->data['product_id'];
		//$product_id = 11;
		$batch_no = $this->request->data['batch_no'];
		//$batch_no = 'T242';
		$inventory_status_id = $this->request->data['inventory_status_id'];
		$batch_list = $this->CurrentInventory->find('all', array(
			'fields' => array('CurrentInventory.expire_date as id', 'CurrentInventory.expire_date as title'),
			'conditions' => array(
				'CurrentInventory.inventory_status_id' => $inventory_status_id,
				'CurrentInventory.product_id' => $product_id,
				'CurrentInventory.batch_number' => $batch_no,
				'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
			),
			'recursive' => -1
		));
		$data_array = Set::extract($batch_list, '{n}.0');
		if (!empty($batch_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function get_inventory_details_so()
	{
		$product_id = $this->request->data['product_id'];
		//$product_id = 12;
		$batch_no = $this->request->data['batch_no'];
		//$batch_no = 12000;
		//$expire_date = ($this->request->data['expire_date'] !='' ? $this->request->data['expire_date'] : '0000-00-00');
		if (!empty($this->request->data['expire_date'])) {
			$conditions_options['CurrentInventory.expire_date'] = $this->request->data['expire_date'];
		}
		$conditions_options['CurrentInventory.product_id'] = $product_id;
		$conditions_options['CurrentInventory.batch_number'] = $batch_no;
		$conditions_options['CurrentInventory.store_id'] = $this->UserAuth->getStoreId();
		$batch_info = $this->CurrentInventory->find('first', array(
			'fields' => array('CurrentInventory.qty', 'Product.challan_measurement_unit_id'),
			'conditions' => array($conditions_options),
			'recursive' => 0
		));
		if (!empty($batch_info)) {
			echo $this->unit_convertfrombase($product_id, $batch_info['Product']['challan_measurement_unit_id'], $batch_info['CurrentInventory']['qty']);
		} else {
			echo '';
		}
		$this->autoRender = false;
	}


	public function get_product_list()
	{
		//$this->loadModel('Product');

		$product_category_id = $this->request->data['product_category_id'];

		$rs = array(array('id' => '', 'name' => '---- Select -----'));

		$products = $this->CurrentInventory->Product->find('all', array(
			'conditions' => array('Product.product_category_id' => $product_category_id),
			'order' => array('Product.order' => 'ASC'),
			'recursive' => -1
		));

		//pr($products);

		$data_array = Set::extract($products, '{n}.Product');

		if (!empty($products)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}

		$this->autoRender = false;
	}


	public function admin_getCategoryQtyTotal($category_id = 20)
	{
		//return $category_id.'<br>';

		//for category summary
		$category_summary = false;
		//$this->loadModel('CurrentInventory');
		/*$this->loadModel('Store');
		$this->loadModel('InventoryStatuses');
		$this->loadModel('ProductCategory');
		$this->loadModel('SalesPerson');*/

		//$category_summary = $this->request->data['CurrentInventory']['category_summary'];

		$conditions = array('CurrentInventory.inventory_status_id !=' => 2, 'Product.product_category_id =' => $category_id);

		$summary_list = $this->CurrentInventory->find(
			'all',
			array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'table' => 'product_categories',
						'alias' => 'ProductCategory',
						'type' => 'INNER',
						'conditions' => array(
							'Product.product_category_id = ProductCategory.id'
						)
					)
				),
				'fields' => array('CurrentInventory.product_id', 'CurrentInventory.store_id', 'SUM(CurrentInventory.qty) AS total', 'Product.name', 'Product.product_category_id', 'Product.base_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'ProductCategory.name', 'CurrentInventory.inventory_status_id'),
				'group' => array('CurrentInventory.product_id', 'Product.name', 'Product.product_category_id', 'Product.order', 'Product.base_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'CurrentInventory.store_id', 'ProductCategory.name', 'CurrentInventory.inventory_status_id'),
				'order' => array('Product.order' => 'ASC'),
				'recursive' => 0
			)
		);

		$qty_total = 0;

		//pr($summary_list);

		foreach ($summary_list as $result) {
			if ($result['Product']['product_category_id'] == $category_id) {
				$qty_total += $result[0]['total'];
			}
		}

		//echo $qty_total;
		//exit;

		return $qty_total;

		//pr($summary_list);

		//exit;

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


	public function download_xl()
	{
		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes
		$params = $this->request->query['data'];

		$this->loadModel('InventoryStatuses');
		$this->loadModel('ProductCategory');
		if (isset($params['CurrentInventory']['store_id']) && $params['CurrentInventory']['store_id']) {
			$StoreId = $params['CurrentInventory']['store_id'];
		} else {
			$StoreId = $this->UserAuth->getStoreId();
		}
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if (CakeSession::read('Office.parent_office_id') != 0) {
			$conditions[] = array('Store.office_id' => CakeSession::read('Office.id'));
		}
		if (!empty($params['CurrentInventory']['product_code'])) {
			$conditions[] = array('Product.product_code' => $params['CurrentInventory']['product_code']);
		}
		if (!empty($params['CurrentInventory']['inventory_status_id'])) {
			$conditions[] = array('CurrentInventory.inventory_status_id' => $params['CurrentInventory']['inventory_status_id']);
		} else {
			$conditions[] = array('CurrentInventory.inventory_status_id !=' => 2);
		}
		if (!empty($params['CurrentInventory']['product_id'])) {
			$conditions[] = array('CurrentInventory.product_id' => $params['CurrentInventory']['product_id']);
		}
		if (!empty($params['CurrentInventory']['store_id'])) {
			$conditions[] = array('CurrentInventory.store_id' => $params['CurrentInventory']['store_id']);
		}
		if (!empty($params['CurrentInventory']['product_categories_id'])) {
			$conditions[] = array('ProductCategory.id' => $params['CurrentInventory']['product_categories_id']);
		}
		if (!empty($params['CurrentInventory']['product_type_id'])) {
			$conditions[] = array('Product.product_type_id' => $params['CurrentInventory']['product_type_id']);
		}
		$currentInventories = $this->CurrentInventory->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'product_categories',
					'alias' => 'ProductCategory',
					'type' => 'INNER',
					'conditions' => array(
						'Product.product_category_id = ProductCategory.id'
					)
				)
			),
			'fields' => array('CurrentInventory.product_id', 'CurrentInventory.store_id', 'SUM(CurrentInventory.qty) AS total', 'Product.name', 'Product.base_measurement_unit_id', 'Product.sales_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'ProductCategory.name', 'CurrentInventory.inventory_status_id'),
			'group' => array('CurrentInventory.product_id', 'Product.name', 'Product.order', 'Product.base_measurement_unit_id', 'Product.sales_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'CurrentInventory.store_id', 'ProductCategory.name', 'CurrentInventory.inventory_status_id'),
			'order' => array('Product.order' => 'ASC'),
			'recursive' => 1
		));


		if (isset($params['CurrentInventory']['category_summary']) && $params['CurrentInventory']['category_summary'] == 1) {

			$summaryCategoryList = $this->ProductCategory->find(
				'all',
				array(
					'recursive' => -1
				)
			);

			$category_summary = $params['CurrentInventory']['category_summary'];
		} else {
			$category_summary = false;
			$summaryCategoryList = '';
		}
		$this->loadModel('MeasurementUnit');
		$measurement_unit_list = $this->MeasurementUnit->find('list', array('fields' => 'name'));
		$productCategories = $this->ProductCategory->find('list');
		$inventoryStatuses = $this->InventoryStatuses->find('list', array('conditions' => array('InventoryStatuses.id !=' => 2)));

		// echo $StoreId;exit; 
		/*----------------- Report Xl table creation ------------------*/
		$table = '';
		if ($summaryCategoryList) {

			$table .= '<table border="1">
			<tr>
				<th class="text-center">Category Name</th>
				<th class="text-center">Quantity</th>
			</tr>';
			foreach ($summaryCategoryList as $result) :

				if ($this->admin_getCategoryQtyTotal($result['ProductCategory']['id']) > 0) {
					$table .= '<tr>';
					$table .= '<td class="text-center">' . h($result['ProductCategory']['name']) . '</td>';
					$table .= '<td class="text-center">' . $this->admin_getCategoryQtyTotal($result['ProductCategory']['id']) . '</td>';
					$table .= '</tr>';
				}
			endforeach;
			$table .= '</table>';
		} else {

			$table .= '<table border="1">';
			$table .= '<tr>';
			if ($StoreId == 13) {
				$table .= '<th class="text-center">Store</th>';
			}
			$table .= '<th class="text-center">Product</th>
				<th class="text-center">Product Unit</th>
				<th class="text-center">Product Code</th>
				<th class="text-center">Inventory Status</th>
				<th class="text-center">Product Category</th>
				<th class="text-center">Quantity</th>
				<th class="text-center">Quantity(Sale Unit)</th>
			</tr>';
			foreach ($currentInventories as $currentInventory) :
				$table .= '<tr>';
				if ($StoreId == 13) {
					$table .= '<td>' . trim($currentInventory['Store']['name']) . '</td>';
				}
				$table .= '<td>' . h($currentInventory['Product']['name']) . '</td>';
				$table .= '<td>' . h($measurement_unit_list[$currentInventory['Product']['base_measurement_unit_id']]) . '</td>';
				$table .= '<td class="text-center">' . h($currentInventory['Product']['product_code']) . '</td>';
				$table .= '<td class="text-center">' . h($currentInventory['InventoryStatuses']['name']) . '</td>';
				$table .= '<td class="text-center">' . h($currentInventory['ProductCategory']['name']) . '</td>';
				$table .= '<td class="text-center">' . h($currentInventory[0]['total']) . '</td>';
				$table .= '<td class="text-center">' . $this->unit_convertfrombase($currentInventory['CurrentInventory']['product_id'], $currentInventory['Product']['sales_measurement_unit_id'], ($currentInventory[0]['total'])) . '</td>';
				$table .= '</tr>';
			endforeach;
			$table .= '</table>';
		}
		header('Content-Type:application/force-download');
		header('Content-Disposition: attachment; filename="inventory.xls"');
		header("Cache-Control: ");
		header("Pragma: ");
		echo $table;
		$this->autoRender = false;
	}
}
