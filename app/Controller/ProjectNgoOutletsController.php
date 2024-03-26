<?php
App::uses('AppController', 'Controller');
/**
 * ProjectNgoOutlets Controller
 *
 * @property ProjectNgoOutlet $ProjectNgoOutlet
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProjectNgoOutletsController extends AppController {

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
		$this->set('page_title','Project Ngo Outlet List');
		$this->ProjectNgoOutlet->recursive = 0;
		$this->paginate = array(			
			'order' => array('ProjectNgoOutlet.id' => 'DESC')
		);
		$this->set('projectNgoOutlets', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Project Ngo Outlet Details');
		if (!$this->ProjectNgoOutlet->exists($id)) {
			throw new NotFoundException(__('Invalid project ngo outlet'));
		}
		$options = array('conditions' => array('ProjectNgoOutlet.' . $this->ProjectNgoOutlet->primaryKey => $id));
		$this->set('projectNgoOutlet', $this->ProjectNgoOutlet->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Project Ngo Outlet');
		if ($this->request->is('post')) {
			$this->request->data['ProjectNgoOutlet']['created_at'] = $this->current_datetime();
			$this->request->data['ProjectNgoOutlet']['updated_at'] = $this->current_datetime();
			$this->request->data['ProjectNgoOutlet']['created_by'] = $this->UserAuth->getUserId();
			$this->ProjectNgoOutlet->create();
			if ($this->ProjectNgoOutlet->save($this->request->data)) {
				$this->Session->setFlash(__('The project ngo outlet has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The project ngo outlet could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$projects = $this->ProjectNgoOutlet->Project->find('list');
		$outlets = $this->ProjectNgoOutlet->Outlet->find('list');
		$this->set(compact('projects', 'outlets'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Project Ngo Outlet');
        $this->ProjectNgoOutlet->id = $id;
		if (!$this->ProjectNgoOutlet->exists($id)) {
			throw new NotFoundException(__('Invalid project ngo outlet'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['ProjectNgoOutlet']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['ProjectNgoOutlet']['updated_at'] = $this->current_datetime();
			if ($this->ProjectNgoOutlet->save($this->request->data)) {
				$this->Session->setFlash(__('The project ngo outlet has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The project ngo outlet could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$this->ProjectNgoOutlet->recursive = -1;
			$options = array('conditions' => array('ProjectNgoOutlet.' . $this->ProjectNgoOutlet->primaryKey => $id));
			$this->request->data = $this->ProjectNgoOutlet->find('first', $options);
		}
		$projects = $this->ProjectNgoOutlet->Project->find('list');
		$outlets = $this->ProjectNgoOutlet->Outlet->find('list');
		$this->set(compact('projects', 'outlets'));
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
		$this->ProjectNgoOutlet->id = $id;
		if (!$this->ProjectNgoOutlet->exists()) {
			throw new NotFoundException(__('Invalid project ngo outlet'));
		}
		if ($this->ProjectNgoOutlet->delete()) {
			$this->Session->setFlash(__('Project ngo outlet deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Project ngo outlet was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
