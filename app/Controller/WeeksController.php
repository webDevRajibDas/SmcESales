<?php
App::uses('AppController', 'Controller');
/**
 * Weeks Controller
 *
 * @property Week $Week
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class WeeksController extends AppController {

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
		$this->set('page_title','Week List');
		$this->Week->recursive = 0;
		$this->paginate = array('order' => array('Week.id' => 'DESC'));		
		$this->set('weeks', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->Week->exists($id)) {
			throw new NotFoundException(__('Invalid week'));
		}
		$options = array('conditions' => array('Week.' . $this->Week->primaryKey => $id));
		$this->set('week', $this->Week->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Week');
		if ($this->request->is('post')) {
			$this->request->data['Week']['start_date'] = date("Y-m-d",strtotime($this->request->data['Week']['start_date']));
			$this->request->data['Week']['end_date'] = date("Y-m-d",strtotime($this->request->data['Week']['end_date']));
			$this->Week->create();
			$this->request->data['Week']['created_at'] = $this->current_datetime();
			$this->request->data['Week']['updated_at'] = $this->current_datetime();
			$this->request->data['Week']['created_by'] = $this->UserAuth->getUserId();
			if ($this->Week->save($this->request->data)) {
				$this->Session->setFlash(__('The week has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The week could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$months = $this->Week->Month->find('list');
		$this->set(compact('months'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Week');
		$this->Week->id = $id;
		if (!$this->Week->exists($id)) {
			throw new NotFoundException(__('Invalid week'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Week']['start_date'] = date("Y-m-d",strtotime($this->request->data['Week']['start_date']));
			$this->request->data['Week']['end_date'] = date("Y-m-d",strtotime($this->request->data['Week']['end_date']));
			$this->request->data['Week']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Week']['updated_at'] = $this->current_datetime();
			if ($this->Week->save($this->request->data)) {
				$this->Session->setFlash(__('The week has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The week could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Week.' . $this->Week->primaryKey => $id));
			$this->request->data = $this->Week->find('first', $options);
		}
		$months = $this->Week->Month->find('list');
		$this->set(compact('months'));
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
		$this->Week->id = $id;
		if (!$this->Week->exists()) {
			throw new NotFoundException(__('Invalid week'));
		}
		if ($this->Week->delete()) {
			$this->Session->setFlash(__('Week deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Week was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
