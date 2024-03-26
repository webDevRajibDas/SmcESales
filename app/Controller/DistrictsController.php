<?php
App::uses('AppController', 'Controller');
/**
 * Districts Controller
 *
 * @property District $District
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistrictsController extends AppController {
/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','District List');
		$this->District->recursive = 0;
		$this->paginate = array(
			'order' => array('District.id' => 'DESC')
		);
		$this->set('districts', $this->paginate());
		$divisions = $this->District->Division->find('list');
		$this->set(compact('divisions'));
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Institute Details');
		if (!$this->District->exists($id)) {
			throw new NotFoundException(__('Invalid district'));
		}
		$options = array('conditions' => array('District.' . $this->District->primaryKey => $id));
		$this->set('district', $this->District->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Institute');
		if ($this->request->is('post')) {
			$this->request->data['District']['created_at'] = $this->current_datetime(); 
			$this->request->data['District']['updated_at'] = $this->current_datetime(); 
			$this->request->data['District']['created_by'] = $this->UserAuth->getUserId();
			$this->District->create();
			if ($this->District->save($this->request->data)) {
				$this->Session->setFlash(__('The district has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The district could not be saved. Please, try again.'), 'flash/error');
			}
		}

		$divisions = $this->District->Division->find('list');
		$this->set(compact('divisions'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Institute');
        $this->District->id = $id;
		if (!$this->District->exists($id)) {
			throw new NotFoundException(__('Invalid district'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['District']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['District']['updated_at'] = $this->current_datetime(); 
			if ($this->District->save($this->request->data)) {
				$this->Session->setFlash(__('The district has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The district could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('District.' . $this->District->primaryKey => $id));
			$this->request->data = $this->District->find('first', $options);
			$divisions = $this->District->Division->find('list');
			$this->set(compact('divisions'));
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
		$this->District->id = $id;
		if (!$this->District->exists()) {
			throw new NotFoundException(__('Invalid district'));
		}
		if ($this->District->delete()) {
			$this->Session->setFlash(__('District deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('District was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
