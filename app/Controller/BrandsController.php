<?php
App::uses('AppController', 'Controller');
/**
 * Brands Controller
 *
 * @property Brand $Brand
 * @property PaginatorComponent $Paginator
 */
class BrandsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Brand List');
		$this->Brand->recursive = 0;
		$this->paginate = array(			
			'order' => array('Brand.id' => 'DESC')
		);
		$this->set('brands', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Brand Details');
		if (!$this->Brand->exists($id)) {
			throw new NotFoundException(__('Invalid brand'));
		}
		$options = array('conditions' => array('Brand.' . $this->Brand->primaryKey => $id));
		$this->set('brand', $this->Brand->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Brand');
		if ($this->request->is('post')) {
			$this->request->data['Brand']['created_at'] = $this->current_datetime();
			$this->request->data['Brand']['updated_at'] = $this->current_datetime();
			$this->request->data['Brand']['created_by'] = $this->UserAuth->getUserId();
			$this->Brand->create();
			if ($this->Brand->save($this->request->data)) {
				$this->Session->setFlash(__('The brand has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The brand could not be saved. Please, try again.'), 'flash/error');
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
		$this->set('page_title','Edit Brand');
        $this->Brand->id = $id;
		if (!$this->Brand->exists($id)) {
			throw new NotFoundException(__('Invalid brand'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Brand']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Brand']['updated_at'] = $this->current_datetime();
			if ($this->Brand->save($this->request->data)) {
				$this->Session->setFlash(__('The brand has been updated'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
			}
		} else {
			$options = array('conditions' => array('Brand.' . $this->Brand->primaryKey => $id));
			$this->request->data = $this->Brand->find('first', $options);
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
		$this->Brand->id = $id;
		if (!$this->Brand->exists()) {
			throw new NotFoundException(__('Invalid brand'));
		}
		if ($this->Brand->delete()) {
			$this->flash(__('Brand deleted'), array('action' => 'index'));
		}
		$this->flash(__('Brand was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
}
