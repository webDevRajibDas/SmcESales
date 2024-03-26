<?php
App::uses('AppController', 'Controller');
/**
 * OutletCategories Controller
 *
 * @property OutletCategory $OutletCategory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OutletCategoriesController extends AppController {

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
		$this->set('page_title','Outlet Category List');
		$this->OutletCategory->recursive = 0;
		$this->paginate = array(			
			'order' => array('OutletCategory.id' => 'DESC')
		);
		$this->set('outletCategories', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Outlet Category Details');
		if (!$this->OutletCategory->exists($id)) {
			throw new NotFoundException(__('Invalid outlet category'));
		}
		$options = array('conditions' => array('OutletCategory.' . $this->OutletCategory->primaryKey => $id));
		$this->set('outletCategory', $this->OutletCategory->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Outlet Category');
		if ($this->request->is('post')) {
			$this->request->data['OutletCategory']['created_at'] = $this->current_datetime(); 
			$this->request->data['OutletCategory']['updated_at'] = $this->current_datetime(); 
			$this->request->data['OutletCategory']['created_by'] = $this->UserAuth->getUserId();
			$this->OutletCategory->create();
			if ($this->OutletCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The outlet category has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The outlet category could not be saved. Please, try again.'), 'flash/error');
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
		$this->set('page_title','Edit Outlet Category');
        $this->OutletCategory->id = $id;
		if (!$this->OutletCategory->exists($id)) {
			throw new NotFoundException(__('Invalid outlet category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['OutletCategory']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['OutletCategory']['updated_at'] = $this->current_datetime(); 
			if ($this->OutletCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The outlet category has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The outlet category could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('OutletCategory.' . $this->OutletCategory->primaryKey => $id));
			$this->request->data = $this->OutletCategory->find('first', $options);
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
		$this->OutletCategory->id = $id;
		if (!$this->OutletCategory->exists()) {
			throw new NotFoundException(__('Invalid outlet category'));
		}
		if ($this->OutletCategory->delete()) {
			$this->Session->setFlash(__('Outlet category deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Outlet category was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
