<?php
App::uses('AppController', 'Controller');
/**
 * CommonMacs Controller
 *
 * @property CommonMac $CommonMac
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CommonMacsController extends AppController {

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
		$this->set('page_title','Common mac List');
		$this->CommonMac->recursive = 0;
		$this->paginate = array('order' => array('CommonMac.id' => 'DESC'));
		$this->set('commonMacs', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Common mac Details');
		if (!$this->CommonMac->exists($id)) {
			throw new NotFoundException(__('Invalid common mac'));
		}
		$options = array('conditions' => array('CommonMac.' . $this->CommonMac->primaryKey => $id));
		$this->set('commonMac', $this->CommonMac->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Common mac');
		if ($this->request->is('post')) {
			$this->CommonMac->create();
			$this->request->data['CommonMac']['created_at'] = $this->current_datetime();
			$this->request->data['CommonMac']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->CommonMac->save($this->request->data)) {
				$this->Session->setFlash(__('The common mac has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The common mac could not be saved. Please, try again.'), 'flash/error');
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
        $this->set('page_title','Edit Common mac');
		$this->CommonMac->id = $id;
		if (!$this->CommonMac->exists($id)) {
			throw new NotFoundException(__('Invalid common mac'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['CommonMac']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->CommonMac->save($this->request->data)) {
				$this->Session->setFlash(__('The common mac has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The common mac could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('CommonMac.' . $this->CommonMac->primaryKey => $id));
			$this->request->data = $this->CommonMac->find('first', $options);
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
		$this->CommonMac->id = $id;
		if (!$this->CommonMac->exists()) {
			throw new NotFoundException(__('Invalid common mac'));
		}
		if ($this->CommonMac->delete()) {
			$this->Session->setFlash(__('Common mac deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Common mac was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
