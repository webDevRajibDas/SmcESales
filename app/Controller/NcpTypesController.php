<?php
App::uses('AppController', 'Controller');
/**
 * MeasurementUnits Controller
 *
 * @property NcpType $NcpType
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class NcpTypesController extends AppController {

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
		$this->set('page_title','Measurement Unit List');
		$this->NcpType->recursive = 0;
		$this->paginate = array(			
			'order' => array('NcpType.id' => 'DESC')
		);
		$this->set('ncpTypes', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Measurement Unit Details');
		if (!$this->NcpType->exists($id)) {
			throw new NotFoundException(__('Invalid measurement unit'));
		}
		$options = array('conditions' => array('NcpType.' . $this->NcpType->primaryKey => $id));
		$this->set('measurementUnit', $this->NcpType->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Ncp Type');
		if ($this->request->is('post')) {
			$this->request->data['NcpType']['created_at'] = $this->current_datetime();
			$this->request->data['NcpType']['updated_at'] = $this->current_datetime();
			$this->request->data['NcpType']['created_by'] = $this->UserAuth->getUserId();
			$this->NcpType->create();
			if ($this->NcpType->save($this->request->data)) {
				$this->Session->setFlash(__('The ncp type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The measurement unit could not be saved. Please, try again.'), 'flash/error');
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
		$this->set('page_title','Edit Measurement Unit');
        $this->NcpType->id = $id;
		if (!$this->NcpType->exists($id)) {
			throw new NotFoundException(__('Invalid measurement unit'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['NcpType']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['NcpType']['updated_at'] = $this->current_datetime();
			if ($this->NcpType->save($this->request->data)) {
				$this->Session->setFlash(__('The ncp type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The measurement unit could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$this->NcpType->recursive = 0;
			$options = array('conditions' => array('NcpType.' . $this->NcpType->primaryKey => $id));
			$this->request->data = $this->NcpType->find('first', $options);
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
		$this->NcpType->id = $id;
		if (!$this->NcpType->exists()) {
			throw new NotFoundException(__('Invalid measurement unit'));
		}
		if ($this->NcpType->delete()) {
			$this->Session->setFlash(__('Measurement unit deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Measurement unit was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
