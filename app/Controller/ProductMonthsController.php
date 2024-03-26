<?php
App::uses('AppController', 'Controller');
/**
 * Designations Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductMonthsController extends AppController
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
		$this->set('page_title', 'Product Months List');
		$this->ProductMonth->recursive = 0;
		$this->paginate = array(
			'order' => array('ProductMonth.id' => 'DESC')
		);
		$this->set('ProductMonths', $this->paginate());
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
		$this->set('page_title', 'ProductMonth Details');
		if (!$this->ProductMonth->exists($id)) {
			throw new NotFoundException(__('Invalid  ProductMonth'));
		}
		$options = array('conditions' => array('ProductMonth.' . $this->ProductMonth->primaryKey => $id));
		$this->set('ProductMonth', $this->ProductMonth->find('first', $options));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->set('page_title', 'Add Product Month');
		if ($this->request->is('post')) {

			$this->request->data['ProductMonth']['day_or_month'] = 1;
			$this->request->data['ProductMonth']['created_at'] = $this->current_datetime();
			$this->request->data['ProductMonth']['updated_at'] = $this->current_datetime();
			$this->request->data['ProductMonth']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['ProductMonth']['updated_by'] = 0;

			$this->ProductMonth->create();
			if ($this->ProductMonth->save($this->request->data)) {
				$this->Session->setFlash(__('The Product Month has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The ProductMonth could not be saved. Please, try again.'), 'flash/error');
			}
		}

		$this->loadModel('Product');

		$product_list = $this->Product->find('list', array(
			'conditions' => array(
				/* 'Product.is_virtual' => 0 */
				'Product.product_type_id' => 1
			),
			'order' => array('Product.order' => 'ASC'),
			'recursive' => -1
		));

		$this->set(compact('product_list'));
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
		$this->set('page_title', 'Edit ProductMonth');
		$this->ProductMonth->id = $id;
		if (!$this->ProductMonth->exists($id)) {
			throw new NotFoundException(__('Invalid ProductMonth'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			$this->request->data['ProductMonth']['updated_at'] = $this->current_datetime();
			$this->request->data['ProductMonth']['updated_by'] =  $this->UserAuth->getUserId();

			if ($this->ProductMonth->save($this->request->data)) {
				$this->Session->setFlash(__('The Product Month has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The ProductMonth could not be saved. Please, try again.'), 'flash/error');
			}
		} else {

			$options = array('conditions' => array('ProductMonth.' . $this->ProductMonth->primaryKey => $id));
			$this->request->data = $this->ProductMonth->find('first', $options);

			$this->loadModel('Product');

			$product_list = $this->Product->find('list', array(
				'conditions' => array(
					/* 'Product.is_virtual' => 0 */
					'Product.product_type_id' => 1
				),
				'order' => array('Product.order' => 'ASC'),
				'recursive' => -1
			));

			$this->set(compact('product_list'));
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
		$this->ProductMonth->id = $id;
		if (!$this->ProductMonth->exists()) {
			throw new NotFoundException(__('Invalid ProductMonth'));
		}
		if ($this->ProductMonth->delete()) {
			$this->Session->setFlash(__('Product Month deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Product Month was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
