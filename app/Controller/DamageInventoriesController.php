<?php
App::uses('AppController', 'Controller');
/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class DamageInventoriesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator','Filter.Filter');
	public $uses = array('CurrentInventory','Store');
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
		$this->set('page_title','Damage Inventories');
		/*pr($this->UserAuth->getStoreId());
		exit;*/
		$this->CurrentInventory->recursive = 1;
		$this->loadModel('Store');
		$this->loadModel('InventoryStatuses');
		$this->loadModel('ProductCategory');
		$office_parent_id = $this->UserAuth->getOfficeParentId();

		if($office_parent_id == 0){
			$conditions =array('CurrentInventory.inventory_status_id'=>3);
			$storeCondition=array();
		}else{
			$conditions = array('CurrentInventory.store_id'=>$this->UserAuth->getStoreId(),'CurrentInventory.inventory_status_id'=>3);
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
			'fields' => array('CurrentInventory.product_id','CurrentInventory.store_id','SUM(CurrentInventory.qty) AS total','Product.name','Product.product_code','InventoryStatuses.name','Store.name','ProductCategory.name'),
			'group'=>array('CurrentInventory.product_id','Product.name','Product.product_code','InventoryStatuses.name','Store.name','CurrentInventory.store_id','ProductCategory.name'),
			//'order' => array('CurrentInventory.id' => 'DESC')
		);

		/*pr($this->paginate());
		exit;*/
		//$store_conditions = array('conditions'=>array('Store.office_id' => $this->UserAuth->getOfficeId()),'order' => array('Store.name' => 'ASC'));
		$this->set('currentInventories', $this->paginate());
		$products = $this->CurrentInventory->Product->find('list');
		$productCategories = $this->ProductCategory->find('list');
		$stores = $this->Store->find('list',array('conditions'=>$storeCondition));
		$inventoryStatuses = $this->InventoryStatuses->find('list');
		$this->set(compact('stores','inventoryStatuses','products','productCategories'));
	//	$r=$this->CurrentInventory->find ('all',array('conditions'=>array('CurrentInventory.store_id'=>33),'fields'=>array('CurrentInventory.product_id','SUM(CurrentInventory.qty) AS total','Product.name'),'group'=>array('CurrentInventory.product_id','Product.name'))) ;

	}

	/*
	 * view details method
	 */
	public function admin_viewDetails($product_id=null,$store_id=null) {
		$this->set('page_title','Current Inventories');


		$this->CurrentInventory->recursive = 1;
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0)
		{
			$this->paginate = array(
				'conditions' => array('CurrentInventory.store_id'=>$store_id,'CurrentInventory.product_id'=>$product_id,'CurrentInventory.inventory_status_id'=>3),
				'fields' => array('CurrentInventory.id','CurrentInventory.batch_number','CurrentInventory.expire_date','CurrentInventory.qty','InventoryStatuses.name','Store.name','Product.name','Product.product_code'),
				'order' => array('CurrentInventory.id' => 'DESC')
			);
			$store_conditions = array('order' => array('Store.name' => 'ASC'));
		}else{
			$this->paginate = array(//$this->UserAuth->getStoreId()
				'conditions' => array('CurrentInventory.store_id'=>$store_id,'CurrentInventory.product_id'=>$product_id,'CurrentInventory.inventory_status_id'=>3),
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
				$this->flash(__('Currentinventory saved.'), array('action' => 'index'));
			} else {
			}
		}
		$stores = $this->CurrentInventory->Store->find('list');

		$products = $this->CurrentInventory->Product->find('list');
		$inventoryStatuses = $this->CurrentInventory->InventoryStatuses->find('list');
		$this->set(compact('stores', 'products','inventoryStatuses'));
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
