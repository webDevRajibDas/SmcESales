<?php
App::uses('AppController', 'Controller');
/**
 * Claims Controller
 *
 * @property Claim $Claim
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ClaimsController extends AppController {

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
		$this->set('page_title','Claim List');
		$this->Claim->recursive = 0;
		$this->paginate = array('order' => array('Claim.id' => 'DESC'));
		$this->set('claims', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Claim Details');
		if (!$this->Claim->exists($id)) {
			throw new NotFoundException(__('Invalid claim'));
		}
		$options = array('conditions' => array('Claim.' . $this->Claim->primaryKey => $id));
		$this->set('claim', $this->Claim->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Claim');
		$this->loadModel('Product');
		$this->loadModel('Store');
		if ($this->request->is('post')) {
			$this->Claim->create();
			$this->request->data['Claim']['created_at'] = $this->current_datetime();
			$this->request->data['Claim']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->Claim->save($this->request->data)) {
				$this->Session->setFlash(__('The claim has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The claim could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$products = $this->Product->find('list',array('order' => array('name'=>'asc')));
		$claimType=array(0=>'Short',1=>'Excess');
		$challans = $this->Claim->Challan->find('list');
		$receiverStore = $this->Store->find('list', array(
			'conditions' => array('store_type_id' => 1,'office_id' => $this->UserAuth->getOfficeParentId()),
			'order' => array('name'=>'asc')
		));
		$this->set(compact('challans','products','claimType','receiverStore'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Claim');
		$this->Claim->id = $id;
		if (!$this->Claim->exists($id)) {
			throw new NotFoundException(__('Invalid claim'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Claim']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->Claim->save($this->request->data)) {
				$this->Session->setFlash(__('The claim has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The claim could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Claim.' . $this->Claim->primaryKey => $id));
			$this->request->data = $this->Claim->find('first', $options);
		}
		$challans = $this->Claim->Challan->find('list');
		$this->set(compact('challans'));
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
		$this->Claim->id = $id;
		if (!$this->Claim->exists()) {
			throw new NotFoundException(__('Invalid claim'));
		}
		if ($this->Claim->delete()) {
			$this->Session->setFlash(__('Claim deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Claim was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}


}
