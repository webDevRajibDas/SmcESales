<?php
App::uses('AppController', 'Controller');
/**
 * MeasurementUnits Controller
 *
 * @property MeasurementUnit $MeasurementUnit
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MeasurementUnitsController extends AppController {

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
		$this->MeasurementUnit->recursive = 0;
		$this->paginate = array(			
			'order' => array('MeasurementUnit.id' => 'DESC')
		);
		$this->set('measurementUnits', $this->paginate());
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
		if (!$this->MeasurementUnit->exists($id)) {
			throw new NotFoundException(__('Invalid measurement unit'));
		}
		$options = array('conditions' => array('MeasurementUnit.' . $this->MeasurementUnit->primaryKey => $id));
		$this->set('measurementUnit', $this->MeasurementUnit->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Measurement Unit');
		if ($this->request->is('post')) {
			$this->request->data['MeasurementUnit']['created_at'] = $this->current_datetime();
			$this->request->data['MeasurementUnit']['updated_at'] = $this->current_datetime();
			$this->request->data['MeasurementUnit']['created_by'] = $this->UserAuth->getUserId();
			$this->MeasurementUnit->create();
			if ($this->MeasurementUnit->save($this->request->data)) {
				$this->Session->setFlash(__('The measurement unit has been saved'), 'flash/success');
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
        $this->MeasurementUnit->id = $id;
		if (!$this->MeasurementUnit->exists($id)) {
			throw new NotFoundException(__('Invalid measurement unit'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['MeasurementUnit']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['MeasurementUnit']['updated_at'] = $this->current_datetime();
			if ($this->MeasurementUnit->save($this->request->data)) {
				$this->Session->setFlash(__('The measurement unit has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The measurement unit could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$this->MeasurementUnit->recursive = 0;
			$options = array('conditions' => array('MeasurementUnit.' . $this->MeasurementUnit->primaryKey => $id));
			$this->request->data = $this->MeasurementUnit->find('first', $options);
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
		$this->MeasurementUnit->id = $id;
		if (!$this->MeasurementUnit->exists()) {
			throw new NotFoundException(__('Invalid measurement unit'));
		}
		if ($this->MeasurementUnit->delete()) {
			$this->Session->setFlash(__('Measurement unit deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Measurement unit was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
