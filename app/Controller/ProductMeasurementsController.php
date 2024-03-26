<?php
App::uses('AppController', 'Controller');
/**
 * ProductMeasurements Controller
 *
 * @property ProductMeasurement $ProductMeasurement
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductMeasurementsController extends AppController {

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
		$this->loadModel('ProductCategory');
		$this->set('page_title','Product Measurement List');
		$product_categories = $this->ProductCategory->find('list');
		$this->set('product_cat_list',$product_categories);
		$this->ProductMeasurement->recursive = 0;
		$this->paginate = array(
			'order' => array('ProductMeasurement.id'=>'DESC')
		);
		$this->set('productMeasurements', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Product Measurement Details');
		if (!$this->ProductMeasurement->exists($id)) {
			throw new NotFoundException(__('Invalid product measurement'));
		}
		$options = array('conditions' => array('ProductMeasurement.' . $this->ProductMeasurement->primaryKey => $id));
		$this->set('productMeasurement', $this->ProductMeasurement->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Product Measurement');
		if ($this->request->is('post')) {
			$this->request->data['ProductMeasurement']['created_at'] = $this->current_datetime(); 
			$this->request->data['ProductMeasurement']['created_by'] = $this->UserAuth->getUserId();
			$this->ProductMeasurement->create();
			if ($this->ProductMeasurement->save($this->request->data)) {
				$this->Session->setFlash(__('The product measurement has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The product measurement could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$products = $this->ProductMeasurement->Product->find('list');
		$measurementUnits = $this->ProductMeasurement->MeasurementUnit->find('list');
		$this->set(compact('products', 'measurementUnits'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Product Measurement');
        $this->ProductMeasurement->id = $id;
		if (!$this->ProductMeasurement->exists($id)) {
			throw new NotFoundException(__('Invalid product measurement'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['ProductMeasurement']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->ProductMeasurement->save($this->request->data)) {
				$this->Session->setFlash(__('The product measurement has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The product measurement could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('ProductMeasurement.' . $this->ProductMeasurement->primaryKey => $id));
			$this->request->data = $this->ProductMeasurement->find('first', $options);
		}
		$products = $this->ProductMeasurement->Product->find('list');
		$measurementUnits = $this->ProductMeasurement->MeasurementUnit->find('list');
		$this->set(compact('products', 'measurementUnits'));
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
		$this->ProductMeasurement->id = $id;
		if (!$this->ProductMeasurement->exists()) {
			throw new NotFoundException(__('Invalid product measurement'));
		}
		if ($this->ProductMeasurement->delete()) {
			$this->Session->setFlash(__('Product measurement deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Product measurement was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
