<?php
App::uses('AppController', 'Controller');
/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class NcpInventoriesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator','Filter.Filter');
	public $uses = array('CurrentInventory','Store','ProductType');
/**
 * admin_index method
 *
 * @return void
 */


	/**
	 * inventory_total method
	 */
	public function admin_index()
	{
		$this->set('page_title','Ncp Inventories');
		/*pr($this->UserAuth->getStoreId());
		exit;*/
		$this->CurrentInventory->recursive = 1;
		//$this->loadModel('Store');
		$this->loadModel('InventoryStatuses');
		$this->loadModel('ProductCategory');
		$office_parent_id = $this->UserAuth->getOfficeParentId();

		if($office_parent_id == 0){
			$conditions =array('CurrentInventory.inventory_status_id '=>2);
			$storeCondition=array();
		}else{
			$conditions = array('CurrentInventory.store_id'=>$this->UserAuth->getStoreId(),'CurrentInventory.inventory_status_id '=>2);
			$storeCondition=array('Store.office_id'=>$this->UserAuth->getOfficeId());
		}

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
			'fields' => array('CurrentInventory.product_id','CurrentInventory.store_id','CurrentInventory.transaction_type_id','SUM(CurrentInventory.qty) AS total','Product.name','Product.product_code','InventoryStatuses.name','Product.base_measurement_unit_id','TransactionType.name','Store.name','ProductCategory.name'),
			'group'=>array('CurrentInventory.product_id','Product.name','Product.product_code','Product.base_measurement_unit_id','InventoryStatuses.name','Store.name','CurrentInventory.transaction_type_id','CurrentInventory.store_id','ProductCategory.name','TransactionType.name'),
			//'order' => array('CurrentInventory.id' => 'DESC')
		);

		/*pr($this->paginate());
		exit;*/
		//$store_conditions = array('conditions'=>array('Store.office_id' => $this->UserAuth->getOfficeId()),'order' => array('Store.name' => 'ASC'));
		//
		$this->loadModel('MeasurementUnit');
		$measurement_unit_list = $this->MeasurementUnit->find('list', array('fields'=>'name'));
		$this->set('currentInventories', $this->paginate());
		$products = $this->CurrentInventory->Product->find('list', array('order' => array('order'=>'asc')));
		$productCategories = $this->ProductCategory->find('list');
		$stores = $this->Store->find('list',array('conditions'=>$storeCondition));
		$inventoryStatuses = $this->InventoryStatuses->find('list',array('conditions'=>array('InventoryStatuses.id !='=>1)));
		$this->set(compact('stores','inventoryStatuses','products','productCategories','measurement_unit_list'));
	//	$r=$this->CurrentInventory->find ('all',array('conditions'=>array('CurrentInventory.store_id'=>33),'fields'=>array('CurrentInventory.product_id','SUM(CurrentInventory.qty) AS total','Product.name'),'group'=>array('CurrentInventory.product_id','Product.name'))) ;

	}

	/*
	 * view details method
	 */
	public function admin_viewDetails($product_id=null,$store_id=null,$tran_type_id=null) {
		$this->set('page_title','Current Inventories');


		$this->CurrentInventory->recursive = 1;
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0)
		{
			$this->paginate = array(
				'conditions' => array('CurrentInventory.store_id'=>$store_id,'CurrentInventory.product_id'=>$product_id,'CurrentInventory.transaction_type_id'=>$tran_type_id,'CurrentInventory.inventory_status_id'=>2),
				'fields' => array('CurrentInventory.id','CurrentInventory.batch_number','CurrentInventory.expire_date','CurrentInventory.qty','InventoryStatuses.name','Store.name','Product.name','Product.product_code'),
				'order' => array('CurrentInventory.id' => 'DESC')
			);
			$store_conditions = array('order' => array('Store.name' => 'ASC'));
		}else{
			$this->paginate = array(//$this->UserAuth->getStoreId()
				'conditions' => array('CurrentInventory.store_id'=>$store_id,'CurrentInventory.product_id'=>$product_id,'CurrentInventory.transaction_type_id'=>$tran_type_id,'CurrentInventory.inventory_status_id'=>2),
				'fields' => array('CurrentInventory.id','CurrentInventory.batch_number','CurrentInventory.expire_date','CurrentInventory.qty','InventoryStatuses.name','Store.name','Product.name','Product.product_code'),
				'order' => array('CurrentInventory.id' => 'DESC')
			);
			$store_conditions = array('conditions'=>array('Store.office_id' => $this->UserAuth->getOfficeId()),'order' => array('Store.name' => 'ASC'));
		}
		$this->set('currentInventories', $this->paginate());
		$stores = $this->Store->find('list',$store_conditions);
		$this->set(compact('stores','office_parent_id'));
	}
/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->CurrentInventory->exists($id)) {
			throw new NotFoundException(__('Invalid current inventory'));
		}
		$options = array('conditions' => array('CurrentInventory.' . $this->CurrentInventory->primaryKey => $id));
		$this->set('currentInventory', $this->CurrentInventory->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->CurrentInventory->create();
			if ($this->CurrentInventory->save($this->request->data)) {
				$this->Session->setFlash(__('Currentinventory saved.'),'flash/success');
				$this->redirect(array('action'=>'index'));
			} else {
			}
		}
		$stores = $this->CurrentInventory->Store->find('list');
		$productTypes=$this->ProductType->find('list',array('order'=>'id'));
		//$products = $this->CurrentInventory->Product->find('list');
		$inventoryStatuses = $this->CurrentInventory->InventoryStatuses->find('list',array('conditions'=>array('InventoryStatuses.id '=>2)));
		$this->set(compact('stores', 'productTypes','inventoryStatuses'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->CurrentInventory->id = $id;
		if (!$this->CurrentInventory->exists($id)) {
			throw new NotFoundException(__('Invalid current inventory'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->CurrentInventory->save($this->request->data)) {
				$this->Session->setFlash(__('The current inventory has been saved.'), 'flash/success');
				$this->redirect(array('action'=>'index'));
			} else {
			}
		} else {
			$options = array('conditions' => array('CurrentInventory.' . $this->CurrentInventory->primaryKey => $id));
			$this->request->data = $this->CurrentInventory->find('first', $options);
		}
		$inventoryStores = $this->CurrentInventory->InventoryStore->find('list');
		$inventoryStatuses = $this->CurrentInventory->InventoryStatus->find('list',array('conditions'=>array('InventoryStatuses.id'=>2)));
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
	public function admin_delete($id = null) {
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
	

}
