<?php
App::uses('AppController', 'Controller');
/**
 * DoctorTypes Controller
 *
 * @property DoctorType $DoctorType
 * @property PaginatorComponent $Paginator
 */
class DoctorTypesController extends AppController {

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
		$this->set('page_title','Doctor type List');
		$this->DoctorType->recursive = 0;
		$this->paginate = array('order' => array('DoctorType.id' => 'DESC'));
		$this->set('doctorTypes', $this->paginate());
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Doctor Type');
		if ($this->request->is('post')) {
			$this->DoctorType->create();
			if ($this->DoctorType->save($this->request->data)) {
				$this->Session->setFlash(__('The doctor type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The doctor type could not be saved. Please, try again.'), 'flash/error');
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
        $this->set('page_title','Edit Doctor Type');
		$this->DoctorType->id = $id;
		if (!$this->DoctorType->exists($id)) {
			throw new NotFoundException(__('Invalid doctor type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->DoctorType->save($this->request->data)) {
				$this->Session->setFlash(__('The doctor type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The doctor type could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('DoctorType.' . $this->DoctorType->primaryKey => $id));
			$this->request->data = $this->DoctorType->find('first', $options);
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
		$this->DoctorType->id = $id;
		if (!$this->DoctorType->exists()) {
			throw new NotFoundException(__('Invalid doctor type'));
		}
		if ($this->DoctorType->delete()) {
			$this->Session->setFlash(__('Doctor type deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Doctor type was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
