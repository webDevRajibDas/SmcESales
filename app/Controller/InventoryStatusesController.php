<?php
App::uses('AppController', 'Controller');
/**
 * InventoryStatuses Controller
 *
 * @property InventoryStatus $InventoryStatus
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class InventoryStatusesController extends AppController {

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
		
		$this->set('page_title','Inventory Status List');
		$this->InventoryStatus->recursive = 0;
		$this->paginate = array(			
			'order' => array('InventoryStatus.id' => 'DESC')
		);
		$this->set('inventoryStatuses', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Inventory Status Details');
		if (!$this->InventoryStatus->exists($id)) {
			throw new NotFoundException(__('Invalid inventory status'));
		}
		$options = array('conditions' => array('InventoryStatus.' . $this->InventoryStatus->primaryKey => $id));
		$this->set('inventoryStatus', $this->InventoryStatus->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Inventory Status');
		if ($this->request->is('post')) {
			$this->request->data['InventoryStatus']['created_at'] = $this->current_datetime(); 
			$this->request->data['InventoryStatus']['updated_at'] = $this->current_datetime(); 
			$this->request->data['InventoryStatus']['created_by'] = $this->UserAuth->getUserId();
			$this->InventoryStatus->create();
			if ($this->InventoryStatus->save($this->request->data)) {
				$this->Session->setFlash(__('The inventory status has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The inventory status could not be saved. Please, try again.'), 'flash/error');
			}
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Inventory Status');
        $this->InventoryStatus->id = $id;
		if (!$this->InventoryStatus->exists($id)) {
			throw new NotFoundException(__('Invalid inventory status'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['InventoryStatus']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['InventoryStatus']['updated_at'] = $this->current_datetime(); 
			if ($this->InventoryStatus->save($this->request->data)) {
				$this->Session->setFlash(__('The inventory status has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The inventory status could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('InventoryStatus.' . $this->InventoryStatus->primaryKey => $id));
			$this->request->data = $this->InventoryStatus->find('first', $options);
		}
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
		$this->InventoryStatus->id = $id;
		if (!$this->InventoryStatus->exists()) {
			throw new NotFoundException(__('Invalid inventory status'));
		}
		if ($this->InventoryStatus->delete()) {
			$this->Session->setFlash(__('Inventory status deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Inventory status was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
