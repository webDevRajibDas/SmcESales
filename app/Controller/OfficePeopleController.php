<?php
App::uses('AppController', 'Controller');
/**
 * OfficePeople Controller
 *
 * @property OfficePerson $OfficePerson
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OfficePeopleController extends AppController {

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
		$this->set('page_title','Office People List');
		$this->OfficePerson->recursive = 0;
		$this->paginate = array(			
			'order' => array('OfficePerson.id' => 'DESC')
		);
		$this->set('officePeople', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Office People Details');
		if (!$this->OfficePerson->exists($id)) {
			throw new NotFoundException(__('Invalid office person'));
		}
		$options = array('conditions' => array('OfficePerson.' . $this->OfficePerson->primaryKey => $id));
		$this->set('officePerson', $this->OfficePerson->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Office People');
		if ($this->request->is('post')) {
			$this->request->data['OfficePerson']['effective_date'] = date("Y-m-d",strtotime($this->request->data['OfficePerson']['effective_date']));
			$this->request->data['OfficePerson']['created_at'] = $this->current_datetime(); 
			$this->request->data['OfficePerson']['updated_at'] = $this->current_datetime(); 
			$this->request->data['OfficePerson']['created_by'] = $this->UserAuth->getUserId();
			$this->OfficePerson->create();
			if ($this->OfficePerson->save($this->request->data)) {
				$this->Session->setFlash(__('The office person has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The office person could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$offices = $this->OfficePerson->Office->find('list');
		$salesPeople = $this->OfficePerson->SalesPerson->find('list');
		$this->set(compact('offices', 'salesPeople'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Office People');
        $this->OfficePerson->id = $id;
		if (!$this->OfficePerson->exists($id)) {
			throw new NotFoundException(__('Invalid office person'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['OfficePerson']['effective_date'] = date("Y-m-d",strtotime($this->request->data['OfficePerson']['effective_date']));
			$this->request->data['OfficePerson']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['OfficePerson']['updated_at'] = $this->current_datetime();
			if ($this->OfficePerson->save($this->request->data)) {
				$this->Session->setFlash(__('The office person has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The office person could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$this->OfficePerson->recursive = 0;
			$options = array('conditions' => array('OfficePerson.' . $this->OfficePerson->primaryKey => $id));
			$this->request->data = $this->OfficePerson->find('first', $options);
		}
		$offices = $this->OfficePerson->Office->find('list');
		$salesPeople = $this->OfficePerson->SalesPerson->find('list');
		$this->set(compact('offices', 'salesPeople'));
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
		$this->OfficePerson->id = $id;
		if (!$this->OfficePerson->exists()) {
			throw new NotFoundException(__('Invalid office person'));
		}
		if ($this->OfficePerson->delete()) {
			$this->Session->setFlash(__('Office person deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Office person was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
