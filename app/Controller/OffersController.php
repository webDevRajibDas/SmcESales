<?php
App::uses('AppController', 'Controller');
/**
 * Offers Controller
 *
 * @property Offer $Offer
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OffersController extends AppController {

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
		$this->Offer->recursive = 0;
		$this->set('offers', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->Offer->exists($id)) {
			throw new NotFoundException(__('Invalid offer'));
		}
		$options = array('conditions' => array('Offer.' . $this->Offer->primaryKey => $id));
		$this->set('offer', $this->Offer->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Offer->create();
			if ($this->Offer->save($this->request->data)) {
				$this->Session->setFlash(__('The offer has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The offer could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$references = $this->Offer->Reference->find('list');
		$combinations = $this->Offer->Combination->find('list');
		$products = $this->Offer->Product->find('list');
		$this->set(compact('references', 'combinations', 'products'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->Offer->id = $id;
		if (!$this->Offer->exists($id)) {
			throw new NotFoundException(__('Invalid offer'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Offer->save($this->request->data)) {
				$this->Session->setFlash(__('The offer has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The offer could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('Offer.' . $this->Offer->primaryKey => $id));
			$this->request->data = $this->Offer->find('first', $options);
		}
		$references = $this->Offer->Reference->find('list');
		$combinations = $this->Offer->Combination->find('list');
		$products = $this->Offer->Product->find('list');
		$this->set(compact('references', 'combinations', 'products'));
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
		$this->Offer->id = $id;
		if (!$this->Offer->exists()) {
			throw new NotFoundException(__('Invalid offer'));
		}
		if ($this->Offer->delete()) {
			$this->Session->setFlash(__('Offer deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Offer was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
