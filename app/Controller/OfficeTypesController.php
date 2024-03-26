<?php
App::uses('AppController', 'Controller');
/**
 * OfficeTypes Controller
 *
 * @property OfficeType $OfficeType
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OfficeTypesController extends AppController {

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
		$this->set('page_title','Office Type List');
		$this->OfficeType->recursive = 0;
		$this->paginate = array(			
			'order' => array('OfficeType.id' => 'DESC')
		);
		$this->set('officeTypes', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Office Type Details');
		if (!$this->OfficeType->exists($id)) {
			throw new NotFoundException(__('Invalid office type'));
		}
		$options = array('conditions' => array('OfficeType.' . $this->OfficeType->primaryKey => $id));
		$this->set('officeType', $this->OfficeType->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Office Type');
		if ($this->request->is('post')) {
			$this->request->data['OfficeType']['created_at'] = $this->current_datetime(); 
			$this->request->data['OfficeType']['updated_at'] = $this->current_datetime(); 
			$this->request->data['OfficeType']['created_by'] = $this->UserAuth->getUserId();
			$this->OfficeType->create();
			if ($this->OfficeType->save($this->request->data)) {
				$this->Session->setFlash(__('The office type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The office type could not be saved. Please, try again.'), 'flash/error');
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
		$this->set('page_title','Edit Office Type');
        $this->OfficeType->id = $id;
		if (!$this->OfficeType->exists($id)) {
			throw new NotFoundException(__('Invalid office type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['OfficeType']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['OfficeType']['updated_at'] = $this->current_datetime(); 
			if ($this->OfficeType->save($this->request->data)) {
				$this->Session->setFlash(__('The office type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The office type could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('OfficeType.' . $this->OfficeType->primaryKey => $id));
			$this->request->data = $this->OfficeType->find('first', $options);
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
		$this->OfficeType->id = $id;
		if (!$this->OfficeType->exists()) {
			throw new NotFoundException(__('Invalid office type'));
		}
		if ($this->OfficeType->delete()) {
			$this->Session->setFlash(__('Office type deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Office type was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
