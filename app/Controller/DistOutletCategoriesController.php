<?php
App::uses('AppController', 'Controller');
/**
 * DistOutletCategories Controller
 *
 * @property DistOutletCategory $DistOutletCategory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistOutletCategoriesController extends AppController {

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
		$this->DistOutletCategory->recursive = 0;
		$this->paginate = array(			
			'order' => array('DistOutletCategory.id' => 'DESC')
		);
		$this->set('results', $this->paginate());
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
		if (!$this->DistOutletCategory->exists($id)) {
			throw new NotFoundException(__('Invalid outlet category'));
		}
		$options = array('conditions' => array('DistOutletCategory.' . $this->DistOutletCategory->primaryKey => $id));
		$this->set('outletCategory', $this->DistOutletCategory->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Outlet Category');
		if ($this->request->is('post')) {
			$this->request->data['DistOutletCategory']['created_at'] = $this->current_datetime(); 
			$this->request->data['DistOutletCategory']['updated_at'] = $this->current_datetime(); 
			$this->request->data['DistOutletCategory']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['DistOutletCategory']['updated_by'] = $this->UserAuth->getUserId();
			$this->DistOutletCategory->create();
			if ($this->DistOutletCategory->save($this->request->data)) {
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
        $this->DistOutletCategory->id = $id;
		if (!$this->DistOutletCategory->exists($id)) {
			throw new NotFoundException(__('Invalid outlet category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['DistOutletCategory']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['DistOutletCategory']['updated_at'] = $this->current_datetime(); 
			if ($this->DistOutletCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The outlet category has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The outlet category could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('DistOutletCategory.' . $this->DistOutletCategory->primaryKey => $id));
			$this->request->data = $this->DistOutletCategory->find('first', $options);
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
		$this->DistOutletCategory->id = $id;
		if (!$this->DistOutletCategory->exists()) {
			throw new NotFoundException(__('Invalid outlet category'));
		}
		if ($this->DistOutletCategory->delete()) {
			$this->Session->setFlash(__('Outlet category deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Outlet category was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
