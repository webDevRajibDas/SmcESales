<?php
App::uses('AppController', 'Controller');
/**
 * TargetForProductSales Controller
 *
 * @property TargetForProductSale $TargetForProductSale
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class TargetForProductSalesController extends AppController {

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
		$this->TargetForProductSale->recursive = 0;
		$this->set('targetForProductSales', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->TargetForProductSale->exists($id)) {
			throw new NotFoundException(__('Invalid target for product sale'));
		}
		$options = array('conditions' => array('TargetForProductSale.' . $this->TargetForProductSale->primaryKey => $id));
		$this->set('targetForProductSale', $this->TargetForProductSale->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->TargetForProductSale->create();
			if ($this->TargetForProductSale->save($this->request->data)) {
				$this->Session->setFlash(__('The target for product sale has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The target for product sale could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$targets = $this->TargetForProductSale->Target->find('list');
		$periods = $this->TargetForProductSale->Period->find('list');
		$products = $this->TargetForProductSale->Product->find('list');
		$measurementUnits = $this->TargetForProductSale->MeasurementUnit->find('list');
		$this->set(compact('targets', 'periods', 'products', 'measurementUnits'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->TargetForProductSale->id = $id;
		if (!$this->TargetForProductSale->exists($id)) {
			throw new NotFoundException(__('Invalid target for product sale'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->TargetForProductSale->save($this->request->data)) {
				$this->Session->setFlash(__('The target for product sale has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The target for product sale could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('TargetForProductSale.' . $this->TargetForProductSale->primaryKey => $id));
			$this->request->data = $this->TargetForProductSale->find('first', $options);
		}
		$targets = $this->TargetForProductSale->Target->find('list');
		$periods = $this->TargetForProductSale->Period->find('list');
		$products = $this->TargetForProductSale->Product->find('list');
		$measurementUnits = $this->TargetForProductSale->MeasurementUnit->find('list');
		$this->set(compact('targets', 'periods', 'products', 'measurementUnits'));
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
		$this->TargetForProductSale->id = $id;
		if (!$this->TargetForProductSale->exists()) {
			throw new NotFoundException(__('Invalid target for product sale'));
		}
		if ($this->TargetForProductSale->delete()) {
			$this->Session->setFlash(__('Target for product sale deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Target for product sale was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
