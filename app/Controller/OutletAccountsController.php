<?php
App::uses('AppController', 'Controller');
/**
 * OutletAccounts Controller
 *
 * @property OutletAccount $OutletAccount
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OutletAccountsController extends AppController
{

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
	public function admin_index()
	{
		$this->set('page_title', 'Outlet account List');
		$this->loadModel('Office');
		$office_list = $this->Office->find('list', array('conditions' => array('office_type_id' => 2)));
		$this->OutletAccount->recursive = 0;
		$this->paginate = array('order' => array('OutletAccount.id' => 'DESC'));
		$this->set('outletAccounts', $this->paginate());
		$this->set(compact('office_list'));
	}

	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null)
	{
		$this->set('page_title', 'Outlet account Details');
		if (!$this->OutletAccount->exists($id)) {
			throw new NotFoundException(__('Invalid outlet account'));
		}
		$options = array('conditions' => array('OutletAccount.' . $this->OutletAccount->primaryKey => $id));
		$this->set('outletAccount', $this->OutletAccount->find('first', $options));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->set('page_title', 'Add Outlet account');
		if ($this->request->is('post')) {
			$this->OutletAccount->create();
			$this->request->data['OutletAccount']['created_at'] = $this->current_datetime();
			$this->request->data['OutletAccount']['created_by'] = $this->UserAuth->getUserId();
			if ($this->OutletAccount->save($this->request->data)) {
				$this->Session->setFlash(__('The outlet account has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The outlet account could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$outlets = $this->OutletAccount->Outlet->find('list');
		$this->set(compact('outlets'));
	}

	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null)
	{
		$this->set('page_title', 'Edit Outlet account');
		$this->OutletAccount->id = $id;
		if (!$this->OutletAccount->exists($id)) {
			throw new NotFoundException(__('Invalid outlet account'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['OutletAccount']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->OutletAccount->save($this->request->data)) {
				$this->Session->setFlash(__('The outlet account has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The outlet account could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('OutletAccount.' . $this->OutletAccount->primaryKey => $id));
			$this->request->data = $this->OutletAccount->find('first', $options);
		}
		$outlets = $this->OutletAccount->Outlet->find('list');
		$this->set(compact('outlets'));
	}

	/**
	 * admin_delete method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null)
	{
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->OutletAccount->id = $id;
		if (!$this->OutletAccount->exists()) {
			throw new NotFoundException(__('Invalid outlet account'));
		}
		if ($this->OutletAccount->delete()) {
			$this->Session->setFlash(__('Outlet account deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Outlet account was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
