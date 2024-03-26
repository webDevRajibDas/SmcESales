<?php
App::uses('AppController', 'Controller');
/**
 * SessionTypes Controller
 *
 * @property SessionType $SessionType
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class SessionTypesController extends AppController {

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
		$this->set('page_title','Session type List');
		$this->SessionType->recursive = 0;
		$this->paginate = array('order' => array('SessionType.id' => 'DESC'));
		$this->set('sessionTypes', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Session type Details');
		if (!$this->SessionType->exists($id)) {
			throw new NotFoundException(__('Invalid session type'));
		}
		$options = array('conditions' => array('SessionType.' . $this->SessionType->primaryKey => $id));
		$this->set('sessionType', $this->SessionType->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Session type');
		if ($this->request->is('post')) {
			$this->SessionType->create();
			$this->request->data['SessionType']['created_at'] = $this->current_datetime();
			$this->request->data['SessionType']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->SessionType->save($this->request->data)) {
				$this->Session->setFlash(__('The session type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The session type could not be saved. Please, try again.'), 'flash/error');
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
        $this->set('page_title','Edit Session type');
		$this->SessionType->id = $id;
		if (!$this->SessionType->exists($id)) {
			throw new NotFoundException(__('Invalid session type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['SessionType']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->SessionType->save($this->request->data)) {
				$this->Session->setFlash(__('The session type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The session type could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('SessionType.' . $this->SessionType->primaryKey => $id));
			$this->request->data = $this->SessionType->find('first', $options);
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
		$this->SessionType->id = $id;
		if (!$this->SessionType->exists()) {
			throw new NotFoundException(__('Invalid session type'));
		}
		if ($this->SessionType->delete()) {
			$this->Session->setFlash(__('Session type deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Session type was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
