<?php
App::uses('AppController', 'Controller');
/**
 * TransactionTypes Controller
 *
 * @property TransactionType $TransactionType
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class TransactionTypesController extends AppController {

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
		$this->set('page_title','Transaction type List');
		$this->TransactionType->recursive = 0;
		$this->paginate = array('order' => array('TransactionType.id' => 'DESC'));
		$this->set('transactionTypes', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Transaction type Details');
		if (!$this->TransactionType->exists($id)) {
			throw new NotFoundException(__('Invalid transaction type'));
		}
		$options = array('conditions' => array('TransactionType.' . $this->TransactionType->primaryKey => $id));
		$this->set('transactionType', $this->TransactionType->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Transaction type');
		if ($this->request->is('post')) {
			$this->TransactionType->create();
			$this->request->data['TransactionType']['created_at'] = $this->current_datetime();
			$this->request->data['TransactionType']['updated_at'] = $this->current_datetime();
			$this->request->data['TransactionType']['created_by'] = $this->UserAuth->getUserId();
			//$this->request->data['TransactionType']['transaction_code']=100;			
			if ($this->TransactionType->save($this->request->data)) {
				$udata['TransactionType']['id']=$this->TransactionType->id;
				$udata['TransactionType']['transaction_code']=100+$udata['TransactionType']['id'];
				$this->TransactionType->save($udata);
				$this->Session->setFlash(__('The transaction type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The transaction type could not be saved. Please, try again.'), 'flash/error');
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
        $this->set('page_title','Edit Transaction type');
		$this->TransactionType->id = $id;
		if (!$this->TransactionType->exists($id)) {
			throw new NotFoundException(__('Invalid transaction type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['TransactionType']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['TransactionType']['updated_at'] = $this->current_datetime();
			if ($this->TransactionType->save($this->request->data)) {
				$this->Session->setFlash(__('The transaction type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The transaction type could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$this->TransactionType->unbindModel(array('hasMany'=>array('Challan','CurrentInventory')));
			$options = array('conditions' => array('TransactionType.' . $this->TransactionType->primaryKey => $id));
			$this->request->data = $this->TransactionType->find('first', $options);
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
		$this->TransactionType->id = $id;
		if (!$this->TransactionType->exists()) {
			throw new NotFoundException(__('Invalid transaction type'));
		}
		if ($this->TransactionType->delete()) {
			$this->Session->setFlash(__('Transaction type deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Transaction type was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
