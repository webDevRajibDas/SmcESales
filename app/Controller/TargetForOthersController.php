<?php
App::uses('AppController', 'Controller');
/**
 * TargetForOthers Controller
 *
 * @property TargetForOther $TargetForOther
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class TargetForOthersController extends AppController {

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
		$this->TargetForOther->recursive = 0;
		$this->set('targetForOthers', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->TargetForOther->exists($id)) {
			throw new NotFoundException(__('Invalid target for other'));
		}
		$options = array('conditions' => array('TargetForOther.' . $this->TargetForOther->primaryKey => $id));
		$this->set('targetForOther', $this->TargetForOther->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->TargetForOther->create();
			if ($this->TargetForOther->save($this->request->data)) {
				$this->Session->setFlash(__('The target for other has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The target for other could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$targets = $this->TargetForOther->Target->find('list');
		$this->set(compact('targets'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->TargetForOther->id = $id;
		if (!$this->TargetForOther->exists($id)) {
			throw new NotFoundException(__('Invalid target for other'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->TargetForOther->save($this->request->data)) {
				$this->Session->setFlash(__('The target for other has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The target for other could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('TargetForOther.' . $this->TargetForOther->primaryKey => $id));
			$this->request->data = $this->TargetForOther->find('first', $options);
		}
		$targets = $this->TargetForOther->Target->find('list');
		$this->set(compact('targets'));
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
		$this->TargetForOther->id = $id;
		if (!$this->TargetForOther->exists()) {
			throw new NotFoundException(__('Invalid target for other'));
		}
		if ($this->TargetForOther->delete()) {
			$this->Session->setFlash(__('Target for other deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Target for other was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
