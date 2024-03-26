<?php
App::uses('AppController', 'Controller');
/**
 * MessageCategories Controller
 *
 * @property MessageCategory $MessageCategory
 * @property PaginatorComponent $Paginator
 */
class MessageCategoriesController extends AppController {

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
		$this->set('page_title','Message category List');
		$this->MessageCategory->recursive = 0;
		$this->paginate = array('order' => array('MessageCategory.id' => 'DESC'));
		$this->set('messageCategories', $this->paginate());
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Message category');
		if ($this->request->is('post')) {
			$this->MessageCategory->create();
			$this->request->data['MessageCategory']['created_at'] = $this->current_datetime();
			$this->request->data['MessageCategory']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->MessageCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The message category has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
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
        $this->set('page_title','Edit Message category');
		$this->MessageCategory->id = $id;
		if (!$this->MessageCategory->exists($id)) {
			throw new NotFoundException(__('Invalid message category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['MessageCategory']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->MessageCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The message category has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$options = array('conditions' => array('MessageCategory.' . $this->MessageCategory->primaryKey => $id));
			$this->request->data = $this->MessageCategory->find('first', $options);
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
		$this->MessageCategory->id = $id;
		if (!$this->MessageCategory->exists()) {
			throw new NotFoundException(__('Invalid message category'));
		}
		if ($this->MessageCategory->delete()) {
			$this->Session->setFlash(__('Message category deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Message category was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
