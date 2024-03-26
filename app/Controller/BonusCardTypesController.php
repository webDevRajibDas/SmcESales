<?php
App::uses('AppController', 'Controller');
/**
 * BonusCardTypes Controller
 *
 * @property BonusCardType $BonusCardType
 * @property PaginatorComponent $Paginator
 */
class BonusCardTypesController extends AppController {

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
		$this->set('page_title','Bonus card type List');
		$this->BonusCardType->recursive = 0;
		$this->paginate = array('order' => array('BonusCardType.id' => 'DESC'));
		$this->set('bonusCardTypes', $this->paginate());
	}


/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Bonus card type');
		if ($this->request->is('post')) {
			$this->BonusCardType->create();
			$this->request->data['BonusCardType']['created_at'] = $this->current_datetime();
			$this->request->data['BonusCardType']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->BonusCardType->save($this->request->data)) {
				$this->Session->setFlash(__('The bonus card type has been saved'), 'flash/success');
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
        $this->set('page_title','Edit Bonus card type');
		$this->BonusCardType->id = $id;
		if (!$this->BonusCardType->exists($id)) {
			throw new NotFoundException(__('Invalid bonus card type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['BonusCardType']['updated_at'] = $this->current_datetime();
			$this->request->data['BonusCardType']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->BonusCardType->save($this->request->data)) {
				$this->Session->setFlash(__('The bonus card type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$options = array('conditions' => array('BonusCardType.' . $this->BonusCardType->primaryKey => $id));
			$this->request->data = $this->BonusCardType->find('first', $options);
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
		$this->BonusCardType->id = $id;
		if (!$this->BonusCardType->exists()) {
			throw new NotFoundException(__('Invalid bonus card type'));
		}
		if ($this->BonusCardType->delete()) {
			$this->Session->setFlash(__('Bonus card type deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Bonus card type was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
