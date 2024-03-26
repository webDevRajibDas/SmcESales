<?php
App::uses('AppController', 'Controller');
/**
 * Offices Controller
 *
 * @property Office $Office
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OfficesController extends AppController {

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
		$this->set('page_title','Office List');
		$this->Office->recursive = 0;
		$this->paginate = array(			
			'order' => array('Office.id' => 'DESC')
		);
		$this->set('offices', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Office Details');
		if (!$this->Office->exists($id)) {
			throw new NotFoundException(__('Invalid office'));
		}
		$options = array('conditions' => array('Office.' . $this->Office->primaryKey => $id));
		$this->set('office', $this->Office->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Office');
		if ($this->request->is('post')) {
			$this->loadModel('Store');
			$this->request->data['Office']['created_at'] = $this->current_datetime(); 
			$this->request->data['Office']['updated_at'] = $this->current_datetime();
			$this->request->data['Office']['parent_office_id'] = ($this->request->data['Office']['parent_office_id']!='' ? $this->request->data['Office']['parent_office_id'] : 0);
			$this->request->data['Office']['created_by'] = $this->UserAuth->getUserId();


			$this->Office->create();

			if ($this->Office->save($this->request->data)) {
				$this->Session->setFlash(__('The office has been saved'), 'flash/success');
				if($this->request->data['Office']['office_type_id']==1 ||$this->request->data['Office']['office_type_id']==2)
				{
					//create a store automatically in store table
					$this->request->data['Store']['name'] =$this->request->data['Office']['office_name'].'_Store';
					$this->request->data['Store']['store_type_id'] = $this->request->data['Office']['office_type_id'];
					$this->request->data['Store']['office_id'] = $this->Office->getLastInsertID();
					$this->request->data['Store']['created_at'] = $this->current_datetime();
					$this->request->data['Store']['updated_at'] = $this->current_datetime();
					$this->request->data['Store']['created_by'] = $this->UserAuth->getUserId();
					$this->request->data['Store']['updated_by'] = $this->UserAuth->getUserId();

				}
				$this->Store->create();
				if($this->Store->save($this->request->data))
				{
					$this->Session->setFlash(__($this->request->data['Store']['name'].' Store of this office is created automatically'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}

			} else {
				//$this->Session->setFlash(__('The office could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$officeTypes = $this->Office->OfficeType->find('list');
		$parentOffices = $this->Office->find('list');
		$this->set(compact('officeTypes', 'parentOffices'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Office');
        $this->Office->id = $id;
		if (!$this->Office->exists($id)) {
			throw new NotFoundException(__('Invalid office'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Office']['office_head_id'] = 0;
			$this->request->data['Office']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Office']['updated_at'] = $this->current_datetime();
			if ($this->Office->save($this->request->data)) {
				$this->Session->setFlash(__('The office has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The office could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$this->Office->recursive = 0;
			$options = array('conditions' => array('Office.' . $this->Office->primaryKey => $id));
			$this->request->data = $this->Office->find('first', $options);
		}
		$officeTypes = $this->Office->OfficeType->find('list');
		$parentOffices = $this->Office->find('list');
		$this->set(compact('officeTypes', 'parentOffices'));
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
		$this->Office->id = $id;
		if (!$this->Office->exists()) {
			throw new NotFoundException(__('Invalid office'));
		}
		if ($this->Office->delete()) {
			$this->Session->setFlash(__('Office deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Office was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
