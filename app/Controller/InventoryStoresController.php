<?php
App::uses('AppController', 'Controller');
/**
 * InventoryStores Controller
 *
 * @property InventoryStore $InventoryStore
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class InventoryStoresController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Inventory Store List');
		$this->InventoryStore->recursive = 0;
		$this->paginate = array(			
			'order' => array('InventoryStore.id' => 'DESC')
		);
		$this->set('inventoryStores', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Inventory Store Details');
		if (!$this->InventoryStore->exists($id)) {
			throw new NotFoundException(__('Invalid inventory store'));
		}
		$options = array('conditions' => array('InventoryStore.' . $this->InventoryStore->primaryKey => $id));
		$this->set('inventoryStore', $this->InventoryStore->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Inventory Store');
		$store_type_list = array(
			1 => 'ASO',
			2 => 'CWH',
			3 => 'HO',
			4 => 'HR',
		);
		$this->set('store_type_list',$store_type_list);
		if ($this->request->is('post')) {
			$this->request->data['InventoryStore']['reference_id'] = 0; 
			$this->request->data['InventoryStore']['created_at'] = $this->current_datetime(); 
			$this->request->data['InventoryStore']['created_by'] = $this->UserAuth->getUserId();
			$this->InventoryStore->create();
			if ($this->InventoryStore->save($this->request->data)) {
				$this->Session->setFlash(__('The inventory store has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The inventory store could not be saved. Please, try again.'), 'flash/error');
			}
		}
		//$references = $this->InventoryStore->Reference->find('list');
		//$this->set(compact('references'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Inventory Store');
		$store_type_list = array(
			1 => 'ASO',
			2 => 'CWH',
			3 => 'HO',
			4 => 'HR',
		);
		$this->set('store_type_list',$store_type_list);
        $this->InventoryStore->id = $id;
		if (!$this->InventoryStore->exists($id)) {
			throw new NotFoundException(__('Invalid inventory store'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['InventoryStore']['reference_id'] = 0; 
			$this->request->data['InventoryStore']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->InventoryStore->save($this->request->data)) {
				$this->Session->setFlash(__('The inventory store has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The inventory store could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('InventoryStore.' . $this->InventoryStore->primaryKey => $id));
			$this->request->data = $this->InventoryStore->find('first', $options);
		}
		//$references = $this->InventoryStore->Reference->find('list');
		//$this->set(compact('references'));
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
		$this->InventoryStore->id = $id;
		if (!$this->InventoryStore->exists()) {
			throw new NotFoundException(__('Invalid inventory store'));
		}
		if ($this->InventoryStore->delete()) {
			$this->Session->setFlash(__('Inventory store deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Inventory store was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
