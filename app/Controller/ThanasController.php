<?php
App::uses('AppController', 'Controller');
/**
 * Thanas Controller
 *
 * @property Thana $Thana
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ThanasController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() 
	{
		$this->set('page_title','Thana List');
		
		
		$districts = $this->Thana->District->find('list');
		$this->set(compact('districts'));
		
		
		$this->Thana->recursive = 0;
		$this->paginate = array(			
			'order' => array('Thana.id' => 'DESC')
		);
		$this->set('thanas', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Thana Details');
		if (!$this->Thana->exists($id)) {
			throw new NotFoundException(__('Invalid thana'));
		}
		$options = array('conditions' => array('Thana.' . $this->Thana->primaryKey => $id));
		$this->set('thana', $this->Thana->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Thana');
		if ($this->request->is('post')) {
			$this->request->data['Thana']['created_at'] = $this->current_datetime(); 
			$this->request->data['Thana']['updated_at'] = $this->current_datetime(); 
			$this->request->data['Thana']['created_by'] = $this->UserAuth->getUserId();
			$this->Thana->create();
			if ($this->Thana->save($this->request->data)) {
				$this->Session->setFlash(__('The thana has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The thana could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$districts = $this->Thana->District->find('list');
		$this->set(compact('districts'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Thana');
        $this->Thana->id = $id;
		if (!$this->Thana->exists($id)) {
			throw new NotFoundException(__('Invalid thana'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Thana']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Thana']['updated_at'] = $this->current_datetime();
			if ($this->Thana->save($this->request->data)) {
				$this->Session->setFlash(__('The thana has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The thana could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Thana.' . $this->Thana->primaryKey => $id));
			$this->request->data = $this->Thana->find('first', $options);
		}
		$districts = $this->Thana->District->find('list');
		$this->set(compact('districts'));
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
		$this->Thana->id = $id;
		if (!$this->Thana->exists()) {
			throw new NotFoundException(__('Invalid thana'));
		}
		if ($this->Thana->delete()) {
			$this->Session->setFlash(__('Thana deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Thana was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
