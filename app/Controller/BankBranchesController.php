<?php
App::uses('AppController', 'Controller');
/**
 * BankBranchController Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class BankBranchesController extends AppController {

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
		$this->set('page_title','Bank Branches List');
		$this->BankBranch->recursive = 1;
		$this->paginate = array(			
			'order' => array('BankBranch.id' => 'DESC')
		);
		//pr($this->paginate());
		$this->set('branches', $this->paginate());		
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Bank Branch Details');
		if (!$this->BankBranch->exists($id)) {
			throw new NotFoundException(__('Invalid  Bank Branches'));
		}
		$options = array('conditions' => array('BankBranch.' . $this->BankBranch->primaryKey => $id));
		$this->set('branch', $this->BankBranch->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Bank Branch');
		if ($this->request->is('post')) {
			$this->BankBranch->create();
			$this->request->data['BankBranch']['updated_at'] = $this->current_datetime();
			if ($this->BankBranch->save($this->request->data)) {
				$this->Session->setFlash(__('The Bank Branch has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The Bank could not be saved. Please, try again.'), 'flash/error');
			}
			
		}
		
		$this->loadModel('Office');
		$this->loadModel('Territory');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$office_conditions = array();			
		}else{
			$office_conditions = array('id' => $this->UserAuth->getOfficeId());
		}
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions,'order'=>array('office_name'=>'asc')));
		$office_id = (isset($this->request->data['Outlet']['office_id']) ? $this->request->data['Outlet']['office_id'] : 0);
		$territories = $this->Territory->find('list',array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
		
		$bank = $this->BankBranch->Bank->find('list',array('fields'=>array('id','name'),'order'=>array('name'=>'asc')));
		$this->set(compact('bank','offices', 'territories'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
	
		$this->set('page_title','Edit Bank Branch');
        $this->BankBranch->id = $id;
		if (!$this->BankBranch->exists($id)) {
			throw new NotFoundException(__('Invalid Bank branch'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['BankBranch']['updated_at'] = $this->current_datetime();
			
			if($this->request->data['BankBranch']['is_common']==1){
				$this->request->data['BankBranch']['territory_id'] = '';
			}
			
			//pr($this->request->data);
			//exit;
			
			if ($this->BankBranch->save($this->request->data)) {
				$this->Session->setFlash(__('The Bank Branch has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The Bank could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('BankBranch.' . $this->BankBranch->primaryKey => $id));
			$this->request->data = $this->BankBranch->find('first', $options);
		}		
		$this->loadModel('Office');
		$this->loadModel('Territory');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$office_conditions = array();			
		}else{
			$office_conditions = array('id' => $this->UserAuth->getOfficeId());
		}
		
		//pr($this->request->data);
		
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions,'order'=>array('office_name'=>'asc')));
		
		if($this->request->data['BankBranch']['office_id']){
			$office_id = $this->request->data['BankBranch']['office_id'];
		}else{
			$office_id = (isset($this->request->data['Territory']['office_id']) ? $this->request->data['Territory']['office_id'] : 0);
		}
		
		$territories = $this->Territory->find('list',array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
		
		$bank = $this->BankBranch->Bank->find('list',array('fields'=>array('id','name'),'order'=>array('name'=>'asc')));
		$this->set(compact('bank','offices', 'territories', 'office_id'));
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
		$this->BankBranch->id = $id;
		if (!$this->BankBranch->exists()) {
			throw new NotFoundException(__('Invalid bank branch'));
		}
		if ($this->BankBranch->delete()) {
			$this->Session->setFlash(__('Bank branch deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Bank branch was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	public function getOfficeName($id=0) 
	{
		if($id)
		{	
			$this->loadModel('Office'); 
		
			$reuslt = $this->Office->find('first', array(
				'conditions' => array(
					'id' => $id
				),
				'recursive' => -1
			));
			
			return $reuslt['Office']['office_name'];
		}
		else
		{
			return false;
		}
	}
}
