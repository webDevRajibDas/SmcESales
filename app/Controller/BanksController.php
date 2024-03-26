<?php
App::uses('AppController', 'Controller');
/**
 * Designations Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class BanksController extends AppController {

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
		$this->set('page_title','Banks List');
		$this->Bank->recursive = 0;
		$this->paginate = array(			
			'order' => array('bank.id' => 'DESC')
		);
		$this->set('banks', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Bank Details');
		if (!$this->Bank->exists($id)) {
			throw new NotFoundException(__('Invalid  Bank'));
		}
		$options = array('conditions' => array('Bank.' . $this->Bank->primaryKey => $id));
		$this->set('bank', $this->Bank->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Bank');
		if ($this->request->is('post')) {
			$this->Bank->create();
			if ($this->Bank->save($this->request->data)) {
				$this->Session->setFlash(__('The Bank has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The Bank could not be saved. Please, try again.'), 'flash/error');
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
		$this->set('page_title','Edit Bank');
        $this->Bank->id = $id;
		if (!$this->Bank->exists($id)) {
			throw new NotFoundException(__('Invalid bank'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Bank->save($this->request->data)) {
				$this->Session->setFlash(__('The Bank has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The Bank could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Bank.' . $this->Bank->primaryKey => $id));
			$this->request->data = $this->Bank->find('first', $options);
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
		$this->Bank->id = $id;
		if (!$this->Bank->exists()) {
			throw new NotFoundException(__('Invalid bank'));
		}
		if ($this->Bank->delete()) {
			$this->Session->setFlash(__('Bank deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Bank was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
