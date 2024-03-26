<?php
App::uses('AppController', 'Controller');
/**
 * Targets Controller
 *
 * @property Target $Target
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class TargetsController extends AppController {

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
		$this->Target->recursive = 0;
		$this->set('targets', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->Target->exists($id)) {
			throw new NotFoundException(__('Invalid target'));
		}
		$options = array('conditions' => array('Target.' . $this->Target->primaryKey => $id));
		$this->set('target', $this->Target->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Target->create();
			if ($this->Target->save($this->request->data)) {
				$this->Session->setFlash(__('The target has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The target could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$officeSalesPeople = $this->Target->OfficeSalesPerson->find('list');
		$targetTypes = $this->Target->TargetType->find('list');
		$this->set(compact('officeSalesPeople', 'targetTypes'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->Target->id = $id;
		if (!$this->Target->exists($id)) {
			throw new NotFoundException(__('Invalid target'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Target->save($this->request->data)) {
				$this->Session->setFlash(__('The target has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The target could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Target.' . $this->Target->primaryKey => $id));
			$this->request->data = $this->Target->find('first', $options);
		}
		$officeSalesPeople = $this->Target->OfficeSalesPerson->find('list');
		$targetTypes = $this->Target->TargetType->find('list');
		$this->set(compact('officeSalesPeople', 'targetTypes'));
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
		$this->Target->id = $id;
		if (!$this->Target->exists()) {
			throw new NotFoundException(__('Invalid target'));
		}
		if ($this->Target->delete()) {
			$this->Session->setFlash(__('Target deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Target was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
