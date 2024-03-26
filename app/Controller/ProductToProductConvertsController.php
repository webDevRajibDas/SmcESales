<?php
App::uses('AppController', 'Controller');
/**
 * ProductConvertHistories Controller
 *
 * @property ProductConvertHistory $ProductConvertHistory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductToProductConvertsController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Filter.Filter');
	public $uses = array('ProductToProductConvert', 'InventoryAdjustment', 'Store', 'InventoryAdjustmentDetail');
	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->set('page_title', 'Product To Product Convert');
		$office_parent_id = $this->UserAuth->getOfficeParentId();

		if ($office_parent_id == 0) {
			$this->paginate = array(

				'joins' => array(

					array(
						'alias' => 'FromCurrentInventory',
						'table' => 'current_inventories',
						'type' => 'LEFT',
						'conditions' => 'FromCurrentInventory.id = ProductToProductConvert.from_current_inventory_id'
					),
					array(
						'alias' => 'ToCurrentInventory',
						'table' => 'current_inventories',
						'type' => 'LEFT',
						'conditions' => 'ToCurrentInventory.id = ProductToProductConvert.to_current_inventory_id'
					),
					array(
						'alias' => 'FromProduct',
						'table' => 'products',
						'type' => 'LEFT',
						'conditions' => 'FromCurrentInventory.product_id = FromProduct.id'
					),
					array(
						'alias' => 'ToProduct',
						'table' => 'products',
						'type' => 'LEFT',
						'conditions' => 'ToCurrentInventory.product_id = ToProduct.id'
					),
				),

				'fields' => array(
					'ProductToProductConvert.*',
					'Store.Name',
					'FromCurrentInventory.batch_number',
					'ToCurrentInventory.batch_number',
					'FromCurrentInventory.expire_date',
					'ToCurrentInventory.expire_date',
					'FromProduct.name',
					'ToProduct.name'
				),
				'order' => array('ProductToProductConvert.id' => 'DESC')
			);
		} else {
			$this->paginate = array(
				'conditions' => array('ProductToProductConvert.store_id' => $this->UserAuth->getStoreId()),
				'joins' => array(
					array(
						'alias' => 'FromCurrentInventory',
						'table' => 'current_inventories',
						'type' => 'LEFT',
						'conditions' => 'FromCurrentInventory.id = ProductToProductConvert.from_current_inventory_id'
					),
					array(
						'alias' => 'ToCurrentInventory',
						'table' => 'current_inventories',
						'type' => 'LEFT',
						'conditions' => 'ToCurrentInventory.id = ProductToProductConvert.to_current_inventory_id'
					),
					array(
						'alias' => 'FromProduct',
						'table' => 'products',
						'type' => 'LEFT',
						'conditions' => 'FromCurrentInventory.product_id = FromProduct.id'
					),
					array(
						'alias' => 'ToProduct',
						'table' => 'products',
						'type' => 'LEFT',
						'conditions' => 'ToCurrentInventory.product_id = ToProduct.id'
					),

				),
				'fields' => array(
					'ProductToProductConvert.*',
					'Store.Name',
					'FromCurrentInventory.batch_number',
					'ToCurrentInventory.batch_number',
					'FromCurrentInventory.expire_date',
					'ToCurrentInventory.expire_date',
					'FromProduct.name',
					'ToProduct.name'
				),
				'order' => array('ProductToProductConvert.id' => 'DESC'),
			);
		}

		$this->set('ptpcovert', $this->paginate());
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->set('page_title', 'Add Product convert history');
		$this->loadModel('CurrentInventory');
		$this->loadModel('Store');

		//print_r($this->UserAuth->getStoreId());exit;

		$this->CurrentInventory->Behaviors->load('Containable');

		if ($this->request->is('post')) {

			$changeQuantity = $this->request->data['ProductToProductConvert']['quantity'];

			$fromProduct = $this->CurrentInventory->find('first', array(
				'conditions' => array(
					'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
					'CurrentInventory.product_id' => $this->request->data['ProductToProductConvert']['from_product_id'],
					'CurrentInventory.batch_number' => $this->request->data['ProductToProductConvert']['from_batch_no'],
					'CurrentInventory.expire_date' => $this->request->data['ProductToProductConvert']['from_expire_date'],
					'CurrentInventory.inventory_status_id' => 1
				),
				'fields' => array('CurrentInventory.id', 'CurrentInventory.qty', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date', 'CurrentInventory.product_id', 'CurrentInventory.inventory_status_id', 'CurrentInventory.store_id'),
			));

			$this->request->data['ProductToProductConvert']['from_current_inventory_id'] = $fromProduct['CurrentInventory']['id'];
			if ($this->request->data['ProductToProductConvert']['to_batch_no'] &&  $this->request->data['ProductToProductConvert']['to_expire_date']) {
				$toProduct = $this->CurrentInventory->find('first', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
						'CurrentInventory.product_id' => $this->request->data['ProductToProductConvert']['to_product_id'],
						'CurrentInventory.batch_number' => $this->request->data['ProductToProductConvert']['to_batch_no'],
						'CurrentInventory.expire_date' => $this->request->data['ProductToProductConvert']['to_expire_date'],
						'CurrentInventory.inventory_status_id' => 1
					),
					'fields' => array('CurrentInventory.id', 'CurrentInventory.qty', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date', 'CurrentInventory.product_id', 'CurrentInventory.inventory_status_id', 'CurrentInventory.store_id'),
				));

				$this->request->data['ProductToProductConvert']['to_current_inventory_id'] = $toProduct['CurrentInventory']['id'];
			} else {
				$toProduct = $this->CurrentInventory->find('first', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
						'CurrentInventory.product_id' => $this->request->data['ProductToProductConvert']['to_product_id'],
						'CurrentInventory.batch_number' => $this->request->data['ProductToProductConvert']['from_batch_no'],
						'CurrentInventory.expire_date' => $this->request->data['ProductToProductConvert']['from_expire_date'],
						'CurrentInventory.inventory_status_id' => 1
					),
					'fields' => array('CurrentInventory.id', 'CurrentInventory.qty', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date', 'CurrentInventory.product_id', 'CurrentInventory.inventory_status_id', 'CurrentInventory.store_id'),
				));

				$this->request->data['ProductToProductConvert']['to_current_inventory_id'] = $toProduct['CurrentInventory']['id'];
			}
			//----from prouduct------------\\
			$isValid = 1;
			if ($fromProduct['CurrentInventory']['qty'] < $this->request->data['ProductToProductConvert']['quantity']) {
				$this->Session->setFlash(__('Invalid Stock Quantity'), 'flash/error');
				$isValid = 0;
			} else {
				$reduceQuantity = $fromProduct['CurrentInventory']['qty'] - $changeQuantity;;
				$fromProductQuantity = $fromProduct['CurrentInventory']['qty'];

				$reducedInventory['CurrentInventory']['id'] = $this->request->data['ProductToProductConvert']['from_current_inventory_id'];
				$reducedInventory['CurrentInventory']['qty'] = $reduceQuantity;
				$reducedInventory['CurrentInventory']['transaction_type_id'] = 45; //convert product out.
				$reducedInventory['CurrentInventory']['transaction_date'] = $this->current_date();
				$this->CurrentInventory->save($reducedInventory);
			}

			//--------to product---------\\
			if (!empty($toProduct)) {
				$addQuantity = $toProduct['CurrentInventory']['qty'] + $changeQuantity;
				$toProductQuantity = $toProduct['CurrentInventory']['qty'];
				$addInventory['CurrentInventory']['transaction_type_id'] = 46; // convert product add.
				$addInventory['CurrentInventory']['id'] = $toProduct['CurrentInventory']['id'];
				$addInventory['CurrentInventory']['updated_at'] = $this->current_datetime();
				$addInventory['CurrentInventory']['qty'] = $addQuantity;
				$addInventory['CurrentInventory']['transaction_date'] = $this->current_date();
				$this->CurrentInventory->save($addInventory);
			} else {
				$addQuantity =  $changeQuantity;
				$addInventory['CurrentInventory']['store_id'] = $this->UserAuth->getStoreId();
				$addInventory['CurrentInventory']['product_id'] = $this->request->data['ProductToProductConvert']['to_product_id'];
				$addInventory['CurrentInventory']['batch_number'] = $this->request->data['ProductToProductConvert']['from_batch_no'];
				$addInventory['CurrentInventory']['expire_date'] = $this->request->data['ProductToProductConvert']['from_expire_date'];
				$addInventory['CurrentInventory']['inventory_status_id'] = 1;
				$addInventory['CurrentInventory']['transaction_type_id'] = 46; // convert product add.
				$addInventory['CurrentInventory']['updated_at'] = $this->current_datetime();
				$addInventory['CurrentInventory']['qty'] = $addQuantity;
				$addInventory['CurrentInventory']['transaction_date'] = $this->current_date();
				$this->CurrentInventory->create();
				$this->CurrentInventory->save($addInventory);


				$toProduct = $this->CurrentInventory->find('first', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
						'CurrentInventory.product_id' => $this->request->data['ProductToProductConvert']['to_product_id'],
						'CurrentInventory.batch_number' => $this->request->data['ProductToProductConvert']['from_batch_no'],
						'CurrentInventory.expire_date' => $this->request->data['ProductToProductConvert']['from_expire_date'],
						'CurrentInventory.inventory_status_id' => 1
					),
					'fields' => array('CurrentInventory.id', 'CurrentInventory.qty', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date', 'CurrentInventory.product_id', 'CurrentInventory.inventory_status_id', 'CurrentInventory.store_id'),
				));

				$this->request->data['ProductToProductConvert']['to_current_inventory_id'] = $toProduct['CurrentInventory']['id'];
			}

			//echo '<pre>';print_r($this->request->data);exit;
			if ($isValid == 1) {
				$this->ProductToProductConvert->create();
				$this->request->data['ProductToProductConvert']['created_at'] = $this->current_datetime();
				$this->request->data['ProductToProductConvert']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['ProductToProductConvert']['type'] = 0;

				if ($this->ProductToProductConvert->save($this->request->data)) {
					/* ------------ From inventory adjustmet :start -------------- */
					$this->InventoryAdjustment->create();
					$in_ad_data['InventoryAdjustment']['store_id'] = $this->UserAuth->getStoreId();

					$in_ad_data['InventoryAdjustment']['transaction_type_id'] = 45;
					$in_ad_data['InventoryAdjustment']['status'] = 1;
					$in_ad_data['InventoryAdjustment']['approval_status'] = 1;
					$in_ad_data['InventoryAdjustment']['acknowledge_status'] = 1;
					$in_ad_data['InventoryAdjustment']['remarks'] = "Product to product convert (out) from system at " . $this->current_datetime();
					$in_ad_data['InventoryAdjustment']['created_at'] = $this->current_datetime();
					$in_ad_data['InventoryAdjustment']['created_by'] = $this->UserAuth->getUserId();
					if ($this->InventoryAdjustment->save($in_ad_data)) {
						$data['InventoryAdjustmentDetail']['inventory_adjustment_id'] = $this->InventoryAdjustment->id;
						$data['InventoryAdjustmentDetail']['current_inventory_id'] = $this->request->data['ProductToProductConvert']['from_current_inventory_id'];
						$data['InventoryAdjustmentDetail']['quantity'] = $changeQuantity;
						$this->InventoryAdjustmentDetail->saveAll($data);
					}
					/* ------------ From inventory adjustmet :end -------------- */


					/* ------------ To inventory adjustmet :start -------------- */
					$this->InventoryAdjustment->create();
					$in_ad_data['InventoryAdjustment']['store_id'] = $this->UserAuth->getStoreId();

					$in_ad_data['InventoryAdjustment']['transaction_type_id'] = 46;
					$in_ad_data['InventoryAdjustment']['status'] = 2;
					$in_ad_data['InventoryAdjustment']['approval_status'] = 1;
					$in_ad_data['InventoryAdjustment']['acknowledge_status'] = 1;
					$in_ad_data['InventoryAdjustment']['remarks'] = "Product to product convert (in) from system at " . $this->current_datetime();
					$in_ad_data['InventoryAdjustment']['created_at'] = $this->current_datetime();
					$in_ad_data['InventoryAdjustment']['created_by'] = $this->UserAuth->getUserId();
					if ($this->InventoryAdjustment->save($in_ad_data)) {
						$data['InventoryAdjustmentDetail']['inventory_adjustment_id'] = $this->InventoryAdjustment->id;
						$data['InventoryAdjustmentDetail']['current_inventory_id'] = $this->request->data['ProductToProductConvert']['to_current_inventory_id'];
						$data['InventoryAdjustmentDetail']['quantity'] = $changeQuantity;
						$this->InventoryAdjustmentDetail->saveAll($data);
					}
					/* ------------ To inventory adjustmet :end -------------- */


					$this->Session->setFlash(__('The product to product convert  has been saved successfully'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('The product to product convert  could not be saved. Please, try again.'), 'flash/error');
				}
			} else {
				$this->Session->setFlash(__('The product to product convert could not be saved.Product Should be in same category and same base unit and convert quantity always should be less than total inventory quantity'), 'flash/error');
			}
		}

		$stores = $this->Store->find('list', array('conditions' => array('Store.id' => $this->UserAuth->getStoreId())));

		$this->LoadModel('ProductType');
		$fromProductTypes = $this->ProductType->find('list', array('order' => 'id'));

		$this->set(compact('stores', 'fromProductTypes'));
	}


	public function get_inventory_status_by_inv_id()
	{

		$type_id = $this->request->data['types'];

		$this->LoadModel('CurrentInventory');
		$cur_inv_product = $this->CurrentInventory->find('all', array(
			'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'CurrentInventory.inventory_status_id' => 1, 'Product.product_type_id' => $type_id),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.id=CurrentInventory.product_id'
				),
			),
			'fields' => array('CurrentInventory.product_id', 'Product.name', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date', 'CurrentInventory.qty', 'CurrentInventory.inventory_status_id'),

			'order' => array('Product.order' => 'ASC'),
			'recursive' => -1

		));

		$data_array = array();

		if ($cur_inv_product) {

			foreach ($cur_inv_product as $invProduct) {
				$fromProducts[$invProduct['CurrentInventory']['product_id']] = $invProduct['Product']['name'];
			}
			$data_array[1] = $fromProducts;
		} else {
			$fromProducts = array();
			$data_array[1] = $fromProducts;
		}

		echo json_encode($data_array);
		$this->autoRender = false;
	}


	public function get_batch_list($from_product = 0)
	{

		$this->LoadModel('CurrentInventory');

		$rs = array(array('id' => '', 'title' => '---- Select Batch -----'));
		$product_id = $this->request->data['product_id'];
		$inventory_status_id = 1;

		$con = array(
			'CurrentInventory.inventory_status_id' => $inventory_status_id,
			'CurrentInventory.product_id' => $product_id,
			'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
		);
		if ($from_product) {
			$con['CurrentInventory.qty >'] = 0;
		}

		$batch_list = $this->CurrentInventory->find('all', array(
			'fields' => array('CurrentInventory.batch_number as id', 'CurrentInventory.batch_number as title'),
			'conditions' => $con,
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

	public function get_expire_date_list($from_product = 0)
	{

		$this->LoadModel('CurrentInventory');

		$rs = array(array('id' => '', 'title' => '---- Select Expired Date -----'));
		$product_id = $this->request->data['product_id'];
		$batch_no = urldecode($this->request->data['batch_no']);
		$inventory_status_id = 1;
		$con = array(
			'CurrentInventory.inventory_status_id' => $inventory_status_id,
			'CurrentInventory.product_id' => $product_id,
			'CurrentInventory.batch_number' => $batch_no,
			'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
		);
		if ($from_product) {
			$con['CurrentInventory.qty >'] = 0;
		}
		$exp_date_list = $this->CurrentInventory->find('all', array(
			'fields' => array('CurrentInventory.expire_date as id', 'CurrentInventory.expire_date as title'),
			'conditions' => $con,
			'recursive' => -1
		));
		$i = 0;
		foreach ($exp_date_list as $data) {
			$data_array[] = array('id' => $data[$i]['id'], 'title' => date("M-y", strtotime($data[$i]['title'])));
		}

		if (!empty($exp_date_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function get_inventory_stock_and_to_product_list()
	{

		$this->LoadModel('CurrentInventory');

		$product_id = $this->request->data['productId'];
		$batch_no = urldecode($this->request->data['batch_no']);
		$expire_date = $this->request->data['expire_date'];
		$ptype = $this->request->data['ptype'];
		$toproduct = $this->request->data['toproduct'];
		$inventory_status_id = 1;

		$con = array(
			'CurrentInventory.qty >' => 0,
			'CurrentInventory.inventory_status_id' => $inventory_status_id,
			'CurrentInventory.product_id' => $product_id,
			'CurrentInventory.batch_number' => $batch_no,
			'CurrentInventory.expire_date' => $expire_date,
			'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
		);
		$stockQty = $this->CurrentInventory->find('first', array(
			'fields' => array('CurrentInventory.*'),
			'conditions' => $con,
			'recursive' => -1
		));

		if (!empty($stockQty)) {
			$qty = $stockQty['CurrentInventory']['qty'] + 0;
		} else {
			$qty = 0;
		}

		$data_array = array();
		$data_array[0] = $qty;


		echo json_encode($data_array);
		$this->autoRender = false;
	}
	public function get_to_product_list()
	{

		$product_id = $this->request->data['productId'];
		$ptype = $this->request->data['ptype'];
		$maintain_batch = $this->request->data['maintain_batch'];
		$is_maintain_expire_date = $this->request->data['maintain_expire'];
		$product_category_id = $this->request->data['product_category_id'];
		$ptype = $this->request->data['ptype'];
		$this->LoadModel('Product');

		$cur_inv_product = $this->Product->find('all', array(
			'conditions' => array(

				'Product.product_type_id' => $ptype,
				'Product.maintain_batch' => $maintain_batch,
				'Product.is_maintain_expire_date' => $is_maintain_expire_date,
				'Product.product_category_id' => $product_category_id,
				/* 'NOT' => array('Product.id' => $product_id) */
			),
			'fields' => array('Product.id', 'Product.name'),

			'order' => array('Product.order' => 'ASC'),
			'recursive' => -1

		));
		//$this->UserAuth->getStoreId()

		if ($cur_inv_product) {

			foreach ($cur_inv_product as $invProduct) {
				$fromProducts[$invProduct['Product']['id']] = $invProduct['Product']['name'];
			}
			$data_array[0] = $fromProducts;
		} else {
			$fromProducts = array();
			$data_array[0] = $fromProducts;
		}
		echo json_encode($data_array);
		$this->autoRender = false;
	}

	public function get_product_measurement_units_info()
	{

		$this->loadModel('Product');

		$product_id = $this->request->data['product_id'];

		$productinfo = $this->Product->find('first', array(
			'conditions' => array(
				'Product.id' => $product_id
			),
			'joins' => array(
				array(
					'table' => 'measurement_units',
					'alias' => 'MeasurementUnit',
					'conditions' => 'Product.base_measurement_unit_id=MeasurementUnit.id'
				),
			),
			'fields' => array('MeasurementUnit.id', 'MeasurementUnit.name'),
			'recursive' => -1

		));

		$name = $productinfo['MeasurementUnit']['name'];
		echo json_encode($name);
		$this->autoRender = false;
	}
}
