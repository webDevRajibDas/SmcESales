<?php
App::uses('AppController', 'Controller');
/**
 * Brands Controller
 *
 * @property StoreType $StoreType
 * @property PaginatorComponent $Paginator
 */
class StoreTypesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Store Type List');
		$this->StoreType->recursive = 0;
		$this->paginate = array(			
			'order' => array('StoreType.id' => 'DESC')
		);
		$this->set('storeTypes', $this->paginate());
	}


/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Store Type');
		if ($this->request->is('post')) {
			$this->StoreType->create();
			if ($this->StoreType->save($this->request->data)) {
				$this->Session->setFlash(__('The Stote type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The brand could not be saved. Please, try again.'), 'flash/error');
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
		$this->set('page_title','Edit Store Type');
        $this->StoreType->id = $id;
		if (!$this->StoreType->exists($id)) {
			throw new NotFoundException(__('Invalid brand'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->StoreType->save($this->request->data)) {
				$this->Session->setFlash(__('The Store type has been updated'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
			}
		} else {
			$options = array('conditions' => array('StoreType.' . $this->StoreType->primaryKey => $id));
			$this->request->data = $this->StoreType->find('first', $options);
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
		$this->StoreType->id = $id;
		if (!$this->StoreType->exists()) {
			throw new NotFoundException(__('Invalid brand'));
		}
		if ($this->StoreType->delete()) {
			$this->flash(__('Store Type deleted'), array('action' => 'index'));
		}
		$this->flash(__('StoreType was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
}
