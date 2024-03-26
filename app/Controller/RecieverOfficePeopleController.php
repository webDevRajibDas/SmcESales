<?php
App::uses('AppController', 'Controller');
/**
 * RecieverOfficePeople Controller
 *
 * @property RecieverOfficePerson $RecieverOfficePerson
 * @property PaginatorComponent $Paginator
 * @property nComponent $n
 * @property SessionComponent $Session
 */
class RecieverOfficePeopleController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator','Session');
	public $uses = array('RecieverOfficePerson','Office','SalesPerson');
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() 
	{		
		$this->set('page_title','Reciever Office Person List');
		
		/* $offices = $this->RecieverOfficePerson->Office->find('list',array('fields'=>array('Office.id','Office.office_name')));
		$this->set(compact('offices')); */
		
		$this->RecieverOfficePerson->recursive = 0;
		$this->paginate = array('order' => array('RecieverOfficePerson.id' => 'DESC'));
		$this->set('recieverOfficePeople', $this->paginate());
		
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Reciever office person');
		if ($this->request->is('post')) {
			$this->RecieverOfficePerson->create();
			if ($this->RecieverOfficePerson->save($this->request->data)) {
				$this->Session->setFlash(__('The reciever office person has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
			
		}
		$offices = $this->RecieverOfficePerson->Office->find('list');
		$salesPeople = $this->RecieverOfficePerson->SalesPerson->find('list',array(
				'conditions' => array('SalesPerson.office_id' => $this->request->data['RecieverOfficePerson']['office_id']),
				'order' => array('SalesPerson.name'=>'asc')
			));
		$this->set(compact('offices','salesPeople'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Reciever office person');
		$this->RecieverOfficePerson->id = $id;
		if (!$this->RecieverOfficePerson->exists($id)) {
			throw new NotFoundException(__('Invalid reciever office person'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->RecieverOfficePerson->save($this->request->data)) {
				$this->Session->setFlash(__('The reciever office person has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$options = array('conditions' => array('RecieverOfficePerson.' . $this->RecieverOfficePerson->primaryKey => $id));
			$this->request->data = $this->RecieverOfficePerson->find('first', $options);
		}
		
		$offices = $this->RecieverOfficePerson->Office->find('list');
		$salesPeople = $this->SalesPerson->find('list', array(
			'fields' => array('SalesPerson.id', 'SalesPerson.name'),
			'conditions' => array('SalesPerson.office_id' => $this->request->data['RecieverOfficePerson']['office_id']),
			'recursive' => -1
		));
		
		$this->set(compact('offices', 'salesPeople'));
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
		$this->RecieverOfficePerson->id = $id;
		if (!$this->RecieverOfficePerson->exists()) {
			throw new NotFoundException(__('Invalid reciever office person'));
		}
		if ($this->RecieverOfficePerson->delete()) {
			$this->Session->setFlash(__('Reciever office person deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Reciever office person was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	

	public function admin_get_sales_person() 
	{
		$this->loadModel('SalesPerson');
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$office_id = $this->request->data['office_id'];
        $SalesPerson = $this->SalesPerson->find('all', array(
			'fields' => array('SalesPerson.id', 'SalesPerson.name'),
			'conditions' => array('SalesPerson.office_id' => $office_id),
			'recursive' => -1
		));
		$data_array = Set::extract($SalesPerson, '{n}.SalesPerson');
		if(!empty($SalesPerson)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
}
