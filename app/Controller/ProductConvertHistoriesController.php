<?php
App::uses('AppController', 'Controller');
/**
 * ProductConvertHistories Controller
 *
 * @property ProductConvertHistory $ProductConvertHistory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductConvertHistoriesController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Filter.Filter');
	//public $uses=array('Store');
	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->set('page_title', 'Product Conversion Histories');
		$office_parent_id = $this->UserAuth->getOfficeParentId();

		if ($office_parent_id == 0) {
			$this->paginate = array(

				'joins' => array(

					array(
						'alias' => 'FromCurrentInventory',
						'table' => 'current_inventories',
						'type' => 'LEFT',
						'conditions' => 'FromCurrentInventory.id = ProductConvertHistory.from_product_id'
					),
					array(
						'alias' => 'ToCurrentInventory',
						'table' => 'current_inventories',
						'type' => 'LEFT',
						'conditions' => 'ToCurrentInventory.id = ProductConvertHistory.to_product_id'
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
					array(
						'alias' => 'FromProductType',
						'table' => 'inventory_statuses',
						'type' => 'LEFT',
						'conditions' => 'FromProductType.id = ProductConvertHistory.from_status_id'
					), array(
						'alias' => 'ToProductType',
						'table' => 'inventory_statuses',
						'type' => 'LEFT',
						'conditions' => 'ToProductType.id = ProductConvertHistory.to_status_id'
					),
				),

				'fields' => array('ProductConvertHistory.*', 'Store.Name', 'FromCurrentInventory.batch_number', 'ToCurrentInventory.batch_number', 'FromCurrentInventory.expire_date', 'ToCurrentInventory.expire_date', 'FromProduct.name', 'ToProduct.name', 'ToProduct.name', 'FromProductType.name', 'ToProductType.name'),
				'order' => array('ProductConvertHistory.id' => 'DESC')
			);
		} else {

			$this->paginate = array(
				'conditions' => array('ProductConvertHistory.store_id' => $this->UserAuth->getStoreId()),

				'joins' => array(

					array(
						'alias' => 'FromCurrentInventory',
						'table' => 'current_inventories',
						'type' => 'LEFT',
						'conditions' => 'FromCurrentInventory.id = ProductConvertHistory.from_product_id'
					),
					array(
						'alias' => 'ToCurrentInventory',
						'table' => 'current_inventories',
						'type' => 'LEFT',
						'conditions' => 'ToCurrentInventory.id = ProductConvertHistory.to_product_id'
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
					array(
						'alias' => 'FromProductType',
						'table' => 'inventory_statuses',
						'type' => 'LEFT',
						'conditions' => 'FromProductType.id = ProductConvertHistory.from_status_id'
					), array(
						'alias' => 'ToProductType',
						'table' => 'inventory_statuses',
						'type' => 'LEFT',
						'conditions' => 'ToProductType.id = ProductConvertHistory.to_status_id'
					),
				),

				'fields' => array('ProductConvertHistory.*', 'Store.Name', 'FromCurrentInventory.batch_number', 'ToCurrentInventory.batch_number', 'FromCurrentInventory.expire_date', 'ToCurrentInventory.expire_date', 'FromProduct.name', 'ToProduct.name', 'FromProductType.name', 'ToProductType.name'),
				'order' => array('ProductConvertHistory.id' => 'DESC')
			);
		}


		$this->set('productConvertHistories', $this->paginate());
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
		$this->set('page_title', 'Product convert history Details');
		$this->loadModel('ProductConvertHistory');
		if (!$this->ProductConvertHistory->exists($id)) {
			throw new NotFoundException(__('Invalid product convert history'));
		}
		$options = array('conditions' => array('ProductConvertHistory.' . $this->ProductConvertHistory->primaryKey => $id));
		$this->set('productConvertHistory', $this->ProductConvertHistory->find('first', $options));
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

		$this->CurrentInventory->Behaviors->load('Containable');

		if ($this->request->is('post')) {
			//pr($this->request->data);

			$changeQuantity = $this->request->data['ProductConvertHistory']['quantity'];
			$fromProduct = $this->CurrentInventory->find('first', array(
				'conditions' => array(
					'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
					'CurrentInventory.product_id' => $this->request->data['ProductConvertHistory']['from_product_id'],
					'CurrentInventory.batch_number' => $this->request->data['ProductConvertHistory']['from_batch_no'],
					'CurrentInventory.expire_date' => $this->request->data['ProductConvertHistory']['from_expire_date'],
					'CurrentInventory.inventory_status_id' => $this->request->data['ProductConvertHistory']['from_status_id']
				),
				'fields' => array('CurrentInventory.id', 'CurrentInventory.qty', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date', 'CurrentInventory.product_id', 'CurrentInventory.inventory_status_id', 'CurrentInventory.store_id'),
			));
			$from_product_batch_no = $fromProduct['CurrentInventory']['batch_number'];
			$from_product_expire_date = $fromProduct['CurrentInventory']['expire_date'];
			$from_product_product_id = $fromProduct['CurrentInventory']['product_id'];
			$from_product_inventory_status_id = $fromProduct['CurrentInventory']['inventory_status_id'];
			$from_product_qty = $fromProduct['CurrentInventory']['qty'];
			$from_product_store_id = $fromProduct['CurrentInventory']['store_id'];
			$this->request->data['ProductConvertHistory']['from_product_id'] = $fromProduct['CurrentInventory']['id'];
			//pr($fromProduct);
			$toProduct = $this->CurrentInventory->find('first', array(
				'conditions' => array('CurrentInventory.store_id' => $from_product_store_id, 'CurrentInventory.product_id' => $from_product_product_id, 'CurrentInventory.batch_number' => $from_product_batch_no, 'CurrentInventory.expire_date' => $from_product_expire_date, 'CurrentInventory.inventory_status_id' => $this->request->data['ProductConvertHistory']['to_status_id']),

			));

			//pr($toProduct);
			$isValid = 1;
			if ($fromProduct['CurrentInventory']['qty'] < $this->request->data['ProductConvertHistory']['quantity']) {
				//$this->ProductConvertHistory->invalidate( 'quantity', '' );
				$this->Session->setFlash(__('Invalid Stock Quantity'), 'flash/error');
				$isValid = 0;
			} else {
				$reduceQuantity = $fromProduct['CurrentInventory']['qty'] - $changeQuantity;;
				$fromProductQuantity = $fromProduct['CurrentInventory']['qty'];

				$reducedInventory['CurrentInventory']['id'] = $this->request->data['ProductConvertHistory']['from_product_id'];
				$reducedInventory['CurrentInventory']['qty'] = $reduceQuantity;
				$reducedInventory['CurrentInventory']['transaction_type_id'] = 56; //Product convert out.
				$reducedInventory['CurrentInventory']['transaction_date'] = $this->current_date();
				$this->CurrentInventory->save($reducedInventory);
			}
			if ($isValid == 1) {

				if (!empty($toProduct)) {
					$this->request->data['ProductConvertHistory']['to_product_id'] = $toProduct['CurrentInventory']['id'];
					$addQuantity = $toProduct['CurrentInventory']['qty'] + $changeQuantity;
					$toProductQuantity = $toProduct['CurrentInventory']['qty'];

					$addInventory['CurrentInventory']['transaction_type_id'] = 55; //Product convert in.
					$addInventory['CurrentInventory']['id'] = $toProduct['CurrentInventory']['id'];
					$addInventory['CurrentInventory']['updated_at'] = $this->current_datetime();
					$addInventory['CurrentInventory']['qty'] = $addQuantity;
					$addInventory['CurrentInventory']['transaction_date'] = $this->current_date();

					$this->CurrentInventory->save($addInventory);
				} else {
					$addInventory['CurrentInventory']['expire_date'] = $from_product_expire_date;
					$addInventory['CurrentInventory']['batch_number'] = $from_product_batch_no;
					$addInventory['CurrentInventory']['store_id'] = $from_product_store_id;
					$addInventory['CurrentInventory']['product_id'] = $from_product_product_id;
					$addInventory['CurrentInventory']['inventory_status_id'] = $this->request->data['ProductConvertHistory']['to_status_id'];
					$addInventory['CurrentInventory']['transaction_type_id'] =  55; //Product convert in.
					$addInventory['CurrentInventory']['updated_at'] = $this->current_datetime();
					$addInventory['CurrentInventory']['created_at'] = $this->current_datetime();
					$addInventory['CurrentInventory']['qty'] = $changeQuantity;
					$addInventory['CurrentInventory']['transaction_date'] = $this->current_date();
					$this->CurrentInventory->create();
					$this->CurrentInventory->save($addInventory);
					$this->request->data['ProductConvertHistory']['to_product_id'] = $this->CurrentInventory->id;
				}
				$this->ProductConvertHistory->create();
				$this->request->data['ProductConvertHistory']['to_product_id'] = $this->request->data['ProductConvertHistory']['from_product_id'];
				$this->request->data['ProductConvertHistory']['created_at'] = $this->current_datetime();
				$this->request->data['ProductConvertHistory']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['ProductConvertHistory']['type'] = 0;

				//if($toProductCategory==$fromProductCategory && $toProductBaseUnit==$fromProductBaseUnit && $fromProductQuantity>=$changeQuantity){

				if ($this->ProductConvertHistory->save($this->request->data)) {


					$this->Session->setFlash(__('The product convert history has been saved successfully'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('The product convert history could not be saved. Please, try again.'), 'flash/error');
				}


				//}
			} else {
				$this->Session->setFlash(__('The product convert history could not be saved.Product Should be in same category and same base unit and convert quantity always should be less than total inventory quantity'), 'flash/error');
			}
		}

		$stores = $this->Store->find('list', array('conditions' => array('Store.id' => $this->UserAuth->getStoreId())));



		/*$cur_inv_product=$this->CurrentInventory->find( 'all',array(
			'conditions' => array('CurrentInventory.store_id'=>$this->UserAuth->getStoreId()),
			'fields' => array('CurrentInventory.id','CurrentInventory.batch_number','CurrentInventory.expire_date','CurrentInventory.qty','CurrentInventory.inventory_status_id'),
			'contain' =>array('InventoryStatuses.name','Store.name','Product.name','Product.product_code','Product.ProductType.name'),
			'order' => array('CurrentInventory.id' => 'DESC'),

		));

		foreach($cur_inv_product as $invProduct)
		{
			$fromProducts[$invProduct['CurrentInventory']['id']]=$invProduct['Product']['name'].' Batch:'.$invProduct['CurrentInventory']['batch_number'].' Exp:'.$invProduct['CurrentInventory']['expire_date'].' Sts:'.$invProduct['CurrentInventory']['inventory_status_id'];


		}
		$toProducts=$fromProducts;*/
		$this->LoadModel('InventoryStatus');
		$inventory_status = $this->InventoryStatus->find('list', array('conditions' => array('InventoryStatus.id !=' => 2)));
		$this->LoadModel('ProductType');
		$fromProductTypes = $this->ProductType->find('list', array('order' => 'id'));
		//pr($inventory_status);
		$this->set(compact('stores', 'inventory_status', 'fromProductTypes'));
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
		$this->loadModel('ProductConvertHistory');
		$this->set('page_title', 'Edit Product convert history');
		$this->ProductConvertHistory->id = $id;
		if (!$this->ProductConvertHistory->exists($id)) {
			throw new NotFoundException(__('Invalid product convert history'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['ProductConvertHistory']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->ProductConvertHistory->save($this->request->data)) {
				$this->Session->setFlash(__('The product convert history has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The product convert history could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('ProductConvertHistory.' . $this->ProductConvertHistory->primaryKey => $id));
			$this->request->data = $this->ProductConvertHistory->find('first', $options);
		}
		$stores = $this->ProductConvertHistory->Store->find('list');
		$fromProducts = $this->ProductConvertHistory->FromProduct->find('list');
		$toProducts = $this->ProductConvertHistory->ToProduct->find('list');
		$fromStatuses = $this->ProductConvertHistory->FromStatus->find('list');
		$toStatuses = $this->ProductConvertHistory->ToStatus->find('list');
		$this->set(compact('stores', 'fromProducts', 'toProducts', 'fromStatuses', 'toStatuses'));
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
		$this->loadModel('ProductConvertHistory');
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ProductConvertHistory->id = $id;
		if (!$this->ProductConvertHistory->exists()) {
			throw new NotFoundException(__('Invalid product convert history'));
		}
		if ($this->ProductConvertHistory->delete()) {
			$this->Session->setFlash(__('Product convert history deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Product convert history was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	public function get_inventory_stock_and_to_product_list()
	{

		$this->LoadModel('CurrentInventory');

		$product_id = $this->request->data['productId'];
		$batch_no = urldecode($this->request->data['batch_no']);
		$expire_date = $this->request->data['expire_date'];
		$inv_status = $this->request->data['inv_status'];

		$inventory_status_id = $inv_status;

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
}
