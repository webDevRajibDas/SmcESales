<?php
App::uses('AppController', 'Controller');
/**
 * Divisions Controller
 *
 * @property Division $Division
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DivisionsController extends AppController {

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
		$this->set('page_title','Division List');
		$this->Division->recursive = 0;
		$this->paginate = array('order' => array('Division.id' => 'DESC'));
		$this->set('divisions', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Division Details');
		if (!$this->Division->exists($id)) {
			throw new NotFoundException(__('Invalid division'));
		}
		$options = array('conditions' => array('Division.' . $this->Division->primaryKey => $id));
		$this->set('division', $this->Division->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Division');
		if ($this->request->is('post')) {
			$this->Division->create();
			$this->request->data['Division']['created_at'] = $this->current_datetime();
			$this->request->data['Division']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['Division']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['District']['updated_at'] = $this->current_datetime();
			if ($this->Division->save($this->request->data)) {
				$this->Session->setFlash(__('The division has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The division could not be saved. Please, try again.'), 'flash/error');
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
        $this->set('page_title','Edit Division');
		$this->Division->id = $id;
		if (!$this->Division->exists($id)) {
			throw new NotFoundException(__('Invalid division'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Division']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Division']['updated_at'] = $this->current_datetime();
			if ($this->Division->save($this->request->data)) {
				$this->Session->setFlash(__('The division has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The division could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Division.' . $this->Division->primaryKey => $id));
			$this->request->data = $this->Division->find('first', $options);
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
		$this->Division->id = $id;
		if (!$this->Division->exists()) {
			throw new NotFoundException(__('Invalid division'));
		}
		if ($this->Division->delete()) {
			$this->Session->setFlash(__('Division deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Division was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
