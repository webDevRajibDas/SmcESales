<?php
App::uses('AppController', 'Controller');
/**
 * LocationTypes Controller
 *
 * @property LocationType $LocationType
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class LocationTypesController extends AppController {

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
		$this->set('page_title','Location Type List');
		$this->LocationType->recursive = 0;
		$this->paginate = array(			
			'order' => array('LocationType.id' => 'DESC')
		);
		$this->set('locationTypes', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Location Type Details');
		if (!$this->LocationType->exists($id)) {
			throw new NotFoundException(__('Invalid location type'));
		}
		$options = array('conditions' => array('LocationType.' . $this->LocationType->primaryKey => $id));
		$this->set('locationType', $this->LocationType->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Location Type');
		if ($this->request->is('post')) {
			$this->request->data['LocationType']['created_at'] = $this->current_datetime(); 
			$this->request->data['LocationType']['updated_at'] = $this->current_datetime(); 
			$this->request->data['LocationType']['created_by'] = $this->UserAuth->getUserId();
			$this->LocationType->create();
			if ($this->LocationType->save($this->request->data)) {
				$this->Session->setFlash(__('The location type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The location type could not be saved. Please, try again.'), 'flash/error');
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
		$this->set('page_title','Edit Location Type');
        $this->LocationType->id = $id;
		if (!$this->LocationType->exists($id)) {
			throw new NotFoundException(__('Invalid location type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['LocationType']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['LocationType']['updated_at'] = $this->current_datetime(); 
			if ($this->LocationType->save($this->request->data)) {
				$this->Session->setFlash(__('The location type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The location type could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('LocationType.' . $this->LocationType->primaryKey => $id));
			$this->request->data = $this->LocationType->find('first', $options);
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
		$this->LocationType->id = $id;
		if (!$this->LocationType->exists()) {
			throw new NotFoundException(__('Invalid location type'));
		}
		if ($this->LocationType->delete()) {
			$this->Session->setFlash(__('Location type deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Location type was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
