<?php
App::uses('AppController', 'Controller');
/**
 * Months Controller
 *
 * @property Month $Month
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MonthsController extends AppController {

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
		$this->set('page_title','Month List');
		$this->Month->recursive = 0;
		$this->set('months', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Month Details');
		if (!$this->Month->exists($id)) {
			throw new NotFoundException(__('Invalid month'));
		}
		$options = array('conditions' => array('Month.' . $this->Month->primaryKey => $id));
		$this->set('month', $this->Month->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Month');
		if ($this->request->is('post')) {
			$this->request->data['Month']['created_at'] = $this->current_datetime();
			$this->request->data['Month']['updated_at'] = $this->current_datetime();
			$this->request->data['Month']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['Month']['YearID'] = 0;
			$this->Month->create();
			if ($this->Month->save($this->request->data)) {
				$this->Session->setFlash(__('The month has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The month could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$fiscalYears = $this->Month->FiscalYear->find('list');
		$this->set(compact('fiscalYears'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Month');
        $this->Month->id = $id;
		if (!$this->Month->exists($id)) {
			throw new NotFoundException(__('Invalid month'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Month']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Month']['updated_at'] = $this->current_datetime();
			$this->request->data['Month']['YearID'] = 0;
			if ($this->Month->save($this->request->data)) {
				$this->Session->setFlash(__('The month has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The month could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$this->Month->recursive = -1;
			$options = array('conditions' => array('Month.' . $this->Month->primaryKey => $id));
			$this->request->data = $this->Month->find('first', $options);
		}
		$fiscalYears = $this->Month->FiscalYear->find('list');
		$this->set(compact('fiscalYears'));
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
		$this->Month->id = $id;
		if (!$this->Month->exists()) {
			throw new NotFoundException(__('Invalid month'));
		}
		if ($this->Month->delete()) {
			$this->Session->setFlash(__('Month deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Month was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
