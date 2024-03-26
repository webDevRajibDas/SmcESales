<?php
App::uses('AppController', 'Controller');
/**
 * BonusCards Controller
 *
 * @property BonusCard $BonusCard
 * @property PaginatorComponent $Paginator
 */
class BonusCardsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator','Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Bonus card List');
		$this->BonusCard->recursive = 0;
		$this->paginate = array('order' => array('BonusCard.id' => 'DESC'));
		$this->set('bonusCards', $this->paginate());
		$fiscalYears = $this->BonusCard->FiscalYear->find('list',array('fields'=>array('year_code')));
		$bonusCardTypes = $this->BonusCard->BonusCardType->find('list');
		$products = $this->BonusCard->Product->find('list',array('order'=>array('order'=>'asc')));
		$this->set(compact('fiscalYears', 'bonusCardTypes', 'products'));
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Bonus card Details');
		if (!$this->BonusCard->exists($id)) {
			throw new NotFoundException(__('Invalid bonus card'));
		}
		$options = array('conditions' => array('BonusCard.' . $this->BonusCard->primaryKey => $id));
		$this->set('bonusCard', $this->BonusCard->find('first', $options));

	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Bonus card');
		if ($this->request->is('post')) {
			$this->BonusCard->create();
			$this->request->data['BonusCard']['created_at'] = $this->current_datetime();
			$this->request->data['BonusCard']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['BonusCard']['updated_at'] = $this->current_datetime();
			$this->request->data['BonusCard']['updated_by'] = $this->UserAuth->getUserId();			
			if ($this->BonusCard->save($this->request->data)) {
				$this->Session->setFlash(__('The bonus card has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		}
		$fiscalYears = $this->BonusCard->FiscalYear->find('list',array('fields'=>array('year_code')));
		$bonusCardTypes = $this->BonusCard->BonusCardType->find('list');
		$products = $this->BonusCard->Product->find('list',array('order'=>array('order'=>'asc')));
		$this->set(compact('fiscalYears', 'bonusCardTypes', 'products'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Bonus card');
		$this->BonusCard->id = $id;
		if (!$this->BonusCard->exists($id)) {
			throw new NotFoundException(__('Invalid bonus card'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['BonusCard']['updated_at'] = $this->current_datetime();
			$this->request->data['BonusCard']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->BonusCard->save($this->request->data)) {
				$this->Session->setFlash(__('The bonus card has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$options = array('conditions' => array('BonusCard.' . $this->BonusCard->primaryKey => $id));
			$this->request->data = $this->BonusCard->find('first', $options);
		}
		$fiscalYears = $this->BonusCard->FiscalYear->find('list',array('fields'=>array('year_code')));
		$bonusCardTypes = $this->BonusCard->BonusCardType->find('list');
		$products = $this->BonusCard->Product->find('list',array('order'=>array('order'=>'asc')));
		$this->set(compact('fiscalYears', 'bonusCardTypes', 'products'));
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
		$this->BonusCard->id = $id;
		if (!$this->BonusCard->exists()) {
			throw new NotFoundException(__('Invalid bonus card'));
		}
		if ($this->BonusCard->delete()) {
			$this->Session->setFlash(__('Bonus card deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Bonus card was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
