<?php
App::uses('AppController', 'Controller');
/**
 * BankAccountsController Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class BankAccountsController extends AppController {

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
		$this->set('page_title','Bank Account List');
		$this->BankAccount->recursive = 0;
		$this->paginate = array(			
			'order' => array('bank_accounts.id' => 'DESC')
		);
		$this->set('accounts', $this->paginate());
		
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Bank Account Details');
		if (!$this->BankAccount->exists($id)) {
			throw new NotFoundException(__('Invalid  Bank Account'));
		}
		$options = array('conditions' => array('BankAccount.' . $this->BankAccount->primaryKey => $id));
		$this->set('account', $this->BankAccount->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Bank Account');
		if ($this->request->is('post')) {
			$this->BankAccount->create();
			if ($this->BankAccount->save($this->request->data)) {
				$this->Session->setFlash(__('The Bank Account has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The Bank could not be saved. Please, try again.'), 'flash/error');
			}
			
		}
		$account = $this->BankAccount->BankBranch->find('list');
		$this->set(compact('account'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
	
		$this->set('page_title','Edit Bank Account');
        $this->BankAccount->id = $id;
		if (!$this->BankAccount->exists($id)) {
			throw new NotFoundException(__('Invalid Bank Account'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->BankAccount->save($this->request->data)) {
				$this->Session->setFlash(__('The Bank Account has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The Bank could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('BankAccount.' . $this->BankAccount->primaryKey => $id));
			$this->request->data = $this->BankAccount->find('first', $options);
		}
		$account = $this->BankAccount->BankBranch->find('list');
		$this->set(compact('account'));
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
		$this->BankAccount->id = $id;
		if (!$this->BankAccount->exists()) {
			throw new NotFoundException(__('Invalid Bank Account'));
		}
		if ($this->BankAccount->delete()) {
			$this->Session->setFlash(__('Bank Account deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Bank Account was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
