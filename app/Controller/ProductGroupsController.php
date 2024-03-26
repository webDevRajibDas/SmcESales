<?php
App::uses('AppController', 'Controller');
/**
 * ProductGroups Controller
 *
 * @property ProductGroup $ProductGroup
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductGroupsController extends AppController
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
		// pr('here');exit;
		$this->set('page_title', 'Product group List');
		$this->ProductGroup->recursive = 0;
		$this->paginate = array('order' => array('ProductGroup.id' => 'DESC'));
		$this->set('productGroups', $this->paginate());
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
		$this->set('page_title', 'Product group Details');
		if (!$this->ProductGroup->exists($id)) {
			throw new NotFoundException(__('Invalid product group'));
		}
		$options = array('conditions' => array('ProductGroup.' . $this->ProductGroup->primaryKey => $id));
		$this->set('productGroup', $this->ProductGroup->find('first', $options));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->set('page_title', 'Add Product group');
		if ($this->request->is('post')) {
			$this->ProductGroup->create();
			$this->request->data['ProductGroup']['created_at'] = $this->current_datetime();
			$this->request->data['ProductGroup']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['ProductGroup']['updated_at'] = $this->current_datetime();
			$this->request->data['ProductGroup']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->ProductGroup->save($this->request->data)) {
				$this->Session->setFlash(__('The product group has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The product group could not be saved. Please, try again.'), 'flash/error');
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
	public function admin_edit($id = null)
	{
		$this->set('page_title', 'Edit Product group');
		$this->ProductGroup->id = $id;
		if (!$this->ProductGroup->exists($id)) {
			throw new NotFoundException(__('Invalid product group'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['ProductGroup']['updated_at'] = $this->current_datetime();
			$this->request->data['ProductGroup']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->ProductGroup->save($this->request->data)) {
				$this->Session->setFlash(__('The product group has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The product group could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('ProductGroup.' . $this->ProductGroup->primaryKey => $id));
			$this->request->data = $this->ProductGroup->find('first', $options);
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
	public function admin_delete($id = null)
	{
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ProductGroup->id = $id;
		if (!$this->ProductGroup->exists()) {
			throw new NotFoundException(__('Invalid product group'));
		}
		if ($this->ProductGroup->delete()) {
			$this->Session->setFlash(__('Product group deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Product group was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
