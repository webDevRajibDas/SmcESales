<?php
App::uses('AppController', 'Controller');
/**
 * TargetTypes Controller
 *
 * @property TargetType $TargetType
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class TargetTypesController extends AppController {

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
		$this->set('page_title','Target Type List');
		$this->TargetType->recursive = 0;
		$this->set('targetTypes', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Target Type Details');
		if (!$this->TargetType->exists($id)) {
			throw new NotFoundException(__('Invalid target type'));
		}
		$options = array('conditions' => array('TargetType.' . $this->TargetType->primaryKey => $id));
		$this->set('targetType', $this->TargetType->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Target Type');
		if ($this->request->is('post')) {
			$this->request->data['TargetType']['created_at'] = $this->current_datetime();
			$this->request->data['TargetType']['updated_at'] = $this->current_datetime();
			$this->request->data['TargetType']['created_by'] = $this->UserAuth->getUserId();
			$this->TargetType->create();
			if ($this->TargetType->save($this->request->data)) {
				$this->Session->setFlash(__('The target type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The target type could not be saved. Please, try again.'), 'flash/error');
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
		$this->set('page_title','Edit Target Type');
        $this->TargetType->id = $id;
		if (!$this->TargetType->exists($id)) {
			throw new NotFoundException(__('Invalid target type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['TargetType']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['TargetType']['updated_at'] = $this->current_datetime();
			if ($this->TargetType->save($this->request->data)) {
				$this->Session->setFlash(__('The target type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The target type could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$this->TargetType->recursive = 0;
			$options = array('conditions' => array('TargetType.' . $this->TargetType->primaryKey => $id));
			$this->request->data = $this->TargetType->find('first', $options);
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
		$this->TargetType->id = $id;
		if (!$this->TargetType->exists()) {
			throw new NotFoundException(__('Invalid target type'));
		}
		if ($this->TargetType->delete()) {
			$this->Session->setFlash(__('Target type deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Target type was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
