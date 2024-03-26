<?php
App::uses('AppController', 'Controller');
/**
 * Projects Controller
 *
 * @property Project $Project
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProjectsController extends AppController {

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
		$this->set('page_title','Project List');
		$this->Project->recursive = 0;
		$this->paginate = array('order' => array('Project.id' => 'DESC'));
		$this->set('projects', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Project Details');
		if (!$this->Project->exists($id)) {
			throw new NotFoundException(__('Invalid project'));
		}
		$options = array('conditions' => array('Project.' . $this->Project->primaryKey => $id));
		$this->set('project', $this->Project->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Project');
		if ($this->request->is('post')) {
			$this->request->data['Project']['start_date'] = date("Y-m-d",strtotime($this->request->data['Project']['start_date']));
			$this->request->data['Project']['end_date'] = date("Y-m-d",strtotime($this->request->data['Project']['end_date']));			
			$this->Project->create();
			$this->request->data['Project']['created_at'] = $this->current_datetime();
			$this->request->data['Project']['updated_at'] = $this->current_datetime();
			$this->request->data['Project']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash(__('The project has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The project could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$institutes = $this->Project->Institute->find('list');
		$this->set(compact('institutes'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Project');
		$this->Project->id = $id;
		if (!$this->Project->exists($id)) {
			throw new NotFoundException(__('Invalid project'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Project']['start_date'] = date("Y-m-d",strtotime($this->request->data['Project']['start_date']));
			$this->request->data['Project']['end_date'] = date("Y-m-d",strtotime($this->request->data['Project']['end_date']));	
			$this->request->data['Project']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Project']['updated_at'] = $this->current_datetime();
			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash(__('The project has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The project could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Project.' . $this->Project->primaryKey => $id));
			$this->request->data = $this->Project->find('first', $options);
		}
		$institutes = $this->Project->Institute->find('list');
		$this->set(compact('institutes'));
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
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project'));
		}
		if ($this->Project->delete()) {
			$this->Session->setFlash(__('Project deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Project was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
