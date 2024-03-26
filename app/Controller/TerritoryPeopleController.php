<?php
App::uses('AppController', 'Controller');
/**
 * TerritoryPeople Controller
 *
 * @property TerritoryPerson $TerritoryPerson
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class TerritoryPeopleController extends AppController {

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
		$this->set('page_title','Territory person List');
		$this->TerritoryPerson->recursive = 0;
		$this->paginate = array('order' => array('TerritoryPerson.id' => 'DESC'));
		$this->set('territoryPeople', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Territory person Details');
		if (!$this->TerritoryPerson->exists($id)) {
			throw new NotFoundException(__('Invalid territory person'));
		}
		$options = array('conditions' => array('TerritoryPerson.' . $this->TerritoryPerson->primaryKey => $id));
		$this->set('territoryPerson', $this->TerritoryPerson->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Territory person');
		if ($this->request->is('post')) {
			$this->TerritoryPerson->create();
			$this->request->data['TerritoryPerson']['created_at'] = $this->current_datetime();
			$this->request->data['TerritoryPerson']['updated_at'] = $this->current_datetime();
			$this->request->data['TerritoryPerson']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->TerritoryPerson->save($this->request->data)) {
				$this->Session->setFlash(__('The territory person has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The territory person could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$territories = $this->TerritoryPerson->Territory->find('list');
		$salesPeople = $this->TerritoryPerson->SalesPerson->find('list');
		$this->set(compact('territories', 'salesPeople'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Territory person');
		$this->TerritoryPerson->id = $id;
		if (!$this->TerritoryPerson->exists($id)) {
			throw new NotFoundException(__('Invalid territory person'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['TerritoryPerson']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['TerritoryPerson']['updated_at'] = $this->current_datetime();
			if ($this->TerritoryPerson->save($this->request->data)) {
				$this->Session->setFlash(__('The territory person has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The territory person could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('TerritoryPerson.' . $this->TerritoryPerson->primaryKey => $id));
			$this->request->data = $this->TerritoryPerson->find('first', $options);
		}
		$territories = $this->TerritoryPerson->Territory->find('list');
		$salesPeople = $this->TerritoryPerson->SalesPerson->find('list');
		$this->set(compact('territories', 'salesPeople'));
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
		$this->TerritoryPerson->id = $id;
		if (!$this->TerritoryPerson->exists()) {
			throw new NotFoundException(__('Invalid territory person'));
		}
		if ($this->TerritoryPerson->delete()) {
			$this->Session->setFlash(__('Territory person deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Territory person was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
