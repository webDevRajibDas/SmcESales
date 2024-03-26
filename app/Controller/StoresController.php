<?php
App::uses('AppController', 'Controller');
/**
 * Stores Controller
 *
 * @property Store $Store
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class StoresController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session','Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {					
		$this->set('page_title','Store List');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$conditions = array();
			$office_conditions = array();
		}else{
			$conditions = array('Store.office_id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		$this->Store->recursive = 0;
		$this->paginate = array('conditions' => $conditions,'order' => array('Store.id' => 'DESC'));
		$this->set('stores', $this->paginate());
		$offices = $this->Store->Office->find('list',array('conditions' => $office_conditions,'order'=>array('office_name'=>'asc')));
		$store_types = $this->Store->StoreType->find('list');
		$this->set(compact('offices','store_types'));
	}


/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Store');
		if ($this->request->is('post')) {

			$this->Store->create();
			$this->request->data['Store']['created_at'] = $this->current_datetime();
			$this->request->data['Store']['updated_at'] = $this->current_datetime();
			$this->request->data['Store']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->Store->save($this->request->data)) {
				$this->Session->setFlash(__('The store has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		}
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$conditions = array();
			$type_conditions = array();
		}else{
			$conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			$type_conditions = array('StoreType.id' => 3);
			$this->request->data['Store']['store_type_id'] = 3;
			$this->request->data['Store']['office_id'] = $this->UserAuth->getOfficeId();
		}
		
		$offices = $this->Store->Office->find('list',array('conditions'=> $conditions,'order'=>array('office_name'=>'asc')));
		$store_types = $this->Store->StoreType->find('list',array('conditions'=> $type_conditions));
		$office_id = (isset($this->request->data['Store']['office_id']) ? $this->request->data['Store']['office_id'] : 0);
		$territories = $this->Store->Territory->find('list',array(
			'conditions' => array('office_id' => $office_id),
			'order' => array('name'=>'asc')
		));
		
		$this->set(compact('store_types','offices','territories'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Store');
		$this->Store->id = $id;
		if (!$this->Store->exists($id)) {
			throw new NotFoundException(__('Invalid store'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Store']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Store']['updated_at'] = $this->current_datetime();
			if ($this->Store->save($this->request->data)) {
				$this->Session->setFlash(__('The store has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$options = array('conditions' => array('Store.' . $this->Store->primaryKey => $id));
			$this->request->data = $this->Store->find('first', $options);
		}
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$conditions = array();
			$type_conditions = array();
		}else{
			$conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			$type_conditions = array('StoreType.id' => 3);
		}
		
		$offices = $this->Store->Office->find('list',array('conditions'=> $conditions,'order'=>array('office_name'=>'asc')));
		$store_types = $this->Store->StoreType->find('list',array('conditions'=> $type_conditions));
		$office_id = ($this->request->is('post') ? $this->request->data['Store']['office_id'] : $this->request->data['Store']['office_id']);
		$territories = $this->Store->Territory->find('list',array(
			'conditions' => array('office_id' => $office_id),
			'order' => array('name'=>'asc')
		));
		$this->set(compact('store_types', 'offices', 'territories'));
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
		$this->Store->id = $id;
		if (!$this->Store->exists()) {
			throw new NotFoundException(__('Invalid store'));
		}
		if ($this->Store->delete()) {
			$this->Session->setFlash(__('Store deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Store was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
