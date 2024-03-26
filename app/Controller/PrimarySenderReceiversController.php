<?php
App::uses('AppController', 'Controller');
/**
 * Weeks Controller
 *
 * @property Client $Client
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class PrimarySenderReceiversController extends AppController {

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
		$this->set('page_title','Sender/Receiver Lists');
		$this->PrimarySenderReceiver->recursive = 0;
		$this->paginate = array('order' => array('PrimarySenderReceiver.id' => 'DESC'));		
		$this->set('sender_receivers', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		// if (!$this->Client->exists($id)) {
		// 	throw new NotFoundException(__('Invalid client'));
		// }
		// $options = array('conditions' => array('Client.' . $this->Client->primaryKey => $id));
		// $this->set('clients', $this->Client->find('first', $options));
	}


/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Sender/Receiver');
		if ($this->request->is('post')) {
			$this->PrimarySenderReceiver->create();
			$this->request->data['PrimarySenderReceiver']['created_at'] = $this->current_datetime();
			$this->request->data['PrimarySenderReceiver']['updated_at'] = $this->current_datetime();
			$this->request->data['PrimarySenderReceiver']['created_by'] = $this->UserAuth->getUserId();
			if ($this->PrimarySenderReceiver->save($this->request->data)) {
				$this->Session->setFlash(__('The Sender/Receiver has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Sender/Receiver could not be saved. Please, try again.'), 'flash/error');
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
       $this->set('page_title','Edit Sender/Receiver');
		 $this->PrimarySenderReceiver->id = $id;
		if (!$this->PrimarySenderReceiver->exists($id)) {
			throw new NotFoundException(__('Invalid Sender/Receiver'));
		 }
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['PrimarySenderReceiver']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['PrimarySenderReceiver']['updated_at'] = $this->current_datetime();
			if ($this->PrimarySenderReceiver->save($this->request->data)) {
				$this->Session->setFlash(__('The Sender/Receiver has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Sender/Receiver could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('PrimarySenderReceiver.' . $this->PrimarySenderReceiver->primaryKey => $id));
			$this->request->data = $this->PrimarySenderReceiver->find('first', $options);
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
		$this->PrimarySenderReceiver->id = $id;
		if (!$this->PrimarySenderReceiver->exists()) {
			throw new NotFoundException(__('Invalid Sender/Receiver'));
		}
		if ($this->PrimarySenderReceiver->delete()) {
			$this->Session->setFlash(__('Sender/Receiver deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Sender/Receiver was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
