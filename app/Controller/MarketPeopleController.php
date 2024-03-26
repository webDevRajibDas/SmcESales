<?php
App::uses('AppController', 'Controller');
/**
 * MarketPeople Controller
 *
 * @property MarketPerson $MarketPerson
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MarketPeopleController extends AppController {

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
		$this->set('page_title','Market People List');
		$this->MarketPerson->recursive = 0;
		$this->paginate = array(			
			'order' => array('MarketPerson.id' => 'DESC')
		);
		$this->set('marketPeople', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Market People Details');
		if (!$this->MarketPerson->exists($id)) {
			throw new NotFoundException(__('Invalid market person'));
		}
		$options = array('conditions' => array('MarketPerson.' . $this->MarketPerson->primaryKey => $id));
		$this->set('marketPerson', $this->MarketPerson->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Market People');
		if ($this->request->is('post')) {
			$this->request->data['MarketPerson']['created_at'] = $this->current_datetime(); 
			$this->request->data['MarketPerson']['updated_at'] = $this->current_datetime(); 
			$this->request->data['MarketPerson']['created_by'] = $this->UserAuth->getUserId();
			$this->MarketPerson->create();
			if ($this->MarketPerson->save($this->request->data)) {
				$this->Session->setFlash(__('The market person has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The market person could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$markets = $this->MarketPerson->Market->find('list');
		$salesPeople = $this->MarketPerson->SalesPerson->find('list');
		$this->set(compact('markets', 'salesPeople'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Market People');
        $this->MarketPerson->id = $id;
		if (!$this->MarketPerson->exists($id)) {
			throw new NotFoundException(__('Invalid market person'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['MarketPerson']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['MarketPerson']['updated_at'] = $this->current_datetime(); 
			if ($this->MarketPerson->save($this->request->data)) {
				$this->Session->setFlash(__('The market person has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The market person could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('MarketPerson.' . $this->MarketPerson->primaryKey => $id));
			$this->request->data = $this->MarketPerson->find('first', $options);
		}
		$markets = $this->MarketPerson->Market->find('list');
		$salesPeople = $this->MarketPerson->SalesPerson->find('list');
		$this->set(compact('markets', 'salesPeople'));
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
		$this->MarketPerson->id = $id;
		if (!$this->MarketPerson->exists()) {
			throw new NotFoundException(__('Invalid market person'));
		}
		if ($this->MarketPerson->delete()) {
			$this->Session->setFlash(__('Market person deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Market person was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
